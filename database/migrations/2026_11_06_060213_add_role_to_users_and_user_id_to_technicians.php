<?php

use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('password');
        });

        Schema::table('technicians', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // Hubungkan akun teknisi yang sudah ada
        $technicians = Technician::whereNull('user_id')->get();
        foreach ($technicians as $tech) {
            $baseSlug = Str::slug($tech->name, '.');
            if (empty($baseSlug)) {
                $baseSlug = 'teknisi.'.$tech->id;
            }
            $email = $baseSlug.'@servicelaptop.test';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $baseSlug.$counter.'@servicelaptop.test';
                $counter++;
            }

            $user = User::create([
                'name' => $tech->name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]);

            $tech->update(['user_id' => $user->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
