<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::withCount('students')->orderBy('nama_kelas')->get();
        return view('bendahara.kelas.index', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'tingkat' => 'required|string|max:10',
            'jurusan' => 'nullable|string|max:100',
        ]);

        Kelas::create($request->all());

        return redirect()->route('bendahara.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $kela->id,
            'tingkat' => 'required|string|max:10',
            'jurusan' => 'nullable|string|max:100',
        ]);

        $kela->update($request->all());

        return redirect()->route('bendahara.kelas.index')->with('success', 'Data kelas berhasil diubah.');
    }

    public function destroy(Kelas $kela)
    {
        if ($kela->students()->count() > 0) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa.');
        }

        $kela->delete();
        return redirect()->route('bendahara.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
