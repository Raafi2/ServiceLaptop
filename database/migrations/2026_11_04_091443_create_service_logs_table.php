<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('event', 30);
            $table->string('status', 12);
            $table->string('note', 200)->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['service_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_logs');
    }
};
