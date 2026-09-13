<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'siswa']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $roles = Role::orderBy('display_name')->get();

        $stats = [
            'total' => User::count(),
            'bendahara' => User::whereHas('role', fn($q) => $q->where('name', 'bendahara'))->count(),
            'siswa' => User::whereHas('role', fn($q) => $q->where('name', 'siswa'))->count(),
            'aktif' => User::where('status', 'aktif')->count(),
            'nonaktif' => User::where('status', 'nonaktif')->count(),
        ];

        return view('bendahara.users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        return view('bendahara.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username|regex:/^[a-zA-Z0-9._-]+$/',
            'email' => 'nullable|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'username.regex' => 'Username hanya boleh memuat huruf, angka, titik, underscore, dan strip.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        return redirect()->route('bendahara.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('display_name')->get();
        return view('bendahara.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'username.regex' => 'Username hanya boleh memuat huruf, angka, titik, underscore, dan strip.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        // Mencegah menonaktifkan akun sendiri jika sedang login
        if ($user->id === Auth::id() && $request->status === 'nonaktif') {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini.');
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('bendahara.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Proteksi: jangan izinkan menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        // Jika user terikat dengan data siswa, lepaskan tautan user_id pada siswa agar data master siswa tetap aman
        if ($user->siswa) {
            $siswa = $user->siswa;
            $siswa->user_id = null;
            $siswa->save();
        }

        $namaUser = $user->name;
        $user->delete();

        return redirect()->route('bendahara.users.index')->with('success', "Akun pengguna {$namaUser} berhasil dihapus dari sistem.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
        }

        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        $statusText = $user->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', "Password untuk akun {$user->name} berhasil di-reset.");
    }
}
