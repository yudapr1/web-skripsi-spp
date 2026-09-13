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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('nisn', 20)->unique();
            $table->string('nis', 20)->unique();
            $table->string('nama_lengkap', 255);
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir');
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('nomor_telepon_wali', 20)->nullable();
            $table->enum('status_siswa', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->string('foto', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
