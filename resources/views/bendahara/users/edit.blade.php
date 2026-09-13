@extends('layouts.app')

@section('title', 'Edit Pengguna - Bendahara')
@section('header_title', 'Edit Pengguna Sistem')

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
                <i class="fa-solid fa-user-pen text-primary"></i> Edit Data Pengguna: <strong>{{ $user->name }}</strong>
            </div>
        </div>

        <form action="{{ route('bendahara.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Username Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Hanya huruf, angka, titik, underscore, & strip</small>
                    @error('username')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Peran / Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>
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
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Dibekukan)</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="status" value="{{ $user->status }}">
                        <small style="color: var(--text-muted); font-size: 11px;">Status akun diri sendiri tidak dapat dinonaktifkan.</small>
                    @endif
                    @error('status')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Bagian Ubah Password (Opsional) -->
            <div style="background: rgba(99, 102, 241, 0.04); border: 1px dashed var(--primary); border-radius: var(--radius-sm); padding: 16px; margin-top: 10px; margin-bottom: 20px;">
                <div style="font-weight: 600; font-size: 13px; color: var(--primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-key"></i> Ubah Password (Kosongkan bila tidak ingin mengubah)
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak diubah">
                        @error('password')
                            <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 18px;">
                <a href="{{ route('bendahara.users.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Perbarui Data Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
