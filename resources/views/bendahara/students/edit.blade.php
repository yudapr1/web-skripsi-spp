@extends('layouts.app')

@section('title', 'Edit Siswa - Bendahara')
@section('header_title', 'Perbarui Data Siswa')

@section('content')
<div class="card" style="max-width: 800px; margin: auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-user-pen text-primary"></i> Edit Data Siswa & Akun
        </div>
        <a href="{{ route('bendahara.students.index') }}" class="btn btn-outline btn-sm">Kembali</a>
    </div>

    <form action="{{ route('bendahara.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            1. Biodata Siswa
        </h4>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">NISN <span class="text-danger">*</span></label>
                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">NIS <span class="text-danger">*</span></label>
                <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Lengkap Siswa <span class="text-danger">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" class="form-select" required>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $student->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Status Siswa <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="aktif" {{ old('status', $student->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lulus" {{ old('status', $student->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="pindah" {{ old('status', $student->status) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">No. Telepon / WhatsApp Orang Tua</label>
            <input type="text" name="no_telepon_ortu" class="form-control" value="{{ old('no_telepon_ortu', $student->no_telepon_ortu) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $student->alamat) }}</textarea>
        </div>

        <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-top: 24px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            2. Akun Login Portal Siswa
        </h4>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Username Login <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $student->user->username ?? '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email ?? '') }}">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('bendahara.students.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
