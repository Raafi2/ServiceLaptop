<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('service_logs')
            ->where('event', 'Diterima')
            ->update([
                'event' => 'Dikerjakan',
                'note' => 'Laptop diterima dan mulai dikerjakan',
            ]);

        DB::table('service_logs')
            ->where('event', 'Ditandai selesai')
            ->update([
                'event' => 'Selesai',
                'note' => 'Laptop selesai dikerjakan',
            ]);
    }

    public function down(): void
    {
        DB::table('service_logs')
            ->where('event', 'Dikerjakan')
            ->update([
                'event' => 'Diterima',
                'note' => 'Servis diterima dan langsung dikerjakan',
            ]);

        DB::table('service_logs')
            ->where('event', 'Selesai')
            ->update([
                'event' => 'Ditandai selesai',
                'note' => 'Laptop selesai dikerjakan',
            ]);
    }
};
