<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $kwitansi->nomor_kwitansi }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
            position: relative;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .school-info {
            font-size: 9.5px;
            color: #475569;
            margin-top: 3px;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 14px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .doc-title .nomor-kwt {
            font-size: 10.5px;
            color: #334155;
            margin-top: 3px;
            font-weight: bold;
        }
        .table-info {
            width: 100%;
            margin-bottom: 10px;
        }
        .table-info td {
            padding: 2px 0;
            vertical-align: top;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .table-items th {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1.5px solid #64748b;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .table-items td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .terbilang-box {
            background-color: #f8fafc;
            border: 1px dashed #94a3b8;
            padding: 6px 10px;
            border-radius: 4px;
            margin-top: 6px;
            font-style: italic;
            font-size: 10px;
        }
        .footer-ttd {
            width: 100%;
            margin-top: 15px;
        }
        .footer-ttd td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .stamp-box {
            display: inline-block;
            border: 1.5px solid #059669;
            color: #059669;
            padding: 3px 12px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border-radius: 4px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi Sekolah -->
    <div class="header-kop">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70px; vertical-align: middle; text-align: left;">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" style="width: 58px; height: 58px; object-fit: contain;">
                    @endif
                </td>
                <td style="vertical-align: middle;">
                    <div class="school-name">{{ $setting->nama_sekolah ?? 'SMK MUHAMMADIYAH SEKAMPUNG' }}</div>
                    <div class="school-info">{{ $setting->alamat_sekolah ?? 'JL. RAYA SEKAMPUNG, Giri Kelopo Mulyo, Kec. Sekampung, Kab. Lampung Timur, Lampung' }}</div>
                    <div class="school-info">Telp/WhatsApp: {{ $setting->nomor_telepon ?? '0812-3456-7890' }} | Email: {{ $setting->email_sekolah ?? 'info@smkmuhsekampung.sch.id' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul Dokumen & Nomor Kwitansi -->
    <div class="doc-title">
        <h3>KWITANSI PEMBAYARAN SISWA</h3>
        <div class="nomor-kwt">No: {{ $kwitansi->nomor_kwitansi }}</div>
    </div>

    <!-- Identitas Transaksi & Siswa -->
    <table class="table-info">
        <tr>
            <td style="width: 18%;">Telah Terima Dari</td>
            <td style="width: 2%;">:</td>
            <td style="width: 45%;"><strong>{{ $siswa->nama_lengkap }}</strong> (NISN: {{ $siswa->nisn }})</td>
            <td style="width: 15%;">Tanggal Bayar</td>
            <td style="width: 2%;">:</td>
            <td style="width: 18%;">{{ $kwitansi->tanggal_terbit->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Kelas / Jurusan</td>
            <td>:</td>
            <td>{{ $siswa->kelas?->nama_kelas ?? '-' }} - {{ $siswa->kelas?->jurusan ?? '-' }}</td>
            <td>Metode Bayar</td>
            <td>:</td>
            <td><strong>{{ strtoupper($transaksi->metode_pembayaran) }}</strong></td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>:</td>
            <td>{{ $transaksi->tagihanTahunan?->tahun_ajaran ?? '-' }}</td>
            <td>No. Transaksi</td>
            <td>:</td>
            <td>{{ $transaksi->nomor_transaksi }}</td>
        </tr>
    </table>

    <!-- Rincian Pos yang Dibayarkan -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 60%;">Rincian Pos Pembayaran</th>
                <th style="width: 35%; text-align: right;">Jumlah yang Dibayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->details as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->posPembayaran?->nama_pos ?? 'Pembayaran Pos Tagihan' }}</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($detail->nominal_bayar, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" style="text-align: right; text-transform: uppercase;">Total Pembayaran:</td>
                <td style="text-align: right; font-size: 12px; color: #0f172a;">Rp {{ number_format($transaksi->nominal_pokok, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Terbilang & Catatan Sisa -->
    <div class="terbilang-box">
        <strong>Terbilang:</strong> <em># {{ $terbilang }} #</em>
    </div>

    <div style="margin-top: 6px; font-size: 9.5px; color: #64748b;">
        * Sisa tanggungan tagihan tahun berjalan saat ini: <strong>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
    </div>

    <!-- Tanda Tangan -->
    <table class="footer-ttd">
        <tr>
            <td>
                <div>Penyetor / Siswa,</div>
                <div style="height: 40px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $siswa->nama_lengkap }}</div>
            </td>
            <td>
                <div>Sekampung, {{ $kwitansi->tanggal_terbit->translatedFormat('d F Y') }}</div>
                <div>Bendahara Penerima,</div>
                <div class="stamp-box">LUNAS / VALID</div>
                <div style="font-weight: bold; text-decoration: underline; margin-top: 4px;">{{ $setting->nama_bendahara ?? 'Hj. Siti Aminah, S.E.' }}</div>
                @if($setting->nip_bendahara)
                    <div style="font-size: 9px; color: #64748b;">NIP. {{ $setting->nip_bendahara }}</div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
