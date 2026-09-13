<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isBendahara()) {
                return redirect('/admin');
            }
            return redirect()->route('siswa.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'], // bisa username / email / NISN
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Coba login via username / email
        $attempt = Auth::attempt([
            $fieldType => $request->login,
            'password' => $request->password,
        ], $request->filled('remember'));

        // Jika gagal dan login berupa NISN / NIS siswa
        if (!$attempt) {
            $siswa = Siswa::where('nisn', $request->login)
                ->orWhere('nis', $request->login)
                ->first();

            if ($siswa && $siswa->user) {
                $attempt = Auth::attempt([
                    'id' => $siswa->user->id,
                    'password' => $request->password,
                ], $request->filled('remember'));
            }
        }

        if ($attempt) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isBendahara()) {
                return redirect()->intended('/admin')
                    ->with('success', 'Selamat datang kembali, Bendahara!');
            }

            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Selamat datang di Portal Siswa, ' . $user->name . '!');
        }

        return back()->withErrors([
            'login' => 'Username/Email/NISN atau kata sandi tidak cocok.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->isBendahara()) {
                return redirect('/admin');
            }
            return redirect()->route('siswa.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nisn' => ['required', 'string'],
            'tanggal_lahir' => ['required', 'date'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        // 1. Validasi ke data master Siswa
        $siswa = Siswa::where('nisn', $request->nisn)
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$siswa) {
            return back()->withErrors([
                'nisn' => 'Data siswa dengan NISN dan Tanggal Lahir tersebut tidak ditemukan dalam data sekolah. Silakan hubungi Bendahara/Tata Usaha.',
            ])->withInput();
        }

        // 2. Cek apakah sudah pernah klaim akun
        if ($siswa->user_id) {
            return back()->withErrors([
                'nisn' => 'Akun untuk siswa ini sudah pernah diaktifkan! Silakan masuk melalui halaman login.',
            ])->withInput();
        }

        // 3. Buat User baru (Role Siswa)
        $roleSiswa = \App\Models\Role::where('name', 'siswa')->first();

        $user = \App\Models\User::create([
            'role_id' => $roleSiswa->id,
            'name' => $siswa->nama_lengkap,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'status' => 'aktif',
        ]);

        // 4. Hubungkan User ID ke record Siswa
        $siswa->update([
            'user_id' => $user->id,
        ]);

        // 5. Login otomatis
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Selamat datang, akun Anda berhasil diaktifkan! Silakan cek rincian tagihan Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar sistem.');
    }
}
