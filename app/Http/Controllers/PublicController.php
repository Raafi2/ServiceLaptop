<?php

namespace App\Http\Controllers;

use App\Helpers\MaskedName;
use App\Helpers\TrackingNumber;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('public.home');
    }

    public function check(Request $request, ?string $nomor = null): View
    {
        $inputNo = $nomor ?? $request->query('c');
        $service = null;
        $notFound = false;
        $maskedCustomerName = '';
        $timeline = [];
        $rateLimitError = null;

        // Rate limiting: 10 attempts per minute per IP
        $throttleKey = 'check:'.$request->ip();

        if (filled($inputNo)) {
            if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $rateLimitError = "Terlalu banyak permintaan. Silakan tunggu {$seconds} detik lagi.";

                return view('public.check', [
                    'trackingNo' => $inputNo,
                    'service' => null,
                    'notFound' => false,
                    'maskedName' => '',
                    'timeline' => [],
                    'rateLimitError' => $rateLimitError,
                ]);
            }

            RateLimiter::hit($throttleKey, 60);

            $normalized = TrackingNumber::normalize($inputNo);

            if (TrackingNumber::isValid($normalized)) {
                $service = Service::with(['customer', 'logs'])
                    ->where('tracking_no', $normalized)
                    ->first();
            }

            if (! $service) {
                $notFound = true;
            } else {
                $maskedCustomerName = MaskedName::mask($service->customer->name);

                $timeline = $service->logs
                    ->filter(fn ($log) => $log->event !== 'Data diubah') // PRD FR-12: jangan tampilkan event "Data diubah"
                    ->filter(fn ($log) => ! in_array($log->event, ['Data diubah', 'Dibatalkan'], true)) // filter juga "Dibatalkan" (pakai keterangan umum)
                    ->map(fn ($log) => (object) [
                        'event' => $log->event,
                        'created_at' => $log->created_at,
                        'note' => $log->note,
                        'changes' => $log->changes,
                    ]);
            }
        }

        return view('public.check', [
            'trackingNo' => $inputNo,
            'service' => $service,
            'notFound' => $notFound,
            'maskedName' => $maskedCustomerName,
            'timeline' => $timeline,
            'rateLimitError' => $rateLimitError,
        ]);
    }
}
