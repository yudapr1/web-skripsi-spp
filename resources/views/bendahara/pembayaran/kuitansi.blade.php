<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran - {{ $transaction->kode_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 13px;
            color: #000;
            padding: 20px;
            max-width: 700px;
            margin: auto;
        }

        .receipt-box {
            border: 2px dashed #333;
            padding: 24px;
            border-radius: 8px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h2 {
            margin: 0 0 4px;
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
            text-decoration: underline;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .content-table {
            width: 100%;
            margin-bottom: 16px;
        }

        .content-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 10px;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature .line {
            margin-top: 50px;
            border-bottom: 1px solid #333;
        }

        .badge-lunas {
            border: 2px solid #000;
            display: inline-block;
            padding: 4px 12px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .receipt-box {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 13px; font-weight: bold; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 6px; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);">
            🖨️ Cetak Kuitansi / Print PDF
        </button>
    </div>

    <div class="receipt-box">
        <div class="header">
            <h2>{{ $setting->nama_sekolah ?? 'SMK NEGERI 1 UNGGULAN' }}</h2>
            <p>{{ $setting->alamat_sekolah ?? 'Jl. Pendidikan No. 45' }}</p>
            <p>Telp: {{ $setting->no_telepon ?? '-' }} | Email: {{ $setting->email_sekolah ?? '-' }}</p>
        </div>

        <div class="title">BUKTI PEMBAYARAN ADMINISTRASI SEKOLAH</div>

        <table class="content-table">
            <tr>
                <td style="width: 30%;">No. Transaksi</td>
                <td style="width: 3%;">:</td>
                <td><strong>{{ $transaction->kode_transaksi }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td>{{ $transaction->tanggal_bayar->format('d F Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td>NISN / NIS</td>
                <td>:</td>
                <td>{{ $transaction->student->nisn }} / {{ $transaction->student->nis }}</td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>:</td>
                <td><strong>{{ $transaction->student->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>Kelas / Jurusan</td>
                <td>:</td>
                <td>{{ $transaction->student->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Pembayaran</td>
                <td>:</td>
                <td>
                    {{ $transaction->bill->tarifPembayaran->posPembayaran->nama_pos }}
                    @if($transaction->bill->bulan)
                        (Bulan {{ $transaction->bill->nama_bulan }} {{ $transaction->bill->tahun }})
                    @endif
                </td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td>{{ strtoupper($transaction->metode_pembayaran) }}</td>
            </tr>
            <tr>
                <td>Jumlah Dibayar</td>
                <td>:</td>
                <td><strong style="font-size: 15px;">Rp {{ number_format($transaction->nominal_bayar, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Status Tagihan</td>
                <td>:</td>
                <td>
                    <span class="badge-lunas">{{ strtoupper($transaction->bill->status) }}</span>
                    @if($transaction->bill->status !== 'lunas')
                        <small>(Sisa: Rp {{ number_format($transaction->bill->sisa_tagihan, 0, ',', '.') }})</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>{{ $transaction->keterangan ?? '-' }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature">
                <p>Siswa / Penyetor,</p>
                <div class="line"></div>
                <p style="margin-top: 4px;">{{ $transaction->student->nama_lengkap }}</p>
            </div>

            <div class="signature">
                <p>Petugas / Bendahara,</p>
                <div class="line"></div>
                <p style="margin-top: 4px;">{{ $transaction->verifier->name ?? $setting->nama_bendahara ?? 'Bendahara Sekolah' }}</p>
            </div>
        </div>
    </div>

</body>
</html>
