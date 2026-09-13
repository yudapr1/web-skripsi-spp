@extends('layouts.siswa')

@section('title', 'Riwayat Transaksi & Kwitansi - SMK Muhammadiyah Sekampung')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Riwayat Transaksi Pembayaran</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh transaksi tunai loket maupun transfer bank beserta status verifikasi.</p>
        </div>
        <a href="{{ route('siswa.tagihan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold hover:bg-blue-100 transition">
            <i class="fa-solid fa-credit-card"></i> Bayar Tagihan Baru
        </a>
    </div>

    <!-- Tabel Riwayat Transaksi -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">No. Transaksi</th>
                        <th class="py-3.5 px-4 sm:px-6">Tanggal</th>
                        <th class="py-3.5 px-4 sm:px-6">Metode</th>
                        <th class="py-3.5 px-4 sm:px-6">Rincian Pos Tagihan</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Nominal Pokok</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Kwitansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Nomor Transaksi -->
                            <td class="py-4 px-4 sm:px-6 font-mono font-bold text-slate-900">
                                {{ $trx->nomor_transaksi }}
                            </td>

                            <!-- Tanggal -->
                            <td class="py-4 px-4 sm:px-6 text-slate-500 whitespace-nowrap">
                                {{ $trx->tanggal_transaksi->format('d M Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $trx->tanggal_transaksi->format('H:i') }} WIB</span>
                            </td>

                            <!-- Metode -->
                            <td class="py-4 px-4 sm:px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                    {{ $trx->metode_pembayaran === 'tunai' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    <i class="fa-solid {{ $trx->metode_pembayaran === 'tunai' ? 'fa-money-bill-wave' : 'fa-building-columns' }}"></i>
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>

                            <!-- Rincian Pos -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="space-y-1">
                                    @foreach($trx->details as $detail)
                                        <div class="flex items-center justify-between gap-2 text-[11px]">
                                            <span class="font-medium text-slate-800">{{ $detail->posPembayaran?->nama_pos }}</span>
                                            <span class="text-slate-500 font-semibold">Rp {{ number_format($detail->nominal_bayar, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Nominal Pokok -->
                            <td class="py-4 px-4 sm:px-6 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($trx->nominal_pokok, 0, ',', '.') }}
                            </td>

                            <!-- Status Verifikasi & Bukti -->
                            <td class="py-4 px-4 sm:px-6 text-center">
                                @if($trx->status === 'terverifikasi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-circle-check text-blue-600"></i> Valid
                                    </span>
                                @elseif($trx->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-hourglass-half text-amber-600"></i> Menunggu
                                    </span>
                                @elseif($trx->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800" title="{{ $trx->catatan_bendahara ?? 'Bukti tidak sesuai' }}">
                                        <i class="fa-solid fa-circle-xmark text-rose-600"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        {{ $trx->status }}
                                    </span>
                                @endif

                                @if($trx->buktiPembayaran?->file_path)
                                    <div class="mt-1 flex items-center justify-center gap-2">
                                        <a href="{{ asset('storage/' . $trx->buktiPembayaran->file_path) }}" target="_blank" 
                                           class="inline-flex items-center gap-1 text-[10px] text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </a>
                                        <span class="text-slate-300">&bull;</span>
                                        <a href="{{ route('bukti.download', $trx->buktiPembayaran->id) }}" 
                                           class="inline-flex items-center gap-1 text-[10px] text-slate-600 hover:text-slate-900 font-semibold hover:underline">
                                            <i class="fa-solid fa-download"></i> Unduh
                                        </a>
                                    </div>
                                @endif

                                @if($trx->catatan_bendahara)
                                    <p class="text-[10px] text-rose-600 mt-1 italic max-w-xs">{{ $trx->catatan_bendahara }}</p>
                                @endif
                            </td>

                            <!-- Tombol Kwitansi -->
                            <td class="py-4 px-4 sm:px-6 text-center">
                                @if($trx->status === 'terverifikasi' && $trx->kwitansi)
                                    <a href="{{ route('kwitansi.pdf', $trx->kwitansi->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-sm transition">
                                        <i class="fa-solid fa-file-arrow-down text-blue-200"></i>
                                        PDF Kwitansi
                                    </a>
                                @elseif($trx->status === 'menunggu_verifikasi')
                                    <span class="text-[10px] text-slate-400 italic">Sedang diverifikasi</span>
                                @else
                                    <span class="text-[10px] text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-4xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold">Belum ada riwayat transaksi pembayaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
