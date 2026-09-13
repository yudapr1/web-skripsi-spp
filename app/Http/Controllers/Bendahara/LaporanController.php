<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiPembayaran;
use App\Models\TagihanTahunan;
use App\Models\Kelas;
use App\Models\PengaturanSekolah;
use App\Exports\LaporanTransaksiExport;
use App\Exports\RekapTagihanExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $kelasId = $request->input('kelas_id');
        $metode = $request->input('metode_pembayaran');

        $query = TransaksiPembayaran::with(['siswa.kelas', 'details.posPembayaran', 'verifier', 'kwitansi'])
            ->where('status', 'terverifikasi')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $transactions = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $totalPemasukan = $transactions->sum('nominal_pokok');

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('bendahara.laporan.index', compact(
            'transactions',
            'totalPemasukan',
            'startDate',
            'endDate',
            'kelasId',
            'metode',
            'kelasList'
        ));
    }

    public function tunggakan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $tahunAjaran = $request->input('tahun_ajaran');

        $query = TagihanTahunan::with(['siswa.kelas', 'items.posPembayaran'])
            ->where('status', '!=', 'lunas');

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        $tagihans = $query->orderBy('siswa_id')->get();
        $totalTunggakan = $tagihans->sum('sisa_tagihan');
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('bendahara.laporan.tunggakan', compact('tagihans', 'totalTunggakan', 'kelasList', 'kelasId', 'tahunAjaran'));
    }

    public function cetakLaporan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $metode = $request->input('metode_pembayaran');

        $query = TransaksiPembayaran::with(['siswa.kelas', 'details.posPembayaran', 'verifier', 'kwitansi'])
            ->where('status', 'terverifikasi')
            ->whereDate('tanggal_transaksi', '>=', $startDate)
            ->whereDate('tanggal_transaksi', '<=', $endDate);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $transactions = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $totalPemasukan = $transactions->sum('nominal_pokok');
        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();
        $kelas = $kelasId ? Kelas::find($kelasId) : null;

        return view('bendahara.laporan.cetak', compact('transactions', 'totalPemasukan', 'startDate', 'endDate', 'setting', 'kelas', 'metode'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'terverifikasi');
        $metode = $request->input('metode_pembayaran');
        $kelasId = $request->input('kelas_id');

        $fileName = 'Laporan_Keuangan_SMK_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new LaporanTransaksiExport($startDate, $endDate, $status, $metode, $kelasId),
            $fileName
        );
    }
}

