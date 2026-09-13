<?php

namespace App\Exports;

use App\Models\TagihanTahunan;
use App\Models\TagihanItem;
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

class RekapTagihanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $tahunAjaran;
    protected $status;
    protected $tingkat;
    protected $kelasId;

    public function __construct($tahunAjaran = null, $status = null, $tingkat = null, $kelasId = null)
    {
        $this->tahunAjaran = $tahunAjaran;
        $this->status = $status;
        $this->tingkat = $tingkat;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = TagihanTahunan::with(['siswa.kelas', 'items.posPembayaran'])
            ->orderBy('tahun_ajaran', 'desc');

        if ($this->tahunAjaran) {
            $query->where('tahun_ajaran', $this->tahunAjaran);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->tingkat) {
            $query->whereHas('siswa.kelas', function ($q) {
                $q->where('tingkat', $this->tingkat);
            });
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
            'No. Tagihan',
            'Tahun Ajaran',
            'NISN',
            'Nama Lengkap Siswa',
            'Kelas & Jurusan',
            'Total Kewajiban Tagihan (Rp)',
            'Sudah Terbayar (Rp)',
            'Sisa Tunggakan (Rp)',
            'Status Pelunasan',
            'Rincian Status Pos Tagihan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $rincianPos = $row->items->map(function ($item) {
            $namaPos = $item->posPembayaran?->nama_pos ?? '-';
            $sisa = number_format($item->sisa_pos, 0, ',', '.');
            $status = strtoupper($item->status);
            return "{$namaPos}: Sisa Rp {$sisa} [{$status}]";
        })->implode(" | ");

        $kelas = ($row->siswa?->kelas?->nama_kelas ?? '-') . ' (' . ($row->siswa?->kelas?->jurusan ?? '-') . ')';

        $statusText = match ($row->status) {
            'lunas' => 'Lunas 100%',
            'sebagian' => 'Sebagian (Mencicil)',
            'belum_lunas' => 'Belum Lunas',
            default => ucfirst($row->status ?? '-'),
        };

        return [
            $no,
            $row->nomor_tagihan,
            $row->tahun_ajaran,
            $row->siswa?->nisn ?? '-',
            $row->siswa?->nama_lengkap ?? '-',
            $kelas,
            (float) $row->total_tagihan,
            (float) $row->total_terbayar,
            (float) $row->sisa_tagihan,
            $statusText,
            $rincianPos ?: '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
            'H' => '#,##0',
            'I' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style Heading (Baris 1)
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Slate Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Style Data Rows
        if ($lastRow > 1) {
            $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
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
            $sheet->getStyle("J2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
