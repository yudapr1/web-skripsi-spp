<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kwitansi extends Model
{
    use HasFactory;

    protected $table = 'kwitansi';

    protected $fillable = [
        'transaksi_pembayaran_id',
        'nomor_kwitansi',
        'tanggal_terbit',
        'diterbitkan_oleh',
        'file_path_pdf',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'datetime',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPembayaran::class, 'transaksi_pembayaran_id');
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}
