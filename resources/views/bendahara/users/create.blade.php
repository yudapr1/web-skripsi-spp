@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - Bendahara')
@section('header_title', 'Tambah Pengguna Sistem')

@section('content')
<div style="max-width: 750px; margin: auto;">
    <div style="margin-bottom: 16px;">
        <a href="{{ route('bendahara.users.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pengguna
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-user-plus text-primary"></i> Formulir Pengguna Baru
            </div>
        </div>

        <form action="{{ route('bendahara.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Siti Aminah / Admin Loket 2" required>
                @error('name')
                    <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Username Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: bendahara2" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Hanya huruf, angka, titik, underscore, & strip</small>
                    @error('username')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: admin@sekolah.sch.id">
                    @error('email')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Peran / Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->display_name ?? ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status Akun <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Dibekukan)</option>
                    </select>
                    @error('status')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                    @error('password')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 18px;">
                <a href="{{ route('bendahara.users.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan Pengguna Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
