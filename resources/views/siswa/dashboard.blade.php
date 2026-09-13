@extends('layouts.siswa')

@section('title', 'Dashboard Siswa - SMK Muhammadiyah Sekampung')

@section('content')
<div class="space-y-6">

    <!-- 1. Banner Sambutan Siswa & Profil Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white p-6 sm:p-8 shadow-xl shadow-blue-900/10 border border-blue-700/50">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white p-1 shadow-lg shadow-blue-950/25 flex-shrink-0">
                    <div class="w-full h-full bg-blue-50 rounded-[12px] flex items-center justify-center text-blue-800 font-extrabold text-2xl sm:text-3xl">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </div>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/15 text-blue-100 border border-white/20 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                        Siswa Aktif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Halo, {{ $siswa->nama_lengkap }}! 👋</h2>
                    <p class="text-xs sm:text-sm text-blue-100 mt-0.5">
                        NISN: <span class="font-bold text-white">{{ $siswa->nisn }}</span> &bull; 
                        Kelas: <span class="font-bold text-white">{{ $siswa->kelas?->nama_kelas ?? '-' }}</span> &bull; 
                        Jurusan: <span class="text-blue-100">{{ $siswa->kelas?->jurusan ?? '-' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0 w-full md:w-auto">
                <a href="{{ route('siswa.tagihan') }}" 
                   class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white hover:bg-blue-50 text-blue-900 font-bold text-sm shadow-lg shadow-blue-950/20 transition transform active:scale-95">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    Lihat & Bayar Tagihan
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Kartu Metrik Keuangan Siswa -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Total Tagihan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tanggungan</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
                    <span class="text-[11px] text-slate-400 mt-1 block">Akumulasi seluruh tahun ajaran</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>

        <!-- Sudah Terbayar -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Sudah Terbayar</p>
                    <h3 class="text-2xl font-black text-blue-700 mt-1">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</h3>
                    <span class="text-[11px] text-blue-600/80 mt-1 block font-medium">Telah diverifikasi bendahara</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- Sisa Tunggakan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-500">Sisa Tagihan Belum Lunas</p>
                    <h3 class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                    <span class="text-[11px] text-rose-500/80 mt-1 block font-medium">Wajib diselesaikan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Progress Bar Pelunasan -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Persentase Pelunasan Tagihan</span>
            </div>
            <span class="text-sm font-extrabold text-blue-600">{{ $persentaseLunas }}% Lunas</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden p-0.5 border border-slate-200">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: {{ $persentaseLunas }}%"></div>
        </div>
    </div>

    <!-- 4. Grid Konten: Rincian Pos Tagihan Aktif & Rekening Sekolah -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Rincian Tagihan Aktif -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rincian Pos Tagihan Tahun Berjalan</h3>
                        <p class="text-[11px] text-slate-500">Tahun Ajaran: {{ $activeTagihan->tahun_ajaran ?? '2026/2027' }}</p>
                    </div>
                </div>
                <a href="{{ route('siswa.tagihan') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Lihat Semua <i class="fa-solid fa-chevron-right text-[10px] ml-0.5"></i>
                </a>
            </div>

            <div class="p-5 flex-1 divide-y divide-slate-100">
                @if($activeTagihan && $activeTagihan->items->isNotEmpty())
                    @foreach($activeTagihan->items as $item)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-slate-800">{{ $item->posPembayaran?->nama_pos }}</h4>
                                <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-400">
                                    <span>Tarif: <strong>Rp {{ number_format($item->nominal_pos, 0, ',', '.') }}</strong></span>
                                    <span>Terbayar: <strong class="text-blue-600">Rp {{ number_format($item->nominal_terbayar, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs font-black {{ $item->sisa_pos > 0 ? 'text-rose-600' : 'text-blue-600' }}">
                                    Rp {{ number_format($item->sisa_pos, 0, ',', '.') }}
                                </div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    {{ $item->status === 'lunas' ? 'bg-blue-100 text-blue-800' : ($item->status === 'sebagian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $item->status === 'lunas' ? 'Lunas' : ($item->status === 'sebagian' ? 'Sebagian' : 'Belum Lunas') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-12 text-center text-slate-400">
                        <i class="fa-solid fa-circle-check text-4xl text-blue-500 mb-2"></i>
                        <p class="text-xs font-semibold">Tidak ada tagihan tertunggak saat ini.</p>
                    </div>
                @endif
            </div>

            @if($activeTagihan && $activeTagihan->status !== 'lunas')
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-right">
                    <a href="{{ route('siswa.bayar.form', $activeTagihan->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-wallet"></i>
                        Bayar Pos Tagihan Ini
                    </a>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Rekening Resmi & 5 Transaksi Terakhir -->
        <div class="space-y-6">
            
            <!-- Info Rekening Bank Tujuan Transfer -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-2xl p-5 text-white border border-blue-800 shadow-md relative overflow-hidden">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-200 mb-3">
                    <i class="fa-solid fa-building-columns"></i>
                    Rekening Resmi Sekolah
                </div>
                <div class="space-y-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-blue-200">Nama Bank</span>
                        <p class="text-sm font-bold text-white">{{ $setting->nama_bank ?? 'Bank Syariah Indonesia (BSI)' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-blue-200">Nomor Rekening</span>
                        <div class="flex items-center justify-between bg-blue-950/70 p-2.5 rounded-xl border border-blue-800/80 mt-1">
                            <span class="font-mono text-sm font-extrabold text-white tracking-wider">{{ $setting->nomor_rekening ?? '7123456789' }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $setting->nomor_rekening ?? '7123456789' }}'); alert('Nomor rekening berhasil disalin!');" 
                                    class="text-xs text-blue-900 hover:bg-white bg-blue-100 px-2.5 py-1 rounded-lg font-bold transition">
                                <i class="fa-solid fa-copy"></i> Salin
                            </button>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-blue-200">Atas Nama</span>
                        <p class="text-xs font-semibold text-white">{{ $setting->atas_nama_rekening ?? 'SMK MUHAMMADIYAH SEKAMPUNG' }}</p>
                    </div>
                </div>
            </div>

            <!-- Transaksi Terakhir -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Transaksi Terbaru</h3>
                    <a href="{{ route('siswa.riwayat') }}" class="text-[11px] font-bold text-blue-600 hover:underline">Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentTransactions as $trx)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $trx->nomor_transaksi }}</p>
                                <span class="text-[10px] text-slate-400">{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }} &bull; {{ strtoupper($trx->metode_pembayaran) }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-slate-900">Rp {{ number_format($trx->nominal_pokok, 0, ',', '.') }}</p>
                                <span class="inline-block text-[9px] font-bold px-1.5 py-0.5 rounded
                                    {{ $trx->status === 'terverifikasi' ? 'bg-blue-100 text-blue-700' : ($trx->status === 'ditolak' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $trx->status === 'terverifikasi' ? 'Valid' : ($trx->status === 'ditolak' ? 'Ditolak' : 'Pending') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat transaksi.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
