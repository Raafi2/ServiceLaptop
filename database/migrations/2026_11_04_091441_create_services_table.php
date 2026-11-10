<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no', 11)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('technician_id')->constrained('technicians')->restrictOnDelete();
            $table->string('laptop', 100);
            $table->string('complaint', 500);
            $table->unsignedInteger('agreed_cost');
            $table->string('photo_path', 255);
            $table->string('thumb_path', 255);
            $table->string('status', 12)->default('diterima');

            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('received_at');

            $table->foreignId('finished_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('finished_at')->nullable();

            $table->foreignId('handed_over_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('handed_over_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 200)->nullable();

            $table->timestamps();

            $table->index(['status', 'received_at']);
            $table->index('customer_id');
            $table->index('technician_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
