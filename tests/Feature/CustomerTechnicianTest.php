<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerTechnicianTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
        $customer = Customer::factory()->create();
        $technician = Technician::factory()->create();
        Service::factory()->create(['customer_id' => $customer->id, 'technician_id' => $technician->id]);
    }

    /**
     * Edit data pelanggan
     */
    public function test_update_customer_details(): void
    {
        $customer = Customer::firstOrFail();

        $response = $this->actingAs($this->user)->put("/pelanggan/{$customer->id}", [
            'nama' => 'Nama Baru Pelanggan',
            'phone' => '081234567899',
        ]);

        $response->assertRedirect("/pelanggan?id={$customer->id}");
        $this->assertEquals('Nama Baru Pelanggan', $customer->fresh()->name);
    }

    /**
     * Edit pelanggan dengan data sendiri yang sama tidak memicu error duplikasi
     */
    public function test_update_customer_with_same_details_succeeds(): void
    {
        $customer = Customer::firstOrFail();

        $response = $this->actingAs($this->user)->put("/pelanggan/{$customer->id}", [
            'nama' => $customer->name,
            'phone' => $customer->phone,
        ]);

        $response->assertRedirect("/pelanggan?id={$customer->id}");
        $response->assertSessionHasNoErrors();
    }

    /**
     * Tambah teknisi baru
     */
    public function test_add_technician_successfully(): void
    {
        $response = $this->actingAs($this->user)->post('/teknisi', [
            'name' => 'Teknisi Handal',
            'phone' => '081912345678',
            'email' => 'teknisi.handal@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/teknisi');
        $this->assertDatabaseHas('technicians', [
            'name' => 'Teknisi Handal',
            'phone' => '081912345678',
            'is_active' => true,
        ]);
    }

    /**
     * Teknisi yang memiliki riwayat servis tidak bisa dihapus
     */
    public function test_technician_with_services_cannot_be_deleted(): void
    {
        $tech = Technician::has('services')->firstOrFail();

        $response = $this->actingAs($this->user)->delete("/teknisi/{$tech->id}");

        $response->assertSessionHas('error', 'Teknisi yang sudah punya servis tidak dapat dihapus');
        $this->assertDatabaseHas('technicians', ['id' => $tech->id]);
    }

    /**
     * Pelanggan tidak menyediakan rute hapus demi integritas data servis
     */
    public function test_customer_delete_route_is_not_available(): void
    {
        $customer = Customer::has('services')->firstOrFail();

        $response = $this->actingAs($this->user)->delete("/pelanggan/{$customer->id}");

        $response->assertStatus(405);
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}
