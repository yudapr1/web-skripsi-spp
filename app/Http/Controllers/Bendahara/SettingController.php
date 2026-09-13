<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $setting = SchoolSetting::firstOrCreate([], [
            'nama_sekolah' => 'SMK NEGERI 1 INFORMATIKA',
            'alamat_sekolah' => 'Jl. Pendidikan No. 45',
            'no_telepon' => '(024) 7654321',
            'email_sekolah' => 'keuangan@sekolah.sch.id',
        ]);

        // Data Users untuk Tab Kelola User di halaman Pengaturan
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

        $activeTab = $request->get('tab', 'sekolah');

        return view('bendahara.settings.index', compact('setting', 'users', 'roles', 'stats', 'activeTab'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:50',
            'email_sekolah' => 'nullable|email|max:100',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'nama_bendahara' => 'nullable|string|max:255',
            'nip_bendahara' => 'nullable|string|max:50',
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:100',
            'atas_nama_rekening' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $setting = SchoolSetting::firstOrCreate([]);
        $data = $request->except(['_token', 'logo']);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('school_logos', 'public');
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan sekolah dan rekening bank berhasil disimpan.');
    }
}
