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
        Schema::create('vets', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('no_telp');
            $table->string('alamat');
            $table->string('STR');
            $table->string('SIP');
            $table->unsignedBigInteger('harga');
            $table->boolean('jenis_kelamin');
            $table->string('foto');
            $table->date('tgl_lahir');
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vets');
    }
};
