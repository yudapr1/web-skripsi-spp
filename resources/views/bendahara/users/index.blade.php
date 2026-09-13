@extends('layouts.app')

@section('title', 'Kelola Pengguna - Bendahara')
@section('header_title', 'Pengaturan Sistem')

@section('content')
<!-- Navigasi Tab Pengaturan -->
<div style="margin-bottom: 24px;">
    <div style="display: flex; gap: 10px; background: rgba(0,0,0,0.03); padding: 6px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <a href="{{ route('bendahara.settings.index') }}" class="btn {{ request()->routeIs('bendahara.settings.*') ? 'btn-primary' : 'btn-outline' }}" style="flex: 1; text-align: center; justify-content: center; font-size: 14px; font-weight: 600; padding: 10px 16px;">
            <i class="fa-solid fa-school me-2"></i> Profil Instansi & Rekening
        </a>
        <a href="{{ route('bendahara.users.index') }}" class="btn {{ request()->routeIs('bendahara.users.*') ? 'btn-primary' : 'btn-outline' }}" style="flex: 1; text-align: center; justify-content: center; font-size: 14px; font-weight: 600; padding: 10px 16px;">
            <i class="fa-solid fa-users-gear me-2"></i> Kelola User / Akun Pengguna
        </a>
    </div>
</div>

<!-- Statistik Ringkas Pengguna -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card" style="background: var(--bg-card); padding: 18px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Total Pengguna</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text-dark); margin-top: 4px;">{{ number_format($stats['total']) }}</div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="stat-card" style="background: var(--bg-card); padding: 18px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Akun Bendahara</div>
            <div style="font-size: 24px; font-weight: 700; color: #8b5cf6; margin-top: 4px;">{{ number_format($stats['bendahara']) }}</div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(139, 92, 246, 0.12); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-user-shield"></i>
        </div>
    </div>

    <div class="stat-card" style="background: var(--bg-card); padding: 18px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Akun Siswa</div>
            <div style="font-size: 24px; font-weight: 700; color: #06b6d4; margin-top: 4px;">{{ number_format($stats['siswa']) }}</div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(6, 182, 212, 0.12); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
    </div>

    <div class="stat-card" style="background: var(--bg-card); padding: 18px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Status Aktif</div>
            <div style="font-size: 24px; font-weight: 700; color: #10b981; margin-top: 4px;">{{ number_format($stats['aktif']) }}</div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>
</div>

<!-- Card Utama Data User -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div class="card-title">
            <i class="fa-solid fa-users-gear text-primary"></i> Daftar Pengguna Sistem
        </div>
        <a href="{{ route('bendahara.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus-circle"></i> Tambah Pengguna Baru
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <form action="{{ route('bendahara.users.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 220px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email..." value="{{ request('search') }}">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="role_id" class="form-select">
                <option value="">-- Semua Role --</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->display_name ?? ucfirst($r->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1; min-width: 140px;">
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline" style="height: 42px;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search', 'role_id', 'status']))
            <a href="{{ route('bendahara.users.index') }}" class="btn btn-outline" style="height: 42px; display: inline-flex; align-items: center;" title="Reset Filter">
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
                    <th>Terdaftar Pada</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: {{ $user->role?->name === 'bendahara' ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #0ea5e9, #06b6d4)' }}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="font-size: 10px; background: rgba(99, 102, 241, 0.15); color: var(--primary); padding: 2px 6px; border-radius: 10px; font-weight: 600; margin-left: 4px;">Akun Anda</span>
                                        @endif
                                    </div>
                                    @if($user->siswa)
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            <i class="fa-solid fa-school"></i> Siswa: {{ $user->siswa->kelas?->nama_kelas ?? 'Tanpa Kelas' }} (NISN: {{ $user->siswa->nisn }})
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code style="font-size: 12px; background: rgba(0,0,0,0.05); padding: 3px 6px; border-radius: 4px;">{{ $user->username }}</code>
                        </td>
                        <td>
                            {{ $user->email ?? '-' }}
                        </td>
                        <td>
                            @if($user->role?->name === 'bendahara')
                                <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.3);">
                                    <i class="fa-solid fa-user-shield"></i> {{ $user->role->display_name ?? 'Bendahara' }}
                                </span>
                            @else
                                <span class="badge" style="background: rgba(6, 182, 212, 0.15); color: #0891b2; border: 1px solid rgba(6, 182, 212, 0.3);">
                                    <i class="fa-solid fa-user-graduate"></i> {{ $user->role->display_name ?? 'Siswa' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'aktif')
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle-check"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fa-solid fa-circle-xmark"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted);">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d M Y H:i') : '-' }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('bendahara.users.toggle-status', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->status === 'aktif' ? 'menonaktifkan' : 'mengaktifkan' }} user {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->status === 'aktif' ? 'btn-outline' : 'btn-outline' }}" title="{{ $user->status === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}" style="color: {{ $user->status === 'aktif' ? '#f59e0b' : '#10b981' }}; border-color: {{ $user->status === 'aktif' ? '#f59e0b' : '#10b981' }};">
                                            <i class="fa-solid {{ $user->status === 'aktif' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('bendahara.users.edit', $user->id) }}" class="btn btn-sm btn-outline" title="Edit Pengguna">
                                    <i class="fa-solid fa-pencil text-primary"></i>
                                </a>

                                @if($user->id !== auth()->id() && !$user->siswa)
                                    <form action="{{ route('bendahara.users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @elseif($user->siswa)
                                    <button type="button" class="btn btn-sm btn-outline" disabled title="Akun terikat data siswa. Kelola di Data Siswa." style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa-solid fa-lock"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            <i class="fa-solid fa-user-slash" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.4;"></i>
                            Tidak ada data pengguna yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
