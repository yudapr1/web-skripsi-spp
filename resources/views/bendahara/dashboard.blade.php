@extends('layouts.app')

@section('title', 'Dashboard Bendahara - SIM Keuangan Sekolah')
@section('header_title', 'Ringkasan Dashboard Keuangan')

@section('content')
<!-- Statistik Card Grid -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div class="stat-info">
            <h5>Kas Hari Ini</h5>
            <h2>Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div class="stat-info">
            <h5>Kas Bulan Ini</h5>
            <h2>Rp {{ number_format($penerimaanBulanIni, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="stat-card stat-rose">
        <div class="stat-icon">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <h5>Total Tunggakan</h5>
            <h2>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <h5>Siswa Aktif</h5>
            <h2>{{ number_format($totalSiswa, 0, ',', '.') }} Siswa</h2>
        </div>
    </div>
</div>

<!-- Quick Action / Shortcut Menu -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
    <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-primary" style="padding: 10px 18px; border-radius: var(--radius-sm);">
        <i class="fa-solid fa-cash-register me-2"></i> Loket Kasir Tunai
    </a>
    <a href="{{ route('bendahara.pembayaran.verifikasi') }}" class="btn btn-outline" style="padding: 10px 18px; border-radius: var(--radius-sm); background: var(--bg-card);">
        <i class="fa-solid fa-receipt me-2 text-warning"></i> Verifikasi Transfer
    </a>
    <a href="{{ route('bendahara.users.index') }}" class="btn btn-outline" style="padding: 10px 18px; border-radius: var(--radius-sm); background: var(--bg-card);">
        <i class="fa-solid fa-users-gear me-2 text-primary"></i> Kelola User / Akun Pengguna
    </a>
    <a href="{{ route('bendahara.settings.index') }}" class="btn btn-outline" style="padding: 10px 18px; border-radius: var(--radius-sm); background: var(--bg-card);">
        <i class="fa-solid fa-gears me-2 text-info"></i> Pengaturan Sistem
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Grafik Tren Penerimaan -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-chart-area text-primary"></i> Tren Penerimaan Kas 6 Bulan Terakhir
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <!-- Verifikasi Transfer Pending -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-bell text-warning"></i> Menunggu Verifikasi
            </div>
            <a href="{{ route('bendahara.pembayaran.verifikasi') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        @if($pendingTransfers->isEmpty())
            <div style="text-align: center; padding: 40px 10px; color: var(--text-muted);">
                <i class="fa-regular fa-circle-check" style="font-size: 36px; margin-bottom: 10px; color: var(--success);"></i>
                <p>Tidak ada transfer menunggu konfirmasi saat ini.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($pendingTransfers as $pt)
                    <div style="padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px;">{{ $pt->student->nama_lengkap }}</div>
                            <div style="font-size: 11.5px; color: var(--text-muted);">{{ $pt->bill->tarifPembayaran->posPembayaran->nama_pos }}</div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--primary);">Rp {{ number_format($pt->nominal_bayar, 0, ',', '.') }}</div>
                        </div>
                        <a href="{{ route('bendahara.pembayaran.verifikasi') }}" class="btn btn-primary btn-sm">Periksa</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Transaksi Kas Terbaru -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-clock-rotate-left text-info"></i> Transaksi Pembayaran Terbaru
        </div>
        <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Buka Loket Kasir
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Trx</th>
                    <th>Tanggal</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Pembayaran</th>
                    <th>Metode</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $trx)
                    <tr>
                        <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                        <td>{{ $trx->tanggal_bayar->format('d/m/Y H:i') }}</td>
                        <td>{{ $trx->student->nama_lengkap }}</td>
                        <td><span class="badge badge-info">{{ $trx->student->kelas->nama_kelas ?? '-' }}</span></td>
                        <td>
                            {{ $trx->bill->tarifPembayaran->posPembayaran->nama_pos }}
                            @if($trx->bill->bulan)
                                ({{ $trx->bill->nama_bulan }} {{ $trx->bill->tahun }})
                            @endif
                        </td>
                        <td>
                            @if($trx->metode_pembayaran === 'tunai')
                                <span class="badge badge-success"><i class="fa-solid fa-money-bill-wave"></i> Tunai</span>
                            @else
                                <span class="badge badge-purple"><i class="fa-solid fa-building-columns"></i> Transfer</span>
                            @endif
                        </td>
                        <td style="font-weight: 700;">Rp {{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->status_verifikasi === 'diverifikasi')
                                <span class="badge badge-success">Diverifikasi</span>
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
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada riwayat transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartMonths) !!},
            datasets: [{
                label: 'Penerimaan (Rp)',
                data: {!! json_encode($chartIncome) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#1d4ed8',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
