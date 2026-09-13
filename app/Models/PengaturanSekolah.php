<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSekolah extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'alamat_sekolah',
        'nomor_telepon',
        'email_sekolah',
        'nama_bank',
        'nomor_rekening',
        'atas_nama_rekening',
        'logo_path',
        'nama_bendahara',
        'nip_bendahara',
    ];
}
