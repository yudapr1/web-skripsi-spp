@extends('layouts.app')

@section('title', 'Laporan Tunggakan Siswa - Bendahara')
@section('header_title', 'Rekapitulasi Tunggakan & Piutang Siswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-filter text-primary"></i> Filter Berdasarkan Kelas
        </div>
    </div>

    <form action="{{ route('bendahara.laporan.tunggakan') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tahun Ajaran:</label>
            <input type="text" name="tahun_ajaran" class="form-control" value="{{ $tahunAjaran ?? '' }}" placeholder="Contoh: 2026/2027">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Pilih Kelas:</label>
            <select name="kelas_id" class="form-select">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="height: 42px;">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
        </button>
    </form>
</div>

<!-- Kotak Total Tunggakan -->
<div class="card" style="background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%); color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; color: #fee2e2;">Total Tunggakan Belum Terbayar</h4>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
        </div>
        <div style="font-size: 13px; color: #fee2e2;">
            Jumlah Tagihan: <strong>{{ count($tagihans) }} Siswa</strong>
        </div>
    </div>
</div>

<!-- Tabel Rincian Tunggakan -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-triangle-exclamation text-danger"></i> Daftar Tagihan Tahunan Tertunggak
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Tagihan</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Total Tagihan</th>
                    <th>Terbayar</th>
                    <th>Sisa Tunggakan</th>
                    <th>Rincian Pos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihans as $index => $t)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $t->nomor_tagihan }}</strong></td>
                        <td>{{ $t->siswa?->nisn ?? '-' }}</td>
                        <td><strong>{{ $t->siswa?->nama_lengkap ?? '-' }}</strong></td>
                        <td><span class="badge badge-info">{{ $t->siswa?->kelas?->nama_kelas ?? '-' }}</span></td>
                        <td>{{ $t->tahun_ajaran }}</td>
                        <td>Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}</td>
                        <td style="color: var(--success); font-weight: 600;">Rp {{ number_format($t->total_terbayar, 0, ',', '.') }}</td>
                        <td style="color: var(--danger); font-weight: 800;">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td>
                            @foreach($t->items as $item)
                                @if($item->sisa_pos > 0)
                                    <div style="font-size: 11px;">
                                        {{ $item->posPembayaran?->nama_pos }}: <span style="color: #dc2626;">Rp {{ number_format($item->sisa_pos, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada tunggakan ditemukan! Semua tagihan telah lunas. 🎉</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
