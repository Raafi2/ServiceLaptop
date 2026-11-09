<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Technician $activeTech;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $this->activeTech = Technician::where('is_active', true)->first() ?? Technician::factory()->create(['is_active' => true]);
        $customer = Customer::factory()->create();
        Service::factory()->create(['customer_id' => $customer->id, 'technician_id' => $this->activeTech->id]);
    }

    /**
     * Uji autentikasi staf toko
     */
    public function test_guest_cannot_access_services(): void
    {
        $response = $this->get('/servis');
        $response->assertRedirect('/login');
    }

    public function test_login_with_correct_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@servicelaptop.test',
            'password' => 'password',
        ]);
        $response->assertRedirect('/servis');
        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@servicelaptop.test',
            'password' => 'wrongpassword',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Email atau kata sandi salah']);
    }

    public function test_login_rate_limiting(): void
    {
        RateLimiter::clear('login:127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'admin@servicelaptop.test',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@servicelaptop.test',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan, coba sebentar lagi.']);
        RateLimiter::clear('login:127.0.0.1');
    }

    /**
     * Penerimaan servis baru lengkap dan valid
     */
    public function test_receive_service_successfully(): void
    {
        Storage::fake('local');

        $photo = UploadedFile::fake()->image('laptop.jpg', 600, 400)->size(1500); // 1.5MB

        $response = $this->actingAs($this->user)->post('/servis', [
            'nama' => 'Testing Pelanggan',
            'hp' => '081299887766',
            'laptop' => 'Asus ROG Zephyrus G14',
            'keluhan' => 'Keyboard mati total',
            'biaya' => '450.000',
            'teknisi_id' => $this->activeTech->id,
            'foto' => $photo,
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Testing Pelanggan',
            'phone' => '081299887766',
        ]);

        $service = Service::where('laptop', 'Asus ROG Zephyrus G14')->first();
        $this->assertNotNull($service);
        $this->assertEquals(ServiceStatus::Dikerjakan, $service->status);
        $this->assertEquals(450000, $service->agreed_cost);
        $this->assertStringStartsWith('SL-', $service->tracking_no);

        // Cek log awal
        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Dikerjakan',
            'status' => 'dikerjakan',
        ]);

        $response->assertRedirect('/servis/'.$service->tracking_no);
        $response->assertSessionHas('success');
    }

    /**
     * Biaya 0 atau negatif ditolak
     */
    public function test_cannot_receive_with_zero_or_negative_cost(): void
    {
        $photo = UploadedFile::fake()->image('laptop.jpg');

        $response = $this->actingAs($this->user)->from('/servis/baru')->post('/servis', [
            'nama' => 'Pelanggan Error',
            'hp' => '081299887766',
            'laptop' => 'Asus VivoBook',
            'keluhan' => 'Mati',
            'biaya' => '0',
            'teknisi_id' => $this->activeTech->id,
            'foto' => $photo,
        ]);

        $response->assertRedirect('/servis/baru');
        $response->assertSessionHasErrors('biaya');
    }

    /**
     * Servis baru langsung status dikerjakan
     */
    public function test_new_service_has_dikerjakan_status(): void
    {
        Storage::fake('local');

        $photo = UploadedFile::fake()->image('laptop.jpg', 600, 400)->size(1500);

        $response = $this->actingAs($this->user)->post('/servis', [
            'nama' => 'Testing Pelanggan',
            'hp' => '081299887766',
            'laptop' => 'Asus ROG Zephyrus G14',
            'keluhan' => 'Keyboard mati total',
            'biaya' => '450.000',
            'teknisi_id' => $this->activeTech->id,
            'foto' => $photo,
        ]);

        $service = Service::where('laptop', 'Asus ROG Zephyrus G14')->first();
        $this->assertNotNull($service);
        $this->assertEquals(ServiceStatus::Dikerjakan, $service->status);

        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Dikerjakan',
            'status' => 'dikerjakan',
        ]);
    }

    /**
     * Tandai servis selesai
     */
    public function test_mark_finished_from_dikerjakan(): void
    {
        $technician = Technician::factory()->create(['is_active' => true]);
        $service = Service::factory()->create(['technician_id' => $technician->id, 'status' => ServiceStatus::Dikerjakan]);

        // Use technician user to mark finished
        $user = $technician->user;
        $response = $this->actingAs($user)->post("/servis/{$service->tracking_no}/selesai");

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $service->refresh();
        $this->assertEquals(ServiceStatus::Selesai, $service->status);

        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Selesai',
            'status' => 'selesai',
        ]);
    }

    /**
     * Serahkan laptop ke pelanggan
     */
    public function test_handover_laptop_from_selesai(): void
    {
        $service = Service::factory()->selesai()->create();

        $response = $this->actingAs($this->user)->post("/servis/{$service->tracking_no}/serahkan");

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $service->refresh();
        $this->assertEquals(ServiceStatus::Serahkan, $service->status);

        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Diserahkan',
            'status' => 'serahkan',
        ]);
    }

    /**
     * Servis yang sudah diambil tidak bisa diserahkan dua kali
     */
    public function test_cannot_handover_twice(): void
    {
        $service = Service::factory()->diambil()->create();

        $response = $this->actingAs($this->user)->post("/servis/{$service->tracking_no}/serahkan");

        $response->assertSessionHas('error', 'Servis ini sudah diserahkan');
    }

    /**
     * Batalkan servis yang berstatus dikerjakan
     */
    public function test_cancel_service_with_reason(): void
    {
        $technician = Technician::factory()->create(['is_active' => true]);
        $service = Service::factory()->create(['technician_id' => $technician->id, 'status' => ServiceStatus::Dikerjakan]);

        // Use technician user to cancel
        $user = $technician->user;
        $response = $this->actingAs($user)->post("/servis/{$service->tracking_no}/batal", [
            'alasan' => 'Motherboard terbakar',
        ]);

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $service->refresh();
        $this->assertEquals(ServiceStatus::Dibatalkan, $service->status);
        $this->assertEquals(0, $service->agreed_cost);
        $this->assertEquals('Motherboard terbakar', $service->cancel_reason);

        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Dibatalkan',
            'status' => 'dibatalkan',
        ]);
    }

    /**
     * Ubah keluhan mencatat riwayat log
     */
    public function test_update_details_creates_log(): void
    {
        $service = Service::factory()->create(['status' => ServiceStatus::Dikerjakan]);

        $response = $this->actingAs($this->user)->put("/servis/{$service->tracking_no}", [
            'laptop' => $service->laptop,
            'keluhan' => 'Keluhan baru yang diupdate',
            'teknisi_id' => $service->technician_id,
        ]);

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $service->refresh();
        $this->assertEquals('Keluhan baru yang diupdate', $service->complaint);

        $this->assertDatabaseHas('service_logs', [
            'service_id' => $service->id,
            'event' => 'Data diubah',
        ]);
    }

    /**
     * Ubah teknisi servis menolak teknisi nonaktif lain
     */
    public function test_update_service_rejects_inactive_technician(): void
    {
        $service = Service::factory()->create(['status' => ServiceStatus::Dikerjakan]);
        $inactiveTech = Technician::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->user)->put("/servis/{$service->tracking_no}", [
            'laptop' => $service->laptop,
            'keluhan' => $service->complaint,
            'teknisi_id' => $inactiveTech->id,
        ]);

        $response->assertSessionHasErrors(['teknisi_id' => 'Pilih Teknisi penanggung jawab yang aktif']);
    }

    public function test_service_detail_actions(): void
    {
        $service = Service::factory()->create(['status' => ServiceStatus::Diterima]);

        $response = $this->actingAs($this->user)->get('/servis/'.$service->tracking_no);
        $response->assertOk();
        $response->assertSee('Unduh ticket service');
        $response->assertSee('Salin nomor');
        $response->assertDontSee('Salin tautan cek status');
    }

    /**
     * Ganti kata sandi admin dengan kata sandi lama yang benar
     */
    public function test_admin_can_update_password(): void
    {
        $response = $this->actingAs($this->user)->put('/profil/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kata sandi berhasil diperbarui');
        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /**
     * Ganti kata sandi gagal bila kata sandi saat ini salah
     */
    public function test_cannot_update_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user)->put('/profil/password', [
            'current_password' => 'wrongcurrentpw',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * Filter daftar servis berdasarkan customer_id eksplisit
     */
    public function test_filter_services_by_customer_id(): void
    {
        $service = Service::with('customer')->firstOrFail();
        $targetCustomer = $service->customer;

        $response = $this->actingAs($this->user)->get('/servis?customer_id='.$targetCustomer->id.'&filter=Semua');

        $response->assertOk();
        $response->assertViewHas('services');
        $services = $response->viewData('services');
        foreach ($services as $s) {
            $this->assertEquals($targetCustomer->id, $s->customer_id);
        }
    }

    /**
     * Filter daftar servis berdasarkan technician_id eksplisit
     */
    public function test_filter_services_by_technician_id(): void
    {
        $tech = Technician::has('services')->firstOrFail();

        $response = $this->actingAs($this->user)->get('/servis?technician_id='.$tech->id.'&filter=Semua');

        $response->assertOk();
        $response->assertViewHas('services');
        $services = $response->viewData('services');
        foreach ($services as $s) {
            $this->assertEquals($tech->id, $s->technician_id);
        }
    }

    /**
     * Akses foto dengan path traversal ditolak (403)
     */
    public function test_photo_route_blocks_path_traversal(): void
    {
        $response = $this->actingAs($this->user)->get('/foto/../services/sample_placeholder.jpg');

        $response->assertStatus(403);
    }

    /**
     * Audit dan testing alur lengkap end-to-end dari intake sampai penyerahan
     */
    public function test_complete_service_lifecycle_end_to_end(): void
    {
        Storage::fake('local');

        // 1. Admin login & penerimaan servis baru
        $photo = UploadedFile::fake()->image('laptop.jpg', 600, 400)->size(1500);
        $intakeResponse = $this->actingAs($this->user)->post('/servis', [
            'nama' => 'Budi Santoso',
            'hp' => '081234567890',
            'laptop' => 'ThinkPad T480',
            'keluhan' => 'Baterai drop cepat habis',
            'biaya' => '550.000',
            'teknisi_id' => $this->activeTech->id,
            'foto' => $photo,
        ]);

        $intakeResponse->assertSessionHas('success');
        $service = Service::where('laptop', 'ThinkPad T480')->first();
        $this->assertNotNull($service);
        $trackingNo = $service->tracking_no;
        $this->assertEquals(ServiceStatus::Dikerjakan, $service->status);

        // 2. Pelanggan cek status pertama kali (Sedang dikerjakan)
        $publicCheck1 = $this->get('/cek/'.strtolower($trackingNo));
        $publicCheck1->assertOk();
        $publicCheck1->assertSee('Dikerjakan');
        $publicCheck1->assertSee('B*** S******');
        $publicCheck1->assertDontSee('550.000');
        $publicCheck1->assertDontSee('081234567890');

        // 3. Teknisi login & tandai selesai
        $techUser = $this->activeTech->user;
        $finishResponse = $this->actingAs($techUser)->post("/servis/{$trackingNo}/selesai");
        $finishResponse->assertSessionHas('success');
        $service->refresh();
        $this->assertEquals(ServiceStatus::Selesai, $service->status);
        $this->assertNotNull($service->finished_at);
        $this->assertEquals($techUser->id, $service->finished_by);

        // 4. Pelanggan cek status kedua kali (Selesai & siap diambil)
        $publicCheck2 = $this->get('/cek/'.$trackingNo);
        $publicCheck2->assertOk();
        $publicCheck2->assertSee('Selesai');
        $publicCheck2->assertSee('Laptop sudah selesai dan siap diambil di toko.');

        // 5. Admin serahkan laptop ke pelanggan
        $handoverResponse = $this->actingAs($this->user)->post("/servis/{$trackingNo}/serahkan");
        $handoverResponse->assertSessionHas('success');
        $service->refresh();
        $this->assertEquals(ServiceStatus::Serahkan, $service->status);
        $this->assertNotNull($service->handed_over_at);
        $this->assertEquals($this->user->id, $service->handed_over_by);

        // 6. Pelanggan cek status ketiga kali (Diserahkan)
        $publicCheck3 = $this->get('/cek/'.$trackingNo);
        $publicCheck3->assertOk();
        $publicCheck3->assertSee('Diserahkan');
        $publicCheck3->assertSee('Laptop sudah diambil pelanggan.');

        // 7. Cek kronologi audit logs
        $logs = $service->logs()->orderBy('created_at')->get();
        $this->assertCount(3, $logs);
        $this->assertEquals('Dikerjakan', $logs[0]->event);
        $this->assertEquals('Selesai', $logs[1]->event);
        $this->assertEquals('Diserahkan', $logs[2]->event);
    }

    public function test_admin_can_backup_data_to_excel_csv(): void
    {
        $response = $this->actingAs($this->user)->get('/servis/backup');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('No Tiket', $content);
        $this->assertStringContainsString('Keluhan', $content);
    }

    public function test_technician_cannot_backup_data(): void
    {
        $techUser = User::factory()->create([
            'email' => 'tech.backup@servicelaptop.test',
            'role' => 'technician',
        ]);

        $response = $this->actingAs($techUser)->get('/servis/backup');
        $response->assertForbidden();
    }
}
