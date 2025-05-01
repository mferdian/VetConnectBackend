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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_date_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_time_id')->constrained()->cascadeOnDelete();
            $table->string('keluhan')->nullable();
            $table->unsignedBigInteger('total_harga');
            $table->enum('status',['confirmed','pending','canceled']);
            $table->enum('status_bayar', ['berhasil','gagal','pending']);
            $table->enum('metode_pembayaran', ['transfer_bank', 'e-wallet', 'cash', 'lainnya']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
