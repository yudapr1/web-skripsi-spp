@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Kas Masuk - Bendahara')
@section('header_title', 'Laporan Penerimaan Kas Sekolah')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-filter text-primary"></i> Filter Rekapitulasi Pembayaran
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('bendahara.laporan.excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fa-solid fa-file-excel"></i> Export Excel (.xlsx)
            </a>
            <a href="{{ route('bendahara.laporan.cetak', request()->all()) }}" target="_blank" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-print"></i> Cetak / Export PDF
            </a>
        </div>
    </div>

    <form action="{{ route('bendahara.laporan.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Dari Tanggal:</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Sampai Tanggal:</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Filter Kelas:</label>
            <select name="kelas_id" class="form-select">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Metode Pembayaran:</label>
            <select name="metode_pembayaran" class="form-select">
                <option value="">-- Semua Metode --</option>
                <option value="tunai" {{ $metode == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ $metode == 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="height: 42px;">
            <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
        </button>
    </form>
</div>

<!-- Kotak Total Kas Terpilih -->
<div class="card" style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%); color: white; border: none; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h4 style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #bfdbfe; font-weight: 700;">Total Pemasukan Kas Periode Ini</h4>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #ffffff;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        </div>
        <div style="font-size: 13px; color: #dbeafe; background: rgba(255,255,255,0.12); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
            Jumlah Transaksi: <strong style="color: #ffffff;">{{ count($transactions) }} Transaksi</strong>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list text-primary"></i> Rincian Pembayaran Masuk
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Pos Pembayaran</th>
                    <th>Metode</th>
                    <th>Nominal</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $trx)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $trx->nomor_transaksi }}</strong></td>
                        <td>{{ $trx->tanggal_transaksi ? $trx->tanggal_transaksi->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $trx->siswa?->nama_lengkap ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $trx->siswa?->kelas?->nama_kelas ?? '-' }}</span></td>
                        <td>
                            @foreach($trx->details as $d)
                                <div>{{ $d->posPembayaran?->nama_pos }}: <strong>Rp {{ number_format($d->nominal_bayar, 0, ',', '.') }}</strong></div>
                            @endforeach
                        </td>
                        <td>
                            @if($trx->metode_pembayaran === 'tunai')
                                <span class="badge badge-success">Tunai</span>
                            @else
                                <span class="badge badge-purple">Transfer</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: #059669;">Rp {{ number_format($trx->nominal_pokok, 0, ',', '.') }}</td>
                        <td>{{ $trx->verifier?->name ?? 'Bendahara' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada catatan transaksi pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
