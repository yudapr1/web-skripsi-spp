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
        Schema::create('pengaturan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah', 255)->default('SMK Muhammadiyah Sekampung');
            $table->text('alamat_sekolah');
            $table->string('nomor_telepon', 50)->nullable();
            $table->string('email_sekolah', 100)->nullable();
            $table->string('nama_bank', 100); // Contoh: "Bank Syariah Indonesia (BSI)"
            $table->string('nomor_rekening', 100);
            $table->string('atas_nama_rekening', 150);
            $table->string('logo_path', 255)->nullable();
            $table->string('nama_bendahara', 150);
            $table->string('nip_bendahara', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sekolah');
    }
};
