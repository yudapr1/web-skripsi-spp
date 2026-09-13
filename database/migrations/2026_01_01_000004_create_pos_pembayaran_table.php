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
        Schema::create('pos_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pos', 50)->unique(); // SPP, DAFTAR_ULANG, SERAGAM, INFAQ, PRAKERIN, UKK, STUDY_TOUR, DANA_KEGIATAN
            $table->string('nama_pos', 255);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_pembayaran');
    }
};
