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
        Schema::create('tarif_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_pembayaran_id')->constrained('pos_pembayaran')->onDelete('cascade');
            $table->string('tahun_ajaran', 20); // Contoh: "2025/2026", "2026/2027"
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->decimal('nominal', 12, 2);
            $table->timestamps();

            $table->unique(['pos_pembayaran_id', 'tahun_ajaran', 'tingkat'], 'unique_tarif_tingkat_pos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_pembayaran');
    }
};
