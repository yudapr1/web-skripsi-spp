<?php

namespace App\Exports;

use App\Models\TransaksiPembayaran;
use App\Models\PengaturanSekolah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanTransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $metode;
    protected $kelasId;

    public function __construct($startDate = null, $endDate = null, $status = null, $metode = null, $kelasId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->metode = $metode;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = TransaksiPembayaran::with(['siswa.kelas', 'details.posPembayaran', 'kwitansi', 'verifier'])
            ->orderBy('tanggal_transaksi', 'desc');

        if ($this->startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->metode) {
            $query->where('metode_pembayaran', $this->metode);
        }

        if ($this->kelasId) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->kelasId);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Transaksi',
            'Tanggal & Waktu',
            'NISN',
            'Nama Lengkap Siswa',
            'Kelas & Jurusan',
            'Metode Bayar',
            'Rincian Pos Tagihan',
            'Nominal Pokok (Rp)',
            'Kode Unik',
            'Total Transfer (Rp)',
            'Status Transaksi',
            'No. Kwitansi Resmi',
            'Diverifikasi Oleh',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $rincianPos = $row->details->map(function ($detail) {
            $namaPos = $detail->posPembayaran?->nama_pos ?? '-';
            $nominal = number_format($detail->nominal_bayar, 0, ',', '.');
            return "{$namaPos}: Rp {$nominal}";
        })->implode(" | ");

        $kelas = ($row->siswa?->kelas?->nama_kelas ?? '-') . ' (' . ($row->siswa?->kelas?->jurusan ?? '-') . ')';

        $statusText = match ($row->status) {
            'terverifikasi' => 'Terverifikasi (Valid)',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Ditolak',
            default => ucfirst($row->status ?? '-'),
        };

        return [
            $no,
            $row->nomor_transaksi,
            $row->tanggal_transaksi ? $row->tanggal_transaksi->format('d/m/Y H:i') : '-',
            $row->siswa?->nisn ?? '-',
            $row->siswa?->nama_lengkap ?? '-',
            $kelas,
            strtoupper($row->metode_pembayaran ?? '-'),
            $rincianPos ?: '-',
            (float) $row->nominal_pokok,
            (int) $row->kode_unik,
            (float) $row->total_transfer,
            $statusText,
            $row->kwitansi?->nomor_kwitansi ?? '-',
            $row->verifier?->name ?? '-',
            $row->catatan_bendahara ?: ($row->catatan_siswa ?: '-'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0',
            'K' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style Heading (Baris 1)
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '047857'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Style Data Rows
        if ($lastRow > 1) {
            $sheet->getStyle("A2:O{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Alignment spesifik kolom
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L2:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M2:M{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
