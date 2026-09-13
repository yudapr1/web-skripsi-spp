<div class="space-y-4 p-3 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-800" x-data="{ openModal: false }">
    <div class="flex items-center justify-between">
        <h4 class="font-bold text-slate-800 dark:text-gray-200 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Bukti Pembayaran yang Diunggah Siswa:
        </h4>
        @php
            $record = $getRecord();
            $bukti = $record?->buktiPembayaran;
        @endphp
        @if ($bukti && $bukti->file_path)
            <span class="text-[11px] text-slate-500 dark:text-gray-400">Klik foto untuk perbesar</span>
        @endif
    </div>

    @if ($bukti && $bukti->file_path)
        @php
            $fileUrl = asset('storage/' . $bukti->file_path);
            $isPdf = str_ends_with(strtolower($bukti->file_path), '.pdf') || ($bukti->file_type === 'application/pdf');
        @endphp

        <div class="flex flex-col items-center justify-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-gray-700 shadow-sm">
            @if ($isPdf)
                <div class="p-6 text-center">
                    <svg class="w-16 h-16 text-rose-500 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    <p class="text-xs font-semibold text-slate-700 dark:text-gray-300">Dokumen Bukti Transfer Berformat PDF</p>
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition">
                            Buka Dokumen PDF ↗
                        </a>
                        <a href="{{ route('bukti.download', $bukti->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh PDF
                        </a>
                    </div>
                </div>
            @else
                <div class="relative group cursor-pointer overflow-hidden rounded-lg border border-slate-200 dark:border-gray-700" @click="openModal = true">
                    <img src="{{ $fileUrl }}" alt="Bukti Transfer" class="max-h-80 w-auto rounded object-contain transition-transform duration-300 group-hover:scale-105" />
                    
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 text-white font-bold text-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                        Klik untuk Perbesar Penuh
                    </div>
                </div>

                <div class="mt-2.5 flex items-center justify-center gap-2">
                    <button type="button" @click="openModal = true" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-slate-800 dark:text-gray-200 text-xs font-semibold rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Perbesar Foto
                    </button>
                    <a href="{{ route('bukti.download', $bukti->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Unduh Foto
                    </a>
                </div>

                <!-- Lightbox / Modal Full View -->
                <div x-show="openModal" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                     @keydown.escape.window="openModal = false"
                     style="display: none;">
                    
                    <div class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center" @click.away="openModal = false">
                        <button type="button" @click="openModal = false" 
                                class="absolute -top-10 right-0 text-white hover:text-rose-400 font-extrabold text-sm flex items-center gap-1 bg-black/50 px-3 py-1 rounded-full">
                            ✕ Tutup (ESC)
                        </button>
                        
                        <img src="{{ $fileUrl }}" alt="Bukti Transfer Penuh" class="max-h-[75vh] w-auto max-w-full rounded-xl shadow-2xl object-contain border border-white/20" />
                        
                        <div class="mt-3 text-center flex items-center gap-3">
                            <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-lg border border-slate-600 shadow transition">
                                Buka Ukuran Asli ↗
                            </a>
                            <a href="{{ route('bukti.download', $bukti->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold rounded-lg shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Unduh Foto Bukti
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-3 text-xs text-slate-600 dark:text-gray-300 text-center space-y-0.5 border-t border-slate-100 dark:border-gray-700 pt-2 w-full">
                <p>Bank Pengirim: <strong class="text-slate-900 dark:text-white">{{ $bukti->nama_bank_pengirim ?? '-' }}</strong></p>
                <p>Nama Pengirim: <strong class="text-slate-900 dark:text-white">{{ $bukti->nama_pemilik_rekening ?? '-' }}</strong> {{ $bukti->nomor_rekening_pengirim ? '(' . $bukti->nomor_rekening_pengirim . ')' : '' }}</p>
            </div>
        </div>
    @else
        <div class="p-6 text-center bg-white dark:bg-gray-800 rounded-lg border border-dashed border-slate-300 dark:border-gray-700">
            <p class="text-xs text-slate-500 italic">Tidak ada foto bukti transfer terlampir (pembayaran tunai di loket).</p>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-3 text-xs bg-blue-50 dark:bg-blue-950/40 p-3 rounded-lg border border-blue-200 dark:border-blue-800/60 text-blue-900 dark:text-blue-300">
        <div>
            <p class="text-[11px] text-blue-700 dark:text-blue-400">Nominal Pokok Tagihan:</p>
            <p class="font-bold text-sm">Rp {{ number_format($record?->nominal_pokok ?? 0, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-[11px] text-blue-700 dark:text-blue-400">Total Transfer Siswa:</p>
            <p class="font-bold text-sm text-blue-700 dark:text-blue-400">Rp {{ number_format($record?->total_transfer ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
