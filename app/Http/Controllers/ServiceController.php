<?php

namespace App\Http\Controllers;

use App\Enums\ServiceStatus;
use App\Http\Requests\CancelServiceRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\Technician;
use App\Services\ServiceWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceWorkflow $workflow
    ) {}

    public function index(Request $request): View
    {
        $q = $request->query('q');
        $customerId = $request->query('customer_id');
        $technicianId = $request->query('technician_id');
        $filter = $request->query('filter', (filled($q) || $customerId || $technicianId) ? 'Semua' : 'Aktif');

        $user = $request->user();

        if ($user->isTechnician()) {
            $techId = $user->technician?->id;
            $base = Service::where('technician_id', $techId);
            $counts = [
                'Aktif' => (clone $base)->whereIn('status', [ServiceStatus::Diterima->value, ServiceStatus::Diagnosa->value, ServiceStatus::Dikerjakan->value, ServiceStatus::Selesai->value])->count(),
                'Semua' => (clone $base)->count(),
                'Dikerjakan' => (clone $base)->where('status', ServiceStatus::Dikerjakan->value)->count(),
                'Selesai' => (clone $base)->where('status', ServiceStatus::Selesai->value)->count(),
                'Diserahkan' => (clone $base)->where('status', ServiceStatus::Serahkan->value)->count(),
                'Dibatalkan' => (clone $base)->where('status', ServiceStatus::Dibatalkan->value)->count(),
            ];

            $servicesQuery = Service::with(['customer', 'technician'])
                ->where('technician_id', $techId)
                ->statusFilter($filter)
                ->search($q)
                ->orderByDesc('received_at');
        } else {
            $counts = [
                'Aktif' => Service::whereIn('status', [ServiceStatus::Diterima->value, ServiceStatus::Diagnosa->value, ServiceStatus::Dikerjakan->value, ServiceStatus::Selesai->value])->count(),
                'Semua' => Service::count(),
                'Dikerjakan' => Service::where('status', ServiceStatus::Dikerjakan->value)->count(),
                'Selesai' => Service::where('status', ServiceStatus::Selesai->value)->count(),
                'Diserahkan' => Service::where('status', ServiceStatus::Serahkan->value)->count(),
                'Dibatalkan' => Service::where('status', ServiceStatus::Dibatalkan->value)->count(),
            ];

            $servicesQuery = Service::with(['customer', 'technician'])
                ->statusFilter($filter)
                ->orderByDesc('received_at');

            if ($customerId) {
                $servicesQuery->where('customer_id', $customerId);
            } elseif ($technicianId) {
                $servicesQuery->where('technician_id', $technicianId);
            } else {
                $servicesQuery->search($q);
            }
        }

        $services = $servicesQuery->paginate(10)->withQueryString();

        return view('services.index', [
            'services' => $services,
            'q' => $q,
            'filter' => $filter,
            'counts' => $counts,
            'totalActive' => $counts['Aktif'],
        ]);
    }

    public function exportBackup(Request $request): StreamedResponse
    {
        $services = Service::with(['customer', 'technician'])
            ->orderByDesc('received_at')
            ->get();

        $filename = 'rekap-servis-laptop-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($services) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM untuk Microsoft Excel

            fputcsv($handle, ['No', 'No Tiket', 'Tanggal Masuk', 'Pelanggan', 'No HP', 'Laptop', 'Keluhan', 'Teknisi', 'Status', 'Tanggal Diserahkan', 'Biaya (Rp)']);

            $no = 1;
            foreach ($services as $s) {
                fputcsv($handle, [
                    $no++,
                    $s->tracking_no,
                    $s->received_at ? $s->received_at->format('d/m/Y') : '-',
                    $s->customer?->name ?? '-',
                    $s->customer?->phone ?? '-',
                    $s->laptop,
                    $s->complaint,
                    $s->technician?->name ?? '-',
                    $s->status->label(),
                    $s->handed_over_at ? $s->handed_over_at->format('d/m/Y') : '-',
                    (int) $s->agreed_cost,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function create(): View
    {
        $technicians = Technician::where('is_active', true)->orderBy('name')->get();

        return view('services.create', [
            'technicians' => $technicians,
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = $this->workflow->receive(
            user: $request->user(),
            customerName: $request->input('nama'),
            customerPhone: $request->input('hp'),
            laptop: $request->input('laptop'),
            complaint: $request->input('keluhan'),
            agreedCost: (int) $request->input('biaya'),
            technicianId: (int) $request->input('teknisi_id'),
            photo: $request->file('foto'),
            photo2: $request->file('foto2')
        );

        return redirect()
            ->route('services.show', $service->tracking_no)
            ->with('success', "Servis tersimpan. Nomor lacak {$service->tracking_no}");
    }

    public function show(Request $request, string $nomor): View
    {
        $service = Service::with([
            'customer',
            'technician',
            'logs.user',
            'receiver',
            'finisher',
            'handler',
            'canceller',
        ])
            ->where('tracking_no', $nomor)
            ->firstOrFail();

        if ($request->user()->isTechnician() && $service->technician_id !== $request->user()->technician?->id) {
            abort(403, 'Akses terbatas untuk teknisi penanggung jawab');
        }

        $technicians = Technician::where('is_active', true)
            ->orWhere('id', $service->technician_id)
            ->orderBy('name')
            ->get();

        return view('services.show', [
            'service' => $service,
            'technicians' => $technicians,
        ]);
    }

    public function update(UpdateServiceRequest $request, string $nomor): RedirectResponse
    {
        $service = Service::where('tracking_no', $nomor)->firstOrFail();

        $this->workflow->updateDetails(
            service: $service,
            user: $request->user(),
            laptop: $request->input('laptop'),
            complaint: $request->input('keluhan'),
            technicianId: (int) $request->input('teknisi_id')
        );

        return redirect()
            ->route('services.show', $service->tracking_no)
            ->with('success', 'Perubahan disimpan');
    }

    public function markFinished(Request $request, string $nomor): RedirectResponse
    {
        $service = Service::where('tracking_no', $nomor)->firstOrFail();

        if ($request->user()->isTechnician()) {
            if ($service->technician_id !== $request->user()->technician?->id) {
                abort(403, 'Akses terbatas untuk teknisi penanggung jawab');
            }
        } else {
            abort(403, 'Akses terbatas untuk teknisi penanggung jawab');
        }

        try {
            $this->workflow->markFinished($service, $request->user());

            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('success', 'Servis ditandai selesai');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('error', $e->getMessage());
        }
    }

    public function handOver(Request $request, string $nomor): RedirectResponse
    {
        $service = Service::where('tracking_no', $nomor)->firstOrFail();

        try {
            $this->workflow->handOver($service, $request->user());

            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('success', 'Laptop diserahkan');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('error', $e->getMessage());
        }
    }

    public function cancel(CancelServiceRequest $request, string $nomor): RedirectResponse
    {
        $service = Service::where('tracking_no', $nomor)->firstOrFail();

        if ($request->user()->isTechnician()) {
            if ($service->technician_id !== $request->user()->technician?->id) {
                abort(403, 'Akses terbatas untuk teknisi penanggung jawab');
            }
        } else {
            abort(403, 'Akses terbatas untuk teknisi penanggung jawab');
        }

        try {
            $this->workflow->cancel($service, $request->user(), $request->input('alasan'));

            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('success', 'Servis dibatalkan');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('services.show', $service->tracking_no)
                ->with('error', $e->getMessage());
        }
    }

    public function photo(string $path): BinaryFileResponse
    {
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(403);
        }

        $cleanPath = 'services/'.ltrim($path, '/');

        if (! Storage::disk('local')->exists($cleanPath)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($cleanPath));
    }
}
