<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['kelas', 'user']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('nama_lengkap', 'asc')->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('bendahara.students.index', compact('students', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('bendahara.students.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|string|unique:students,nisn|max:20',
            'nis' => 'required|string|unique:students,nis|max:20',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email|unique:users,email',
            'no_telepon_ortu' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'phone' => $request->no_telepon_ortu,
            ]);

            Student::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telepon_ortu' => $request->no_telepon_ortu,
                'status' => 'aktif',
            ]);
        });

        return redirect()->route('bendahara.students.index')->with('success', 'Data siswa dan akun portal berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['kelas', 'user', 'bills.tarifPembayaran.posPembayaran', 'transactions.bill.tarifPembayaran.posPembayaran']);
        return view('bendahara.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load(['kelas', 'user']);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('bendahara.students.edit', compact('student', 'kelasList'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'username' => 'required|string|max:50|unique:users,username,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'email' => 'nullable|email|unique:users,email,' . $student->user_id,
            'no_telepon_ortu' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,lulus,pindah',
        ]);

        DB::transaction(function () use ($request, $student) {
            $userData = [
                'name' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->no_telepon_ortu,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $student->user->update($userData);

            $student->update([
                'kelas_id' => $request->kelas_id,
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telepon_ortu' => $request->no_telepon_ortu,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('bendahara.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('bendahara.students.index')->with('success', 'Siswa dan akun pengguna berhasil dihapus.');
    }
}
