<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penerimaan Kas Sekolah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-filter {
            margin-bottom: 15px;
            font-size: 12px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th, .report-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }

        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 220px;
        }

        .signature .line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 6px; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);">
            🖨️ Cetak / Print PDF
        </button>
    </div>

    <div class="header">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="width: 70px; border: none; vertical-align: middle; text-align: left;">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" style="width: 60px; height: 60px; object-fit: contain;">
                    @endif
                </td>
                <td style="border: none; vertical-align: middle; text-align: center;">
                    <h2>{{ $setting->nama_sekolah ?? 'SMK MUHAMMADIYAH SEKAMPUNG' }}</h2>
                    <p>{{ $setting->alamat_sekolah ?? 'JL. RAYA SEKAMPUNG, Giri Kelopo Mulyo, Kec. Sekampung, Kab. Lampung Timur, Lampung' }}</p>
                    <p>Telp: {{ $setting->nomor_telepon ?? $setting->no_telepon ?? '-' }} | Email: {{ $setting->email_sekolah ?? '-' }}</p>
                </td>
                <td style="width: 70px; border: none;"></td>
            </tr>
        </table>
    </div>

    <div class="title">LAPORAN REKAPITULASI PENERIMAAN KAS ADMINISTRASI SEKOLAH</div>

    <div class="info-filter">
        Periode Tanggal: <strong>{{ date('d/m/Y', strtotime($startDate)) }} s.d. {{ date('d/m/Y', strtotime($endDate)) }}</strong><br>
        Kelas: <strong>{{ $kelas ? $kelas->nama_kelas : 'Semua Kelas' }}</strong> | 
        Metode: <strong>{{ $metode ? strtoupper($metode) : 'Semua Metode' }}</strong>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>No. Trx</th>
                <th>Tanggal</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Pos Pembayaran</th>
                <th>Metode</th>
                <th>Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $trx->kode_transaksi }}</td>
                    <td>{{ $trx->tanggal_bayar->format('d/m/Y H:i') }}</td>
                    <td>{{ $trx->student->nisn }}</td>
                    <td>{{ $trx->student->nama_lengkap }}</td>
                    <td>{{ $trx->student->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $trx->bill->tarifPembayaran->posPembayaran->nama_pos }} {{ $trx->bill->bulan ? '('.$trx->bill->nama_bulan.')' : '' }}</td>
                    <td style="text-align: center;">{{ strtoupper($trx->metode_pembayaran) }}</td>
                    <td style="text-align: right;">{{ number_format($trx->nominal_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">Tidak ada transaksi.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="8" style="text-align: right;">TOTAL PENERIMAAN:</td>
                <td style="text-align: right; font-size: 13px;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="line"></div>
            <p style="margin-top: 4px;"><strong>{{ $setting->nama_kepala_sekolah ?? 'Kepala Sekolah' }}</strong><br>NIP: {{ $setting->nip_kepala_sekolah ?? '-' }}</p>
        </div>

        <div class="signature">
            <p>Semarang, {{ date('d F Y') }}<br>Bendahara Sekolah</p>
            <div class="line"></div>
            <p style="margin-top: 4px;"><strong>{{ $setting->nama_bendahara ?? 'Bendahara' }}</strong><br>NIP: {{ $setting->nip_bendahara ?? '-' }}</p>
        </div>
    </div>

</body>
</html>
