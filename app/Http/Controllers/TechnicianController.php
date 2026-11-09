<?php

namespace App\Http\Controllers;

use App\Enums\ServiceStatus;
use App\Helpers\CustomerKey;
use App\Http\Requests\StoreTechnicianRequest;
use App\Http\Requests\UpdateTechnicianRequest;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(): View
    {
        $technicians = Technician::with('services')->orderBy('name')->get();

        $stats = $technicians->map(function ($tech) {
            $services = $tech->services;

            return [
                'technician' => $tech,
                'total' => $services->count(),
                'dikerjakan' => $services->where('status', ServiceStatus::Dikerjakan)->count(),
                'selesai' => $services->where('status', ServiceStatus::Selesai)->count(),
                'diserahkan' => $services->where('status', ServiceStatus::Serahkan)->count(),
                'dibatalkan' => $services->where('status', ServiceStatus::Dibatalkan)->count(),
            ];
        });

        $activeCount = $technicians->where('is_active', true)->count();

        return view('technicians.index', [
            'technicians' => $technicians,
            'stats' => $stats,
            'activeCount' => $activeCount,
        ]);
    }

    public function store(StoreTechnicianRequest $request): RedirectResponse
    {
        $name = trim(preg_replace('/\s+/', ' ', (string) $request->input('name')));
        $phone = CustomerKey::normalizePhone((string) $request->input('phone'));
        $email = trim(mb_strtolower((string) $request->input('email')));
        $password = (string) $request->input('password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'technician',
        ]);

        Technician::create([
            'user_id' => $user->id,
            'name' => $name,
            'phone' => $phone,
            'is_active' => true,
        ]);

        return redirect()
            ->route('technicians.index')
            ->with('success', 'Teknisi ditambahkan');
    }

    public function update(UpdateTechnicianRequest $request, int $id): RedirectResponse
    {
        $tech = Technician::findOrFail($id);

        $name = trim(preg_replace('/\s+/', ' ', (string) $request->input('name')));
        $phone = CustomerKey::normalizePhone((string) $request->input('phone'));
        $isActive = (bool) $request->input('is_active');

        $tech->update([
            'name' => $name,
            'phone' => $phone,
            'is_active' => $isActive,
        ]);

        if ($tech->user) {
            $userData = ['name' => $name];
            $inputEmail = trim(mb_strtolower((string) $request->input('email')));
            if ($inputEmail !== $tech->user->email) {
                $userData['email'] = $inputEmail;
            }
            if ($request->filled('password')) {
                $userData['password'] = Hash::make((string) $request->input('password'));
            }
            $tech->user->update($userData);
        }

        return redirect()
            ->route('technicians.index')
            ->with('success', 'Data Teknisi disimpan');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tech = Technician::withCount('services')->findOrFail($id);

        if ($tech->services_count > 0) {
            return back()->with('error', 'Teknisi yang sudah punya servis tidak dapat dihapus');
        }

        $user = $tech->user;
        $tech->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()
            ->route('technicians.index')
            ->with('success', 'Teknisi dihapus');
    }
}
