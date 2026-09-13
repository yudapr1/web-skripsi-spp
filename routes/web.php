<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboard;
use App\Http\Controllers\Bendahara\StudentController;
use App\Http\Controllers\Bendahara\KelasController;
use App\Http\Controllers\Bendahara\PosPembayaranController;
use App\Http\Controllers\Bendahara\PembayaranController;
use App\Http\Controllers\Bendahara\LaporanController;
use App\Http\Controllers\Bendahara\SettingController;
use App\Http\Controllers\Bendahara\UserController;
use App\Http\Controllers\Siswa\SiswaPortalController;
use App\Http\Controllers\KwitansiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Cetak Kwitansi PDF Resmi (Bisa diakses Bendahara & Siswa terautentikasi)
Route::middleware('auth')->get('/kwitansi/{kwitansi}/pdf', [KwitansiController::class, 'cetakPdf'])->name('kwitansi.pdf');

// Unduh Foto / Berkas Bukti Transfer Pembayaran Siswa
Route::middleware('auth')->get('/bukti-pembayaran/{bukti}/download', [KwitansiController::class, 'downloadBukti'])->name('bukti.download');

// Autentikasi & Klaim Akun Siswa
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// ==========================================
// AREA ROLE: BENDAHARA (Admin Loket & Keuangan)
// ==========================================
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [BendaharaDashboard::class, 'index'])->name('dashboard');

    // Data Master Siswa
    Route::resource('students', StudentController::class);

    // Data Master Kelas
    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit']);

    // Pos Tarif & Otomasi Tagihan
    Route::get('/pos-tarif', [PosPembayaranController::class, 'index'])->name('pos.index');
    Route::post('/pos-tarif/pos', [PosPembayaranController::class, 'storePos'])->name('pos.store');
    Route::post('/pos-tarif/tarif', [PosPembayaranController::class, 'storeTarif'])->name('tarif.store');
    Route::get('/generate-tagihan', [PosPembayaranController::class, 'generateTagihanForm'])->name('tagihan.generate.form');
    Route::post('/generate-tagihan', [PosPembayaranController::class, 'generateTagihan'])->name('tagihan.generate');

    // Loket Pembayaran Kasir (Cari NIS/NISN, Bayar Tunai)
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/bayar-tunai', [PembayaranController::class, 'bayarTunai'])->name('pembayaran.bayar_tunai');

    // Verifikasi Bukti Transfer Siswa
    Route::get('/verifikasi-transfer', [PembayaranController::class, 'verifikasiIndex'])->name('pembayaran.verifikasi');
    Route::post('/verifikasi-transfer/{transaction}', [PembayaranController::class, 'prosesVerifikasi'])->name('pembayaran.proses_verifikasi');

    // Cetak Kuitansi
    Route::get('/kuitansi/{transaction}', [PembayaranController::class, 'cetakKuitansi'])->name('kuitansi.cetak');

    // Laporan & Rekapitulasi
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');

    // Pengaturan Profil Sekolah & Kelola Pengguna
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
});

// ==========================================
// AREA ROLE: SISWA (Portal Pembayaran Siswa)
// ==========================================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/tagihan', [SiswaPortalController::class, 'tagihan'])->name('tagihan');
    Route::get('/riwayat', [SiswaPortalController::class, 'riwayat'])->name('riwayat');
    Route::get('/bayar/{tagihan}', [SiswaPortalController::class, 'bayarForm'])->name('bayar.form');
    Route::post('/bayar/{tagihan}', [SiswaPortalController::class, 'prosesUploadBayar'])->name('bayar.upload');
    Route::get('/kuitansi/{transaction}', [SiswaPortalController::class, 'cetakKuitansi'])->name('kuitansi.cetak');
});
