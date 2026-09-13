@extends('layouts.app')

@section('title', 'Tambah Siswa Baru - Bendahara')
@section('header_title', 'Pendaftaran Siswa & Akun Portal')

@section('content')
<div class="card" style="max-width: 800px; margin: auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-user-plus text-primary"></i> Formulir Data Siswa & Akun Login
        </div>
        <a href="{{ route('bendahara.students.index') }}" class="btn btn-outline btn-sm">Kembali</a>
    </div>

    <form action="{{ route('bendahara.students.store') }}" method="POST">
        @csrf

        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            1. Biodata Siswa
        </h4>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Nomor Induk Siswa Nasional (NISN) <span class="text-danger">*</span></label>
                <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required placeholder="Contoh: 0054891234">
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required placeholder="Contoh: 20261001">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Lengkap Siswa <span class="text-danger">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required placeholder="Nama lengkap sesuai ijazah/akta">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">No. Telepon / WhatsApp Orang Tua</label>
            <input type="text" name="no_telepon_ortu" class="form-control" value="{{ old('no_telepon_ortu') }}" placeholder="08xxxxxxxxxx">
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat domisili siswa">{{ old('alamat') }}</textarea>
        </div>

        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-top: 24px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            2. Akun Login Portal Siswa
        </h4>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Username Login <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Contoh: ahmad / NISN">
            </div>

            <div class="form-group">
                <label class="form-label">Kata Sandi Default <span class="text-danger">*</span></label>
                <input type="text" name="password" class="form-control" value="{{ old('password', 'password123') }}" required placeholder="Minimal 6 karakter">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Email (Opsional)</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="siswa@sekolah.sch.id">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('bendahara.students.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Data Siswa
            </button>
        </div>
    </form>
</div>
@endsection
