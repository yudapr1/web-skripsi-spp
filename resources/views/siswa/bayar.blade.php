@extends('layouts.siswa')

@section('title', 'Checkout Pembayaran Transfer - SMK Muhammadiyah Sekampung')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="checkoutApp()">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('siswa.tagihan') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tagihan
        </a>
        <span class="text-xs font-semibold text-slate-400">Tahun Ajaran: {{ $tagihan->tahun_ajaran }}</span>
    </div>

    <!-- Banner Checkout Card -->
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-blue-900/10 border border-blue-700/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 text-blue-100 border border-white/20 mb-2">
                    <i class="fa-solid fa-building-columns"></i>
                    Pembayaran Transfer Bank Manual
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white">Alokasi Pembayaran Pos Cicilan</h2>
                <p class="text-xs sm:text-sm text-blue-100 mt-1">Pilih pos tagihan yang ingin Anda cicil/bayar pada transaksi transfer kali ini.</p>
            </div>
            
            <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl text-right flex-shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100 block">Total Sisa Tagihan Tahun Ini</span>
                <span class="text-xl font-black text-white">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Form Checkout & Upload Bukti -->
    <form action="{{ route('siswa.bayar.upload', $tagihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Langkah 1: Pilih Pos & Tentukan Nominal Cicilan -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-100 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-blue-50 pb-4">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">1</div>
                <div>
                    <h3 class="text-base font-bold text-blue-950">Pilih Pos & Tentukan Nominal Cicilan</h3>
                    <p class="text-xs text-slate-500">Centang pos yang ingin dicicil dan masukkan nominal uang yang ingin ditransfer.</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($tagihan->items as $item)
                    @if($item->sisa_pos > 0)
                        <div class="p-4 rounded-2xl border transition-all"
                             :class="selectedItems['{{ $item->id }}'] ? 'bg-blue-50/70 border-blue-300 shadow-sm' : 'bg-slate-50 border-slate-200 opacity-90'">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" 
                                           id="pos_{{ $item->id }}" 
                                           class="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer"
                                           x-model="selectedItems['{{ $item->id }}']"
                                           @change="onCheckChange('{{ $item->id }}', {{ (float)$item->sisa_pos }})">
                                    <div>
                                        <label for="pos_{{ $item->id }}" class="text-sm font-bold text-slate-900 cursor-pointer">
                                            {{ $item->posPembayaran?->nama_pos }}
                                        </label>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                            <span>Sisa Tanggungan: <strong class="text-blue-700">Rp {{ number_format($item->sisa_pos, 0, ',', '.') }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full sm:w-60" x-show="selectedItems['{{ $item->id }}']" x-transition>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nominal Dicicil (Rp):</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                                        <input type="number" 
                                               name="alokasi[{{ $item->id }}][nominal]" 
                                               x-model.number="nominalItems['{{ $item->id }}']"
                                               :disabled="!selectedItems['{{ $item->id }}']"
                                               max="{{ $item->sisa_pos }}"
                                               min="0"
                                               class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- 2. Langkah 2: Ringkasan Total Transfer & Rekening Bank Sekolah -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-100 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-blue-50 pb-4">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">2</div>
                <div>
                    <h3 class="text-base font-bold text-blue-950">Total Transfer & Rekening Bank Sekolah</h3>
                    <p class="text-xs text-slate-500">Silakan lakukan pembayaran transfer sesuai nominal di bawah ke rekening resmi sekolah.</p>
                </div>
            </div>

            <!-- Rekening Bank Box -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-900 text-white p-5 rounded-2xl space-y-3 border border-blue-800 shadow-md">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-blue-800/80 pb-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Tujuan Bank Transfer</span>
                        <p class="text-sm font-bold text-white">{{ $setting->nama_bank ?? 'Bank Syariah Indonesia (BSI)' }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Atas Nama Rekening</span>
                        <p class="text-xs font-semibold text-white">{{ $setting->atas_nama_rekening ?? 'SMK MUHAMMADIYAH SEKAMPUNG' }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block">Nomor Rekening Tujuan:</span>
                        <span class="font-mono text-lg font-black text-white tracking-wider">{{ $setting->nomor_rekening ?? '7123456789' }}</span>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $setting->nomor_rekening ?? '7123456789' }}'); alert('Nomor rekening disalin!');" 
                            class="px-4 py-2 bg-white hover:bg-blue-50 text-blue-900 text-xs font-bold rounded-xl shadow-sm transition">
                        <i class="fa-solid fa-copy mr-1"></i> Salin No. Rekening
                    </button>
                </div>
            </div>

            <!-- Kalkulasi Nominal -->
            <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-blue-900 block">Total Nominal yang Ditransfer:</span>
                        <span class="text-[11px] text-blue-700">Sesuai akumulasi pos yang dipilih</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xl sm:text-2xl font-black text-blue-700" x-text="formatRupiah(subtotal)">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Langkah 3: Unggah Bukti Transfer & Info Pengirim -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">3</div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Upload Bukti Transfer</h3>
                    <p class="text-xs text-slate-500">Lampirkan foto struk ATM, tangkapan layar m-banking, atau bukti setoran.</p>
                </div>
            </div>

            <!-- Input File Bukti Transfer & Live Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Foto / Dokumen Bukti Transfer (JPG, PNG, PDF Max 2MB) *</label>
                <input type="file" name="bukti_transfer" required accept="image/*,.pdf" 
                       @change="previewFile($event)"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl cursor-pointer">
                
                <!-- Image Preview Area -->
                <div x-show="previewUrl" class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-4">
                    <img :src="previewUrl" alt="Pratinjau Bukti" class="h-24 w-24 object-cover rounded-xl border border-slate-300 shadow-sm" />
                    <div>
                        <p class="text-xs font-bold text-slate-800" x-text="fileName"></p>
                        <span class="text-[11px] text-blue-600 font-semibold"><i class="fa-solid fa-circle-check"></i> Berkas siap diunggah</span>
                    </div>
                </div>
            </div>

            <!-- Rekening Pengirim (Opsional) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Bank Pengirim</label>
                    <input type="text" name="nama_bank_pengirim" placeholder="Contoh: BRI / BCA / Mandiri" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="nama_pemilik_rekening" placeholder="Atas nama pengirim" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. Rekening Pengirim</label>
                    <input type="text" name="nomor_rekening_pengirim" placeholder="Opsional" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:border-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Siswa (Opsional)</label>
                <textarea name="catatan_siswa" rows="2" placeholder="Tuliskan catatan tambahan jika ada..." 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:border-blue-500 outline-none"></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                :disabled="subtotal <= 0"
                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-extrabold text-sm sm:text-base shadow-xl shadow-blue-600/30 transition transform active:scale-98 flex items-center justify-center gap-2">
            <i class="fa-solid fa-paper-plane"></i>
            Kirim Bukti Pembayaran untuk Diverifikasi
        </button>

    </form>

</div>

@push('scripts')
<script>
function checkoutApp() {
    return {
        selectedItems: {},
        nominalItems: {},
        previewUrl: null,
        fileName: '',
        get subtotal() {
            let total = 0;
            for (let id in this.selectedItems) {
                if (this.selectedItems[id]) {
                    let val = parseFloat(this.nominalItems[id]) || 0;
                    total += val;
                }
            }
            return total;
        },
        onCheckChange(id, sisa) {
            if (this.selectedItems[id]) {
                this.nominalItems[id] = sisa;
            } else {
                this.nominalItems[id] = 0;
            }
        },
        previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                if (file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewUrl = null;
                }
            }
        },
        formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }
    }
}
</script>
@endpush
@endsection
