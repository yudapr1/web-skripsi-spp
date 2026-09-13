@extends('layouts.app')

@section('title', 'Data Kelas - Bendahara')
@section('header_title', 'Manajemen Kelas Sekolah')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Form Tambah Kelas -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-plus-circle text-primary"></i> Tambah Kelas Baru
            </div>
        </div>

        <form action="{{ route('bendahara.kelas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: X RPL 1" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                <select name="tingkat" class="form-select" required>
                    <option value="10">Kelas 10 (X)</option>
                    <option value="11">Kelas 11 (XI)</option>
                    <option value="12">Kelas 12 (XII)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Jurusan / Kompetensi Keahlian</label>
                <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fa-solid fa-save"></i> Simpan Kelas
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kelas -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-chalkboard text-primary"></i> Daftar Kelas Terdaftar
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelasList as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td>Tingkat {{ $k->tingkat }}</td>
                            <td>{{ $k->jurusan ?? '-' }}</td>
                            <td><span class="badge badge-info">{{ $k->students_count }} Siswa</span></td>
                            <td>
                                <form action="{{ route('bendahara.kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus data kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" {{ $k->students_count > 0 ? 'disabled title=Masih_ada_siswa' : '' }}>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
