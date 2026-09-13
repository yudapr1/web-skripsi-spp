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
        Schema::create('transaksi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique(); // TRX-20260820-0001
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tagihan_tahunan_id')->constrained('tagihan_tahunan')->onDelete('cascade');
            $table->dateTime('tanggal_transaksi');
            $table->enum('metode_pembayaran', ['tunai', 'transfer']);
            $table->decimal('nominal_pokok', 12, 2);
            $table->integer('kode_unik')->default(0); // 3 digit acak jika transfer
            $table->decimal('total_transfer', 12, 2); // nominal_pokok + kode_unik
            $table->enum('status', ['menunggu_verifikasi', 'terverifikasi', 'ditolak', 'dibatalkan'])->default('menunggu_verifikasi');
            $table->text('catatan_siswa')->nullable();
            $table->text('catatan_bendahara')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_pembayaran_id')->constrained('transaksi_pembayaran')->onDelete('cascade');
            $table->foreignId('tagihan_item_id')->constrained('tagihan_item')->onDelete('cascade');
            $table->foreignId('pos_pembayaran_id')->constrained('pos_pembayaran')->onDelete('restrict');
            $table->decimal('nominal_bayar', 12, 2);
            $table->timestamps();
        });

        Schema::create('bukti_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_pembayaran_id')->constrained('transaksi_pembayaran')->onDelete('cascade');
            $table->string('nama_bank_pengirim', 100)->nullable();
            $table->string('nama_pemilik_rekening', 150)->nullable();
            $table->string('nomor_rekening_pengirim', 50)->nullable();
            $table->string('file_path', 255);
            $table->string('file_type', 50);
            $table->integer('file_size');
            $table->dateTime('uploaded_at');
            $table->timestamps();
        });

        Schema::create('kwitansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_pembayaran_id')->unique()->constrained('transaksi_pembayaran')->onDelete('cascade');
            $table->string('nomor_kwitansi', 50)->unique(); // KWT/2026/08/0001
            $table->dateTime('tanggal_terbit');
            $table->foreignId('diterbitkan_oleh')->constrained('users')->onDelete('restrict');
            $table->string('file_path_pdf', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansi');
        Schema::dropIfExists('bukti_pembayaran');
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi_pembayaran');
    }
};
