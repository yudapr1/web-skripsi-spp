@extends('layouts.app')

@section('title', 'Loket Pembayaran Kasir - Bendahara')
@section('header_title', 'Loket Kasir Pembayaran Siswa')

@section('content')
<!-- Form Pencarian Siswa -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-magnifying-glass text-primary"></i> Cari Data Siswa
        </div>
    </div>
    
    <form action="{{ route('bendahara.pembayaran.index') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end;">
        <div class="form-group" style="flex: 1; margin-bottom: 0;">
            <label class="form-label">Masukkan NISN atau NIS Siswa:</label>
            <input type="text" name="nisn_or_nis" class="form-control" placeholder="Contoh: 0054891234 atau 20261001" value="{{ request('nisn_or_nis') }}" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary" style="height: 42px;">
            <i class="fa-solid fa-search"></i> Cari Tagihan
        </button>
    </form>
</div>

@if($selectedStudent)
    <!-- Info Siswa Ditemukan -->
    <div class="card" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 54px; height: 54px; border-radius: 50%; background: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800;">
                    {{ strtoupper(substr($selectedStudent->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700;">{{ $selectedStudent->nama_lengkap }}</h3>
                    <p style="font-size: 13px; color: #cbd5e1;">NISN: {{ $selectedStudent->nisn }} | NIS: {{ $selectedStudent->nis }} | Kelas: <strong>{{ $selectedStudent->kelas->nama_kelas ?? '-' }}</strong></p>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Total Tunggakan</div>
                <div style="font-size: 22px; font-weight: 800; color: #f87171;">Rp {{ number_format($selectedStudent->total_tunggakan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Daftar Tagihan Siswa -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Daftar Tagihan & Status Pembayaran
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pos Pembayaran</th>
                        <th>Bulan/Tahun</th>
                        <th>Jatuh Tempo</th>
                        <th>Total Tagihan</th>
                        <th>Terbayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Aksi Bayar Tunai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td><strong>{{ $bill->tarifPembayaran->posPembayaran->nama_pos }}</strong></td>
                            <td>
                                @if($bill->bulan)
                                    {{ $bill->nama_bulan }} {{ $bill->tahun }}
                                @else
                                    Tahun {{ $bill->tahun }}
                                @endif
                            </td>
                            <td>{{ $bill->jatuh_tempo ? $bill->jatuh_tempo->format('d M Y') : '-' }}</td>
                            <td>Rp {{ number_format($bill->nominal_tagihan, 0, ',', '.') }}</td>
                            <td style="color: var(--success); font-weight: 600;">Rp {{ number_format($bill->nominal_terbayar, 0, ',', '.') }}</td>
                            <td style="color: var(--danger); font-weight: 700;">Rp {{ number_format($bill->sisa_tagihan, 0, ',', '.') }}</td>
                            <td>
                                @if($bill->status === 'lunas')
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Lunas</span>
                                @elseif($bill->status === 'sebagian')
                                    <span class="badge badge-warning">Sebagian</span>
                                @else
                                    <span class="badge badge-danger">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if($bill->status !== 'lunas')
                                    <button type="button" class="btn btn-success btn-sm" onclick="openPaymentModal({{ $bill->id }}, '{{ $bill->tarifPembayaran->posPembayaran->nama_pos }}', {{ $bill->sisa_tagihan }})">
                                        <i class="fa-solid fa-money-bill-wave"></i> Bayar
                                    </button>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;"><i class="fa-solid fa-circle-check text-success"></i> Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data tagihan untuk siswa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pembayaran Siswa Ini -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-clock-rotate-left text-info"></i> Riwayat Pembayaran Siswa Terpilih
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Trx</th>
                        <th>Tanggal</th>
                        <th>Pos Tarif</th>
                        <th>Metode</th>
                        <th>Nominal Dibayar</th>
                        <th>Penerima / Verifikator</th>
                        <th>Status</th>
                        <th>Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $trx)
                        <tr>
                            <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                            <td>{{ $trx->tanggal_bayar->format('d/m/Y H:i') }}</td>
                            <td>{{ $trx->bill->tarifPembayaran->posPembayaran->nama_pos }}</td>
                            <td>
                                @if($trx->metode_pembayaran === 'tunai')
                                    <span class="badge badge-success">Tunai</span>
                                @else
                                    <span class="badge badge-purple">Transfer</span>
                                @endif
                            </td>
                            <td style="font-weight: 700;">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                            <td>{{ $trx->verifier->name ?? 'Sistem' }}</td>
                            <td>
                                @if($trx->status_verifikasi === 'diverifikasi')
                                    <span class="badge badge-success">Valid</span>
                                @elseif($trx->status_verifikasi === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->status_verifikasi === 'diverifikasi')
                                    <a href="{{ route('bendahara.kuitansi.cetak', $trx->id) }}" target="_blank" class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-print"></i> Kuitansi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Modal Pembayaran Tunai -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 450px; padding: 24px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 700;" id="modalTitle">Entri Pembayaran Tunai</h3>
            <button type="button" onclick="closePaymentModal()" style="border: none; background: transparent; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('bendahara.pembayaran.bayar_tunai') }}" method="POST">
            @csrf
            <input type="hidden" name="bill_id" id="modalBillId">
            
            <div class="form-group">
                <label class="form-label">Pos Tagihan</label>
                <input type="text" id="modalPosName" class="form-control" readonly style="background: #f1f5f9;">
            </div>

            <div class="form-group">
                <label class="form-label">Nominal Bayar (Rp)</label>
                <input type="number" name="nominal_bayar" id="modalNominal" class="form-control" required min="1000">
                <small style="color: var(--text-muted);">Sisa yang harus dibayar: <strong id="modalSisaText"></strong></small>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Titip lewat wali kelas, dll">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openPaymentModal(billId, posName, sisa) {
        document.getElementById('modalBillId').value = billId;
        document.getElementById('modalPosName').value = posName;
        document.getElementById('modalNominal').value = sisa;
        document.getElementById('modalNominal').max = sisa;
        document.getElementById('modalSisaText').innerText = 'Rp ' + sisa.toLocaleString('id-ID');
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }
</script>
@endpush
