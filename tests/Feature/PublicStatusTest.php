<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PublicStatusTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Cek status dengan nomor lacak valid
     */
    public function test_check_status_valid_number(): void
    {
        $service = Service::factory()->create(['status' => ServiceStatus::Diagnosa]);

        $response = $this->get('/cek/'.strtolower($service->tracking_no));
        $response->assertOk();
        $response->assertSee(strtoupper($service->tracking_no));
        $response->assertSee($service->laptop);
        $response->assertSee('Beranda');
        $response->assertSee('Cari nomor lain');

        // Pastikan tidak menampilkan biaya, nomor HP asli, atau keluhan rahasia
        $response->assertDontSee('agreed_cost');
        $response->assertDontSee($service->customer->phone);
    }

    /**
     * Halaman publik menampilkan riwayat status dengan tanggal tanpa jam
     */
    public function test_public_status_displays_timeline_with_date_only(): void
    {
        $service = Service::factory()->create(['status' => ServiceStatus::Dikerjakan]);

        $response = $this->get('/cek/'.$service->tracking_no);
        $response->assertOk();

        // Riwayat publik menampilkan pesan status dan tanggal tanpa jam
        $response->assertSee('Dikerjakan');
        $response->assertSee('Laptop sedang dalam pengerjaan teknisi.');
        $response->assertSee($service->received_at->locale('id')->isoFormat('D MMM Y'));

        // Tidak memuat jam (HH:mm)
        $response->assertDontSee($service->received_at->format('H:i'));
    }

    /**
     * Cek status dengan nomor servis tidak ditemukan
     */
    public function test_check_status_invalid_number(): void
    {
        $response = $this->get('/cek/SL-NONEXIST');
        $response->assertOk();
        $response->assertSee('Nomor service tidak ditemukan');
    }

    /**
     * Halaman beranda menampilkan section informasi toko
     */
    public function test_home_page(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee(config('toko.alamat'));
        $response->assertSee(config('toko.whatsapp'));
        $response->assertSee('Keunggulan');
        $response->assertSee('Layanan Perbaikan Laptop');
    }

    /**
     * Rate limiting pada cek status membatasi maksimal 10 request per menit
     */
    public function test_rate_limiting_on_check_status(): void
    {
        RateLimiter::clear('check:127.0.0.1');

        for ($i = 0; $i < 10; $i++) {
            $this->get('/cek/SL-123456');
        }

        $response = $this->get('/cek/SL-123456');
        $response->assertSee('Terlalu banyak permintaan');

        RateLimiter::clear('check:127.0.0.1');
    }
}
