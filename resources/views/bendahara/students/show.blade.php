@extends('layouts.app')

@section('title', 'Detail Siswa - Bendahara')
@section('header_title', 'Profil & Histori Tagihan Siswa')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Info Siswa -->
    <div class="card">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-light), var(--primary)); color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; margin: 0 auto 12px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
            </div>
            <h3 style="font-size: 17px; font-weight: 700;">{{ $student->nama_lengkap }}</h3>
            <span class="badge badge-info">{{ $student->kelas->nama_kelas ?? '-' }}</span>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; font-size: 13.5px;">
            <div style="margin-bottom: 10px;">
                <span class="text-muted">NISN:</span> <strong>{{ $student->nisn }}</strong>
            </div>
            <div style="margin-bottom: 10px;">
                <span class="text-muted">NIS:</span> <strong>{{ $student->nis }}</strong>
            </div>
            <div style="margin-bottom: 10px;">
                <span class="text-muted">Username Login:</span> <code>{{ $student->user->username ?? '-' }}</code>
            </div>
            <div style="margin-bottom: 10px;">
                <span class="text-muted">No. HP Ortu:</span> {{ $student->no_telepon_ortu ?? '-' }}
            </div>
            <div style="margin-bottom: 10px;">
                <span class="text-muted">Alamat:</span> {{ $student->alamat ?? '-' }}
            </div>
            <div style="margin-bottom: 10px;">
                <span class="text-muted">Total Tunggakan:</span>
                <strong style="color: var(--danger); font-size: 15px;">Rp {{ number_format($student->total_tunggakan, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="{{ route('bendahara.pembayaran.index', ['nisn_or_nis' => $student->nisn]) }}" class="btn btn-success" style="width: 100%;">
                <i class="fa-solid fa-cash-register"></i> Buka Loket Kasir
            </a>
        </div>
    </div>

    <!-- Tagihan dan Riwayat -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Daftar Semua Tagihan
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pos Pembayaran</th>
                            <th>Periode</th>
                            <th>Tagihan</th>
                            <th>Terbayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->bills as $b)
                            <tr>
                                <td>{{ $b->tarifPembayaran->posPembayaran->nama_pos }}</td>
                                <td>{{ $b->bulan ? $b->nama_bulan . ' ' . $b->tahun : 'Tahun ' . $b->tahun }}</td>
                                <td>Rp {{ number_format($b->nominal_tagihan, 0, ',', '.') }}</td>
                                <td style="color: var(--success); font-weight: 600;">Rp {{ number_format($b->nominal_terbayar, 0, ',', '.') }}</td>
                                <td>
                                    @if($b->status === 'lunas')
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif($b->status === 'sebagian')
                                        <span class="badge badge-warning">Sebagian</span>
                                    @else
                                        <span class="badge badge-danger">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada tagihan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-clock-rotate-left text-info"></i> Riwayat Pembayaran
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Trx</th>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Kuitansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->transactions as $trx)
                            <tr>
                                <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                                <td>{{ $trx->tanggal_bayar->format('d/m/Y H:i') }}</td>
                                <td style="font-weight: 700;">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                                <td><span class="badge badge-info">{{ ucfirst($trx->metode_pembayaran) }}</span></td>
                                <td>
                                    @if($trx->status_verifikasi === 'diverifikasi')
                                        <span class="badge badge-success">Valid</span>
                                    @elseif($trx->status_verifikasi === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($trx->status_verifikasi === 'diverifikasi')
                                        <a href="{{ route('bendahara.kuitansi.cetak', $trx->id) }}" target="_blank" class="btn btn-outline btn-sm">
                                            <i class="fa-solid fa-print"></i> Cetak
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada riwayat transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
