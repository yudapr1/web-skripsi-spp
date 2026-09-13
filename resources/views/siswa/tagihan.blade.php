@extends('layouts.siswa')

@section('title', 'Tagihan Siswa - SMK Muhammadiyah Sekampung')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Daftar Tagihan Keuangan Siswa</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rincian seluruh kewajiban administrasi sekolah per tahun ajaran.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600">
            <i class="fa-solid fa-user-graduate text-blue-600"></i>
            {{ $siswa->nama_lengkap }} ({{ $siswa->kelas?->nama_kelas ?? '-' }})
        </div>
    </div>

    <!-- List Tagihan Tahunan -->
    <div class="space-y-6">
        @forelse($tagihans as $tagihan)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                
                <!-- Header Tagihan Tahunan -->
                <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-extrabold flex-shrink-0">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">Tahun Ajaran {{ $tagihan->tahun_ajaran }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $tagihan->status === 'lunas' ? 'bg-blue-100 text-blue-800' : ($tagihan->status === 'sebagian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $tagihan->status === 'lunas' ? 'Lunas' : ($tagihan->status === 'sebagian' ? 'Sebagian' : 'Belum Lunas') }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">No. Tagihan: <span class="font-mono font-bold text-slate-600">{{ $tagihan->nomor_tagihan }}</span></p>
                        </div>
                    </div>

                    <!-- Summary Ringkas & Tombol Aksi -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 w-full md:w-auto justify-between md:justify-end">
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Sisa Tagihan</span>
                            <span class="text-base sm:text-lg font-black {{ $tagihan->sisa_tagihan > 0 ? 'text-rose-600' : 'text-blue-600' }}">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($tagihan->status !== 'lunas')
                            <a href="{{ route('siswa.bayar.form', $tagihan->id) }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition transform active:scale-95">
                                <i class="fa-solid fa-credit-card"></i>
                                Bayar Cicilan Transfer
                            </a>
                        @else
                            <div class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 px-3.5 py-2 rounded-xl">
                                <i class="fa-solid fa-circle-check"></i>
                                Lunas 100%
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tabel Rincian Pos Tagihan (Itemized Allocation) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/75 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                            <tr>
                                <th class="py-3 px-4 sm:px-6">No</th>
                                <th class="py-3 px-4 sm:px-6">Nama Pos Pembayaran</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Tarif Tagihan</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Sudah Terbayar</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Sisa Pos</th>
                                <th class="py-3 px-4 sm:px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($tagihan->items as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 font-bold text-slate-900">
                                        {{ $item->posPembayaran?->nama_pos }}
                                        @if($item->posPembayaran?->keterangan)
                                            <span class="block text-[10px] font-normal text-slate-400 mt-0.5">{{ $item->posPembayaran->keterangan }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right font-medium">Rp {{ number_format($item->nominal_pos, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right font-bold text-blue-600">Rp {{ number_format($item->nominal_terbayar, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right font-black {{ $item->sisa_pos > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                        Rp {{ number_format($item->sisa_pos, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider
                                            {{ $item->status === 'lunas' ? 'bg-blue-100 text-blue-800' : ($item->status === 'sebagian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $item->status === 'lunas' ? 'Lunas' : ($item->status === 'sebagian' ? 'Sebagian' : 'Belum Lunas') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-200 text-slate-900">
                            <tr>
                                <td colspan="2" class="py-3.5 px-4 sm:px-6 text-right uppercase text-[11px]">Total Tagihan & Pelunasan:</td>
                                <td class="py-3.5 px-4 sm:px-6 text-right">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-right text-blue-600">Rp {{ number_format($tagihan->total_terbayar, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 sm:px-6 text-right text-rose-600 font-extrabold">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm text-slate-400">
                <i class="fa-solid fa-file-circle-check text-5xl text-blue-500 mb-3"></i>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Tagihan</h3>
                <p class="text-xs text-slate-500 mt-1">Anda belum memiliki tagihan tahunan yang dibuat oleh Bendahara.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
