@extends('layouts.app')

@section('title', 'Pos Tarif Pembayaran - Bendahara')
@section('header_title', 'Master Pos Tarif & Iuran Sekolah')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Form Tambah Pos -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-folder-plus text-primary"></i> 1. Tambah Kategori Pos Pembayaran
            </div>
        </div>

        <form action="{{ route('bendahara.pos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Pos Pembayaran <span class="text-danger">*</span></label>
                <input type="text" name="nama_pos" class="form-control" placeholder="Contoh: SPP Bulanan, Uang Gedung, UAS" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tipe Pembayaran <span class="text-danger">*</span></label>
                <select name="tipe" class="form-select" required>
                    <option value="bulanan">Bulanan (Ditagih per bulan: 1-12)</option>
                    <option value="bebas">Bebas / Sekali Bayar (Dapat dicicil atau bayar lunas)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Deskripsi peruntukan dana"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-plus"></i> Simpan Pos
            </button>
        </form>
    </div>

    <!-- Form Tetapkan Nominal Tarif -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-hand-holding-dollar text-success"></i> 2. Tetapkan Nominal Tarif
            </div>
        </div>

        <form action="{{ route('bendahara.tarif.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Pilih Pos Pembayaran <span class="text-danger">*</span></label>
                <select name="pos_pembayaran_id" class="form-select" required>
                    <option value="">-- Pilih Pos --</option>
                    @foreach($posList as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->nama_pos }} ({{ ucfirst($pos->tipe) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Berlaku untuk Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">-- Semua Kelas (Global) --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="2026/2027" required placeholder="Contoh: 2026/2027">
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal Tarif (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control" placeholder="Contoh: 250000" required min="0">
                </div>
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%;">
                <i class="fa-solid fa-check"></i> Tetapkan Tarif
            </button>
        </form>
    </div>
</div>

<!-- Daftar Pos dan Tarif yang Berlaku -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list-check text-primary"></i> Daftar Pos Pembayaran & Tarif Terdaftar
        </div>
        <a href="{{ route('bendahara.tagihan.generate.form') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-bolt"></i> Generate Tagihan Siswa
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Pos Pembayaran</th>
                    <th>Tipe</th>
                    <th>Keterangan</th>
                    <th>Rincian Tarif Berdasarkan Kelas / Tahun</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posList as $p)
                    <tr>
                        <td><strong>{{ $p->nama_pos }}</strong></td>
                        <td>
                            @if($p->tipe === 'bulanan')
                                <span class="badge badge-info">Bulanan</span>
                            @else
                                <span class="badge badge-purple">Bebas</span>
                            @endif
                        </td>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                        <td>
                            @if($p->tarifs->isEmpty())
                                <span class="text-muted">Belum ada tarif ditentukan.</span>
                            @else
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @foreach($p->tarifs as $t)
                                        <div style="font-size: 12.5px; background: #f8fafc; padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border-color);">
                                            <strong>{{ $t->kelas->nama_kelas ?? 'Semua Kelas' }}</strong> (T.A {{ $t->tahun_ajaran }}): 
                                            <span style="color: var(--primary); font-weight: 700;">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada pos pembayaran yang dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
