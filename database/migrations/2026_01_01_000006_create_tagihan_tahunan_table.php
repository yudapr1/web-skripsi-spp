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
        Schema::create('tagihan_tahunan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan', 50)->unique(); // Misal: TAG-2026-X-0012
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('tahun_ajaran', 20); // Contoh: "2025/2026"
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('total_terbayar', 12, 2)->default(0.00);
            $table->decimal('sisa_tagihan', 12, 2);
            $table->enum('status', ['belum_lunas', 'sebagian', 'lunas'])->default('belum_lunas');
            $table->timestamps();
        });

        Schema::create('tagihan_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_tahunan_id')->constrained('tagihan_tahunan')->onDelete('cascade');
            $table->foreignId('pos_pembayaran_id')->constrained('pos_pembayaran')->onDelete('restrict');
            $table->decimal('nominal_pos', 12, 2);
            $table->decimal('nominal_terbayar', 12, 2)->default(0.00);
            $table->decimal('sisa_pos', 12, 2);
            $table->enum('status', ['belum_lunas', 'sebagian', 'lunas'])->default('belum_lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_item');
        Schema::dropIfExists('tagihan_tahunan');
    }
};
