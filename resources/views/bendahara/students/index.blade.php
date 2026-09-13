@extends('layouts.app')

@section('title', 'Data Siswa - Bendahara')
@section('header_title', 'Manajemen Data Siswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-users text-primary"></i> Daftar Siswa Terdaftar
        </div>
        <a href="{{ route('bendahara.students.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus"></i> Tambah Siswa Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <form action="{{ route('bendahara.students.index') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama, NISN, atau NIS..." value="{{ request('search') }}">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="kelas_id" class="form-select">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1; min-width: 130px;">
            <select name="status" class="form-select">
                <option value="">-- Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline" style="height: 42px;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>NISN</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Username Akun</th>
                    <th>Status</th>
                    <th>Total Tunggakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                    <tr>
                        <td><strong>{{ $s->nisn }}</strong></td>
                        <td>{{ $s->nis }}</td>
                        <td><strong>{{ $s->nama_lengkap }}</strong></td>
                        <td><span class="badge badge-info">{{ $s->kelas->nama_kelas ?? '-' }}</span></td>
                        <td><code>{{ $s->user->username ?? '-' }}</code></td>
                        <td>
                            @if($s->status === 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($s->status) }}</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: {{ $s->total_tunggakan > 0 ? 'var(--danger)' : 'var(--success)' }};">
                            Rp {{ number_format($s->total_tunggakan, 0, ',', '.') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('bendahara.pembayaran.index', ['nisn_or_nis' => $s->nisn]) }}" class="btn btn-success btn-sm" title="Buka Loket Pembayaran">
                                    <i class="fa-solid fa-cash-register"></i>
                                </a>
                                <a href="{{ route('bendahara.students.show', $s->id) }}" class="btn btn-outline btn-sm" title="Detail Siswa">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('bendahara.students.edit', $s->id) }}" class="btn btn-primary btn-sm" title="Edit Data">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('bendahara.students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus data siswa ini beserta tagihannya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data siswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $students->links() }}
    </div>
</div>
@endsection
