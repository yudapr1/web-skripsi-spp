@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran Transfer - Bendahara')
@section('header_title', 'Verifikasi Bukti Transfer Siswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-receipt text-primary"></i> Daftar Bukti Transfer yang Diunggah Siswa
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Trx</th>
                    <th>Waktu Upload</th>
                    <th>Nama Siswa & Kelas</th>
                    <th>Tagihan / Pos</th>
                    <th>Nominal</th>
                    <th>Bukti Transfer</th>
                    <th>Status</th>
                    <th>Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $trx)
                    <tr>
                        <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                        <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <strong>{{ $trx->student->nama_lengkap }}</strong><br>
                            <small class="text-muted">NISN: {{ $trx->student->nisn }} | Kelas: {{ $trx->student->kelas->nama_kelas ?? '-' }}</small>
                        </td>
                        <td>
                            {{ $trx->bill->tarifPembayaran->posPembayaran->nama_pos }}
                            @if($trx->bill->bulan)
                                ({{ $trx->bill->nama_bulan }} {{ $trx->bill->tahun }})
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--primary);">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->bukti_transfer)
                                <a href="{{ asset('storage/' . $trx->bukti_transfer) }}" target="_blank" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-image"></i> Lihat Foto Bukti
                                </a>
                            @else
                                <span class="text-muted">Tidak ada lampiran</span>
                            @endif
                        </td>
                        <td>
                            @if($trx->status_verifikasi === 'diverifikasi')
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Diverifikasi</span>
                            @elseif($trx->status_verifikasi === 'pending')
                                <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                            @else
                                <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Ditolak</span>
                            @endif
                        </td>
                        <td>
                            @if($trx->status_verifikasi === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form action="{{ route('bendahara.pembayaran.proses_verifikasi', $trx->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui transaksi ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="diverifikasi">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>
                                    </form>

                                    <form action="{{ route('bendahara.pembayaran.proses_verifikasi', $trx->id) }}" method="POST" onsubmit="return confirm('Tolak transfer ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 12px; color: var(--text-muted);">Telah diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada transaksi transfer yang perlu diverifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $transfers->links() }}
    </div>
</div>
@endsection
