<?php

namespace Database\Seeders;

use App\Enums\ServiceStatus;
use App\Helpers\CustomerKey;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceLog;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@servicelaptop.test'],
            [
                'name' => 'Arum Ambarsari',
                'password' => Hash::make('admin#1234'),
                'role' => 'admin',
            ]
        );

        $teknisiUser = User::firstOrCreate(
            ['email' => 'teknisi@servicelaptop.com'],
            [
                'name' => 'Haris Adrian',
                'password' => Hash::make('teknisi#1234'),
                'role' => 'technician',
            ]
        );

        $teknisi = Technician::firstOrCreate(
            ['user_id' => $teknisiUser->id],
            [
                'name' => 'Haris Adrian',
                'phone' => '085711758049',
                'is_active' => true,
            ]
        );

        $pelanggan1 = Customer::firstOrCreate(
            ['phone' => '085712345678'],
            [
                'name' => 'Siti Rahma',
                'name_key' => CustomerKey::normalizeName('Siti Rahma'),
            ]
        );

        $defaultPhoto = 'services/sample_placeholder.jpg';
        $defaultThumb = 'services/thumbs/sample_placeholder_thumb.jpg';

        $servis2 = Service::firstOrCreate(
            ['tracking_no' => 'SL-TPD555'],
            [
                'customer_id' => $pelanggan1->id,
                'technician_id' => $teknisi->id,
                'laptop' => 'Dell XPS 13 Plus',
                'complaint' => 'Laptop cepat panas dan kipas berisik',
                'agreed_cost' => 450000,
                'photo_path' => $defaultPhoto,
                'thumb_path' => $defaultThumb,
                'status' => ServiceStatus::Dikerjakan,
                'received_by' => $admin->id,
                'received_at' => now()->subDay(),
            ]
        );

        ServiceLog::firstOrCreate(
            ['service_id' => $servis2->id, 'event' => 'Dikerjakan'],
            [
                'user_id' => $teknisiUser->id,
                'status' => 'dikerjakan',
                'note' => 'Laptop sedang dibersihkan thermal paste dan perbaikan heatsink',
                'created_at' => now()->subDay(),
            ]
        );
    }
}
