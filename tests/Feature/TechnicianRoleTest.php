<?php

namespace Tests\Feature;

use App\Enums\ServiceStatus;
use App\Models\Service;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TechnicianRoleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $techUser;

    protected Technician $technician;

    protected Technician $otherTechnician;

    protected User $otherTechUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@servicelaptop.test'],
            ['name' => 'Admin Toko', 'password' => Hash::make('password'), 'role' => 'admin']
        );
        $this->admin->update(['role' => 'admin']);

        $this->techUser = User::firstOrCreate(
            ['email' => 'tech.test@servicelaptop.test'],
            ['name' => 'Teknisi Test', 'password' => Hash::make('password123'), 'role' => 'technician']
        );
        $this->techUser->update(['role' => 'technician']);

        $this->technician = Technician::firstOrCreate(
            ['name' => 'Teknisi Test'],
            ['user_id' => $this->techUser->id, 'phone' => '081299990001', 'is_active' => true]
        );
        $this->technician->update(['user_id' => $this->techUser->id, 'is_active' => true]);

        $this->otherTechUser = User::firstOrCreate(
            ['email' => 'other.tech@servicelaptop.test'],
            ['name' => 'Teknisi Lain', 'password' => Hash::make('password123'), 'role' => 'technician']
        );
        $this->otherTechUser->update(['role' => 'technician']);

        $this->otherTechnician = Technician::firstOrCreate(
            ['name' => 'Teknisi Lain'],
            ['user_id' => $this->otherTechUser->id, 'phone' => '081299990002', 'is_active' => true]
        );
        $this->otherTechnician->update(['user_id' => $this->otherTechUser->id, 'is_active' => true]);

        // Create services for tests
        Service::factory()->create(['status' => ServiceStatus::Dikerjakan]);
        Service::factory()->create(['status' => ServiceStatus::Dikerjakan]);
        Service::factory()->selesai()->create();
    }

    /**
     * Teknisi bisa login dengan email
     */
    public function test_technician_can_login_with_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'tech.test@servicelaptop.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/servis');
        $this->assertAuthenticatedAs($this->techUser);
    }

    /**
     * Teknisi bisa login dengan nomor HP
     */
    public function test_technician_can_login_with_phone_number(): void
    {
        $response = $this->post('/login', [
            'email' => '0812-9999-0001',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/servis');
        $this->assertAuthenticatedAs($this->techUser);
    }

    /**
     * Teknisi nonaktif tidak bisa login
     */
    public function test_inactive_technician_cannot_login(): void
    {
        $this->technician->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'tech.test@servicelaptop.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Dashboard teknisi hanya menampilkan servis tugasnya
     */
    public function test_technician_dashboard_only_shows_assigned_services(): void
    {
        // Cari atau buat servis untuk teknisi ini dan teknisi lain
        $myService = Service::where('status', ServiceStatus::Dikerjakan->value)->first();
        $myService->update(['technician_id' => $this->technician->id]);

        $otherService = Service::where('status', ServiceStatus::Dikerjakan->value)
            ->where('id', '!=', $myService->id)
            ->first();
        $otherService->update(['technician_id' => $this->otherTechnician->id]);

        $response = $this->actingAs($this->techUser)->get('/servis');

        $response->assertOk();
        $response->assertSee($myService->tracking_no);
        $response->assertDontSee($otherService->tracking_no);
    }

    /**
     * Teknisi dilarang mengakses form penerimaan servis baru
     */
    public function test_technician_cannot_access_new_service_or_store(): void
    {
        $response = $this->actingAs($this->techUser)->get('/servis/baru');
        $response->assertForbidden();

        $responseStore = $this->actingAs($this->techUser)->post('/servis', []);
        $responseStore->assertForbidden();
    }

    /**
     * Teknisi dilarang mengedit data servis
     */
    public function test_technician_cannot_update_service_details(): void
    {
        $service = Service::firstOrFail();

        $response = $this->actingAs($this->techUser)->put("/servis/{$service->tracking_no}", [
            'laptop' => 'Laptop Diubah',
            'keluhan' => 'Keluhan Diubah',
            'teknisi_id' => $this->technician->id,
        ]);

        $response->assertForbidden();
    }

    /**
     * Teknisi dilarang menyerahkan laptop ke pelanggan
     */
    public function test_technician_cannot_handover_laptop(): void
    {
        $service = Service::where('status', ServiceStatus::Selesai->value)->firstOrFail();

        $response = $this->actingAs($this->techUser)->post("/servis/{$service->tracking_no}/serahkan");
        $response->assertForbidden();
    }

    /**
     * Teknisi dilarang mengelola pelanggan dan teknisi
     */
    public function test_technician_cannot_access_management_pages(): void
    {
        $this->actingAs($this->techUser)->get('/pelanggan')->assertForbidden();
        $this->actingAs($this->techUser)->get('/teknisi')->assertForbidden();
        $this->actingAs($this->techUser)->post('/teknisi', [])->assertForbidden();
    }

    /**
     * Teknisi bisa menandai selesai servis miliknya
     */
    public function test_technician_can_finish_assigned_service(): void
    {
        $service = Service::where('status', ServiceStatus::Dikerjakan->value)->firstOrFail();
        $service->update(['technician_id' => $this->technician->id]);

        $response = $this->actingAs($this->techUser)->post("/servis/{$service->tracking_no}/selesai");

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $this->assertEquals(ServiceStatus::Selesai, $service->fresh()->status);
    }

    /**
     * Teknisi dilarang mengubah status servis milik teknisi lain
     */
    public function test_technician_cannot_finish_other_technician_service(): void
    {
        $service = Service::where('status', ServiceStatus::Dikerjakan->value)->firstOrFail();
        $service->update(['technician_id' => $this->otherTechnician->id]);

        $response = $this->actingAs($this->techUser)->post("/servis/{$service->tracking_no}/selesai");

        $response->assertForbidden();
    }

    /**
     * Teknisi bisa membatalkan servis tugasnya dengan alasan
     */
    public function test_technician_can_cancel_assigned_service(): void
    {
        $service = Service::where('status', ServiceStatus::Dikerjakan->value)->firstOrFail();
        $service->update(['technician_id' => $this->technician->id]);

        $response = $this->actingAs($this->techUser)->post("/servis/{$service->tracking_no}/batal", [
            'alasan' => 'Komponen langka tidak ditemukan',
        ]);

        $response->assertRedirect("/servis/{$service->tracking_no}");
        $fresh = $service->fresh();
        $this->assertEquals(ServiceStatus::Dibatalkan, $fresh->status);
        $this->assertEquals(0, $fresh->agreed_cost);
        $this->assertEquals('Komponen langka tidak ditemukan', $fresh->cancel_reason);
    }

    /**
     * Teknisi bisa ubah profil (nama, no HP, dan password)
     */
    public function test_technician_can_update_profile(): void
    {
        $response = $this->actingAs($this->techUser)->put('/profil', [
            'name' => 'Nama Baru Teknisi',
            'phone' => '081299990099',
            'current_password' => 'password123',
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertRedirect('/profil');
        $response->assertSessionHas('success', 'Profile diperbarui');

        $this->assertEquals('Nama Baru Teknisi', $this->techUser->fresh()->name);
        $this->assertEquals('081299990099', $this->technician->fresh()->phone);
        $this->assertTrue(Hash::check('passwordBaru123', $this->techUser->fresh()->password));
    }

    /**
     * Admin bisa membuat teknisi dengan email dan password kustom
     */
    public function test_admin_can_create_technician_with_password_and_email(): void
    {
        $response = $this->actingAs($this->admin)->post('/teknisi', [
            'name' => 'Teknisi Kustom',
            'phone' => '081277778888',
            'email' => 'kustom@servicelaptop.test',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/teknisi');
        $user = User::where('email', 'kustom@servicelaptop.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret12345', $user->password));
        $this->assertEquals('technician', $user->role);
    }

    /**
     * Admin gagal buat teknisi tanpa kata sandi
     */
    public function test_admin_cannot_create_technician_without_password(): void
    {
        $response = $this->actingAs($this->admin)->post('/teknisi', [
            'name' => 'Teknisi Tanpa Sandi',
            'phone' => '081277779999',
            'email' => 'tanpasandi@servicelaptop.test',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Admin bisa mereset password teknisi dari modal ubah
     */
    public function test_admin_can_update_technician_password(): void
    {
        $response = $this->actingAs($this->admin)->put("/teknisi/{$this->technician->id}", [
            'name' => $this->technician->name,
            'phone' => $this->technician->phone,
            'email' => $this->techUser->email,
            'is_active' => '1',
            'password' => 'newSecret999',
        ]);

        $response->assertRedirect('/teknisi');
        $this->assertTrue(Hash::check('newSecret999', $this->techUser->fresh()->password));
    }
}
