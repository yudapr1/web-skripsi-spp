@extends('layouts.app')

@section('title', 'Generate Tagihan Siswa - Bendahara')
@section('header_title', 'Otomasi Pembuatan Tagihan Siswa')

@section('content')
<div class="card" style="max-width: 700px; margin: auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-wand-magic-sparkles text-primary"></i> Generator Tagihan Massal per Kelas
        </div>
        <a href="{{ route('bendahara.pos.index') }}" class="btn btn-outline btn-sm">Kembali ke Pos Tarif</a>
    </div>

    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 14px; border-radius: 6px; margin-bottom: 20px; font-size: 13.5px; color: #1e40af;">
        <i class="fa-solid fa-info-circle me-1"></i>
        <strong>Informasi:</strong> Fitur ini akan membuat tagihan secara otomatis untuk seluruh siswa aktif di kelas yang dipilih. Untuk pos tipe <strong>Bulanan</strong>, sistem akan membuat 12 invoice (Januari - Desember).
    </div>

    <form action="{{ route('bendahara.tagihan.generate') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Pilih Tarif Pos Pembayaran <span class="text-danger">*</span></label>
            <select name="tarif_pembayaran_id" class="form-select" required>
                <option value="">-- Pilih Pos & Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id }}">
                        {{ $t->posPembayaran->nama_pos }} ({{ ucfirst($t->posPembayaran->tipe) }}) - 
                        Rp {{ number_format($t->nominal, 0, ',', '.') }} 
                        [{{ $t->kelas->nama_kelas ?? 'Semua Kelas' }} - T.A {{ $t->tahun_ajaran }}]
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Terapkan ke Kelas <span class="text-danger">*</span></label>
            <select name="kelas_id" class="form-select" required>
                <option value="">-- Pilih Kelas Target --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->jurusan ?? 'Umum' }})</option>
                @endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Tahun Tagihan <span class="text-danger">*</span></label>
                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required min="2020" max="2050">
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Jatuh Tempo Bulanan <span class="text-danger">*</span></label>
                <input type="number" name="jatuh_tempo_hari" class="form-control" value="10" required min="1" max="28" placeholder="Contoh: Tanggal 10">
                <small class="text-muted">Jatuh tempo setiap tanggal ini per bulan.</small>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('bendahara.pos.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin men-generate tagihan ini untuk semua siswa di kelas tersebut?')">
                <i class="fa-solid fa-play"></i> Generate Sekarang
            </button>
        </div>
    </form>
</div>
@endsection
