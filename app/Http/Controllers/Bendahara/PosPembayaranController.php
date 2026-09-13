<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosPembayaran;
use App\Models\TarifPembayaran;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class PosPembayaranController extends Controller
{
    public function index()
    {
        $posList = PosPembayaran::with(['tarifs.kelas'])->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('bendahara.pos.index', compact('posList', 'kelasList'));
    }

    public function storePos(Request $request)
    {
        $request->validate([
            'nama_pos' => 'required|string|max:100',
            'tipe' => 'required|in:bulanan,bebas',
            'keterangan' => 'nullable|string',
        ]);

        PosPembayaran::create($request->all());

        return redirect()->route('bendahara.pos.index')->with('success', 'Pos pembayaran baru berhasil ditambahkan.');
    }

    public function storeTarif(Request $request)
    {
        $request->validate([
            'pos_pembayaran_id' => 'required|exists:pos_pembayaran,id',
            'tahun_ajaran' => 'required|string|max:15',
            'nominal' => 'required|numeric|min:0',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        TarifPembayaran::create($request->all());

        return redirect()->route('bendahara.pos.index')->with('success', 'Tarif pembayaran berhasil ditetapkan.');
    }

    public function generateTagihanForm()
    {
        $tarifs = TarifPembayaran::with(['posPembayaran', 'kelas'])->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('bendahara.pos.generate_tagihan', compact('tarifs', 'kelasList'));
    }

    public function generateTagihan(Request $request)
    {
        $request->validate([
            'tarif_pembayaran_id' => 'required|exists:tarif_pembayaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun' => 'required|numeric|digits:4',
            'jatuh_tempo_hari' => 'required|numeric|min:1|max:28',
        ]);

        $tarif = TarifPembayaran::with('posPembayaran')->findOrFail($request->tarif_pembayaran_id);
        $students = Student::where('kelas_id', $request->kelas_id)->where('status', 'aktif')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif di kelas yang dipilih.');
        }

        $countCreated = 0;

        DB::transaction(function () use ($students, $tarif, $request, &$countCreated) {
            foreach ($students as $student) {
                if ($tarif->posPembayaran->tipe === 'bulanan') {
                    // Generate 12 bulan (Bulan 7 s.d. 12 tahun berjalan, dan 1 s.d. 6 tahun berikutnya untuk tahun ajaran)
                    // Atau 1 s.d. 12
                    for ($m = 1; $m <= 12; $m++) {
                        $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                        $dayStr = str_pad($request->jatuh_tempo_hari, 2, '0', STR_PAD_LEFT);
                        $jatuhTempo = "{$request->tahun}-{$monthStr}-{$dayStr}";

                        // Cek apakah sudah ada tagihan untuk siswa & bulan & tarif ini
                        $exists = Bill::where('student_id', $student->id)
                            ->where('tarif_pembayaran_id', $tarif->id)
                            ->where('bulan', $m)
                            ->where('tahun', $request->tahun)
                            ->exists();

                        if (!$exists) {
                            Bill::create([
                                'student_id' => $student->id,
                                'tarif_pembayaran_id' => $tarif->id,
                                'bulan' => $m,
                                'tahun' => $request->tahun,
                                'nominal_tagihan' => $tarif->nominal,
                                'nominal_terbayar' => 0,
                                'status' => 'belum_lunas',
                                'jatuh_tempo' => $jatuhTempo,
                            ]);
                            $countCreated++;
                        }
                    }
                } else {
                    // Tagihan tipe Bebas (Sekali Bayar / Angsuran)
                    $exists = Bill::where('student_id', $student->id)
                        ->where('tarif_pembayaran_id', $tarif->id)
                        ->where('tahun', $request->tahun)
                        ->exists();

                    if (!$exists) {
                        Bill::create([
                            'student_id' => $student->id,
                            'tarif_pembayaran_id' => $tarif->id,
                            'bulan' => null,
                            'tahun' => $request->tahun,
                            'nominal_tagihan' => $tarif->nominal,
                            'nominal_terbayar' => 0,
                            'status' => 'belum_lunas',
                            'jatuh_tempo' => "{$request->tahun}-12-31",
                        ]);
                        $countCreated++;
                    }
                }
            }
        });

        return redirect()->route('bendahara.pembayaran.index')
            ->with('success', "Berhasil membuat {$countCreated} tagihan untuk kelas terpilih.");
    }
}
