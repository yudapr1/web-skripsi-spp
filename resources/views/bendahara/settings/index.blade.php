@extends('layouts.app')

@section('title', 'Pengaturan Sistem & User - Bendahara')
@section('header_title', 'Pengaturan Sistem')

@section('content')
<!-- Navigasi Tab Pengaturan -->
<div style="max-width: 1000px; margin: 0 auto 24px auto;">
    <div style="display: flex; gap: 10px; background: rgba(0,0,0,0.04); padding: 6px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <button type="button" onclick="switchSettingsTab('sekolah')" id="tab-btn-sekolah" class="btn {{ $activeTab === 'sekolah' ? 'btn-primary' : 'btn-outline' }}" style="flex: 1; text-align: center; justify-content: center; font-size: 14px; font-weight: 600; padding: 10px 16px; border: none;">
            <i class="fa-solid fa-school me-2"></i> 1. Profil Instansi & Rekening
        </button>
        <button type="button" onclick="switchSettingsTab('users')" id="tab-btn-users" class="btn {{ $activeTab === 'users' ? 'btn-primary' : 'btn-outline' }}" style="flex: 1; text-align: center; justify-content: center; font-size: 14px; font-weight: 600; padding: 10px 16px; border: none;">
            <i class="fa-solid fa-users-gear me-2"></i> 2. Kelola User / Akun Pengguna
        </button>
    </div>
</div>

<!-- ============================================================== -->
<!-- TAB 1: PROFIL INSTANSI & REKENING PEMBAYARAN                    -->
<!-- ============================================================== -->
<div id="tab-content-sekolah" style="display: {{ $activeTab === 'sekolah' ? 'block' : 'none' }}; max-width: 1000px; margin: auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-school text-primary"></i> Data Lembaga & Rekening Pembayaran
            </div>
        </div>

        <form action="{{ route('bendahara.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                1. Identitas Sekolah (Untuk Kop Laporan & Kuitansi)
            </h4>

            <div class="form-group">
                <label class="form-label">Nama Sekolah / Lembaga <span class="text-danger">*</span></label>
                <input type="text" name="nama_sekolah" class="form-control" value="{{ old('nama_sekolah', $setting->nama_sekolah) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat_sekolah" class="form-control" rows="2">{{ old('alamat_sekolah', $setting->alamat_sekolah) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $setting->no_telepon) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Sekolah</label>
                    <input type="email" name="email_sekolah" class="form-control" value="{{ old('email_sekolah', $setting->email_sekolah) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Kepala Sekolah</label>
                    <input type="text" name="nama_kepala_sekolah" class="form-control" value="{{ old('nama_kepala_sekolah', $setting->nama_kepala_sekolah) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">NIP Kepala Sekolah</label>
                    <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ old('nip_kepala_sekolah', $setting->nip_kepala_sekolah) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Bendahara</label>
                    <input type="text" name="nama_bendahara" class="form-control" value="{{ old('nama_bendahara', $setting->nama_bendahara) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">NIP Bendahara</label>
                    <input type="text" name="nip_bendahara" class="form-control" value="{{ old('nip_bendahara', $setting->nip_bendahara) }}">
                </div>
            </div>

            <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-top: 24px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                2. Rekening Bank Tujuan Transfer Siswa
            </h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank', $setting->nama_bank) }}" placeholder="Contoh: BANK JATENG / BCA">
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Rekening</label>
                    <input type="text" name="nomor_rekening" class="form-control" value="{{ old('nomor_rekening', $setting->nomor_rekening) }}" placeholder="Contoh: 1234567890">
                </div>

                <div class="form-group">
                    <label class="form-label">Atas Nama Rekening</label>
                    <input type="text" name="atas_nama_rekening" class="form-control" value="{{ old('atas_nama_rekening', $setting->atas_nama_rekening) }}" placeholder="Contoh: SMKN 1 INFORMATIKA">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan Pengaturan Sekolah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- TAB 2: KELOLA PENGGUNA / AKUN (USER MANAGEMENT)               -->
<!-- ============================================================== -->
<div id="tab-content-users" style="display: {{ $activeTab === 'users' ? 'block' : 'none' }}; max-width: 1000px; margin: auto;">
    <!-- Statistik Ringkas Pengguna -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
        <div class="stat-card" style="background: var(--bg-card); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Total Pengguna</div>
                <div style="font-size: 22px; font-weight: 700; color: var(--text-dark); margin-top: 2px;">{{ number_format($stats['total']) }}</div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="stat-card" style="background: var(--bg-card); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Bendahara / Admin</div>
                <div style="font-size: 22px; font-weight: 700; color: #8b5cf6; margin-top: 2px;">{{ number_format($stats['bendahara']) }}</div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(139, 92, 246, 0.12); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <div class="stat-card" style="background: var(--bg-card); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Akun Siswa</div>
                <div style="font-size: 22px; font-weight: 700; color: #06b6d4; margin-top: 2px;">{{ number_format($stats['siswa']) }}</div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6, 182, 212, 0.12); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <div class="stat-card" style="background: var(--bg-card); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Akun Aktif</div>
                <div style="font-size: 22px; font-weight: 700; color: #10b981; margin-top: 2px;">{{ number_format($stats['aktif']) }}</div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Card Daftar Pengguna -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div class="card-title">
                <i class="fa-solid fa-users-gear text-primary"></i> Daftar Pengguna Aplikasi
            </div>
            <a href="{{ route('bendahara.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus-circle"></i> Tambah Pengguna Baru
            </a>
        </div>

        <!-- Filter & Search Pengguna -->
        <form action="{{ route('bendahara.settings.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="users">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email..." value="{{ request('search') }}">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <select name="role_id" class="form-select">
                    <option value="">-- Semua Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->display_name ?? ucfirst($r->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 130px;">
                <select name="status" class="form-select">
                    <option value="">-- Status --</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-outline" style="height: 42px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'role_id', 'status']))
                <a href="{{ route('bendahara.settings.index', ['tab' => 'users']) }}" class="btn btn-outline" style="height: 42px; display: inline-flex; align-items: center;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $u->role?->name === 'bendahara' ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #0ea5e9, #06b6d4)' }}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-dark);">
                                            {{ $u->name }}
                                            @if($u->id === auth()->id())
                                                <span style="font-size: 10px; background: rgba(99, 102, 241, 0.15); color: var(--primary); padding: 2px 6px; border-radius: 10px; font-weight: 600; margin-left: 4px;">Akun Anda</span>
                                            @endif
                                        </div>
                                        @if($u->siswa)
                                            <div style="font-size: 11px; color: var(--text-muted);">
                                                <i class="fa-solid fa-school"></i> Siswa: {{ $u->siswa->kelas?->nama_kelas ?? '-' }} (NISN: {{ $u->siswa->nisn }})
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code style="font-size: 12px; background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px;">{{ $u->username }}</code></td>
                            <td>{{ $u->email ?? '-' }}</td>
                            <td>
                                @if($u->role?->name === 'bendahara')
                                    <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.3);">
                                        <i class="fa-solid fa-user-shield"></i> {{ $u->role->display_name ?? 'Bendahara' }}
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(6, 182, 212, 0.15); color: #0891b2; border: 1px solid rgba(6, 182, 212, 0.3);">
                                        <i class="fa-solid fa-user-graduate"></i> {{ $u->role->display_name ?? 'Siswa' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($u->status === 'aktif')
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('bendahara.users.toggle-status', $u->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status user {{ $u->name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline" title="{{ $u->status === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}" style="color: {{ $u->status === 'aktif' ? '#f59e0b' : '#10b981' }};">
                                                <i class="fa-solid {{ $u->status === 'aktif' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('bendahara.users.edit', $u->id) }}" class="btn btn-sm btn-outline" title="Edit Pengguna">
                                        <i class="fa-solid fa-pencil text-primary"></i>
                                    </a>

                                    @if($u->id !== auth()->id() && !$u->siswa)
                                        <form action="{{ route('bendahara.users.destroy', $u->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function switchSettingsTab(tabName) {
        const sekolahContent = document.getElementById('tab-content-sekolah');
        const usersContent = document.getElementById('tab-content-users');
        const btnSekolah = document.getElementById('tab-btn-sekolah');
        const btnUsers = document.getElementById('tab-btn-users');

        if (tabName === 'users') {
            sekolahContent.style.display = 'none';
            usersContent.style.display = 'block';

            btnSekolah.classList.remove('btn-primary');
            btnSekolah.classList.add('btn-outline');

            btnUsers.classList.remove('btn-outline');
            btnUsers.classList.add('btn-primary');
        } else {
            usersContent.style.display = 'none';
            sekolahContent.style.display = 'block';

            btnUsers.classList.remove('btn-primary');
            btnUsers.classList.add('btn-outline');

            btnSekolah.classList.remove('btn-outline');
            btnSekolah.classList.add('btn-primary');
        }
    }
</script>
@endpush
@endsection

