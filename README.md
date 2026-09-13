# Sistem Pembayaran Administrasi Sekolah (SPP & Keuangan)
Framework: **Laravel 12** | Database: **MySQL** | Multi-Role: **Bendahara & Siswa**

Aplikasi web sistem informasi manajemen pembayaran administrasi dan SPP sekolah yang komprehensif, modern, dan siap pakai.

---

## 🔑 Hak Akses & Akun Demo

Aplikasi memiliki 2 level pengguna dengan login fleksibel (bisa menggunakan **Username**, **Email**, atau **NISN** siswa):

| Role | Username / Login | Password | Akses & Kemampuan |
|---|---|---|---|
| **Bendahara** | `bendahara` *(atau `bendahara@sekolah.sch.id`)* | `password123` | Dashboard kas, Loket Kasir Tunai, Verifikasi Bukti Transfer, Master Data Siswa & Kelas, Pos Tarif SPP, Generator Tagihan Massal, Rekapitulasi Kas & Tunggakan, Cetak Kuitansi & Laporan. |
| **Siswa** | `siswa` *(atau NISN `0054891234`)* | `password123` | Portal Siswa, Info Tagihan Bulanan & Bebas, Upload Foto/Bukti Transfer Bank, Riwayat Transaksi, Cetak Kuitansi Mandiri. |
| **Siswa 2** | `nabila` *(atau NISN `0054895678`)* | `password123` | Akun siswa pengujian kedua |

---

## 🚀 Panduan Menjalankan Aplikasi di Komputer Lokal

### Prasyarat
- **PHP** versi 8.2 atau lebih baru
- **Composer**
- **MySQL Server** (melalui XAMPP, Laragon, atau MySQL Standalone)

### Langkah-langkah:

1. **Buat Database di MySQL / phpMyAdmin:**
   - Nama database: `db_spp_sekolah`
   - Karakter set: `utf8mb4_general_ci`

2. **Instal Dependensi (Vendor):**
   Buka terminal/command prompt di direktori project `d:\web skripsi`, lalu jalankan:
   ```bash
   composer install
   ```

3. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

4. **Jalankan Migrasi Database & Seeder Data Awal:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Perintah ini akan membuat seluruh tabel dan mengisikan data master sekolah, kelas, siswa contoh, pos SPP, tarif, dan tagihan awal)*

5. **Hubungkan Storage untuk Bukti Transfer & Logo:**
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka browser dan akses alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📂 Struktur Utama Aplikasi

- **`app/Http/Controllers/`**
  - `AuthController.php` : Autentikasi multi-login & redirection berdasarkan role.
  - `Bendahara/` : Dashboard, Siswa, Kelas, Pos Tarif, Loket Pembayaran, Verifikasi Transfer, Laporan Kas & Tunggakan, Profil Sekolah.
  - `Siswa/` : Portal Mandiri Siswa (Dashboard, Tagihan, Upload Bukti Transfer, Riwayat).
- **`app/Models/`** : `User`, `Student`, `Kelas`, `PosPembayaran`, `TarifPembayaran`, `Bill`, `Transaction`, `SchoolSetting`.
- **`database/migrations/`** : 8 file skema database relasional lengkap.
- **`database/seeders/DatabaseSeeder.php`** : Seeder lengkap berisi akun default dan data dummy.
- **`resources/views/`** : Template Blade modern responsive dengan palet warna elegan, grafik Chart.js, dan layout kuitansi/laporan siap cetak.
