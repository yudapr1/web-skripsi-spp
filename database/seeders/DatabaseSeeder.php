<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PosPembayaran;
use App\Models\TarifPembayaran;
use App\Models\PengaturanSekolah;
use App\Models\TagihanTahunan;
use App\Models\TagihanItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roleBendahara = Role::create([
            'name' => 'bendahara',
            'display_name' => 'Bendahara Sekolah',
        ]);

        $roleSiswa = Role::create([
            'name' => 'siswa',
            'display_name' => 'Siswa / Wali Murid',
        ]);

        // 2. Seed Default Bendahara User
        $userBendahara = User::create([
            'role_id' => $roleBendahara->id,
            'name' => 'Bendahara SMK',
            'username' => 'bendahara',
            'email' => 'bendahara@smkmuhsekampung.sch.id',
            'password' => Hash::make('password123'),
            'status' => 'aktif',
        ]);

        // 3. Seed Pengaturan Sekolah
        PengaturanSekolah::create([
            'nama_sekolah' => 'SMK Muhammadiyah Sekampung',
            'alamat_sekolah' => 'JL. RAYA SEKAMPUNG, Giri Kelopo Mulyo, Kec. Sekampung, Kab. Lampung Timur, Lampung',
            'nomor_telepon' => '081234567890',
            'email_sekolah' => 'info@smkmuhsekampung.sch.id',
            'nama_bank' => 'Bank Syariah Indonesia (BSI)',
            'nomor_rekening' => '7123456789',
            'atas_nama_rekening' => 'SMK MUHAMMADIYAH SEKAMPUNG',
            'nama_bendahara' => 'Hj. Siti Aminah, S.E.',
            'nip_bendahara' => '198507152010012003',
        ]);

        // 4. Seed Data Kelas
        $kelasX_TKJ = Kelas::create([
            'nama_kelas' => 'X TKJ 1',
            'tingkat' => 'X',
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'status' => 'aktif',
        ]);

        $kelasXI_AKL = Kelas::create([
            'nama_kelas' => 'XI AKL 1',
            'tingkat' => 'XI',
            'jurusan' => 'Akuntansi dan Keuangan Lembaga',
            'status' => 'aktif',
        ]);

        $kelasXII_TBSM = Kelas::create([
            'nama_kelas' => 'XII TBSM 1',
            'tingkat' => 'XII',
            'jurusan' => 'Teknik Bisnis Sepeda Motor',
            'status' => 'aktif',
        ]);

        // 5. Seed Pos Pembayaran (8 Pos Standar Sekolah)
        $posData = [
            ['kode_pos' => 'SPP', 'nama_pos' => 'Sumbangan Pembinaan Pendidikan (SPP)'],
            ['kode_pos' => 'DAFTAR_ULANG', 'nama_pos' => 'Daftar Ulang & Administrasi'],
            ['kode_pos' => 'SERAGAM', 'nama_pos' => 'Uang Baju & Atribut Sekolah'],
            ['kode_pos' => 'INFAQ', 'nama_pos' => 'Infaq Pembangunan Gedung / Sarpras'],
            ['kode_pos' => 'DANA_KEGIATAN', 'nama_pos' => 'Dana Kegiatan Kesiswaan & OSIS'],
            ['kode_pos' => 'PRAKERIN', 'nama_pos' => 'Praktik Kerja Industri (Prakerin)'],
            ['kode_pos' => 'UKK', 'nama_pos' => 'Uji Kompetensi Keahlian (UKK)'],
            ['kode_pos' => 'STUDY_TOUR', 'nama_pos' => 'Kunjungan Industri / Study Tour'],
        ];

        $posModels = [];
        foreach ($posData as $p) {
            $posModels[$p['kode_pos']] = PosPembayaran::create([
                'kode_pos' => $p['kode_pos'],
                'nama_pos' => $p['nama_pos'],
                'is_active' => true,
            ]);
        }

        // 6. Seed Tarif Pembayaran Berdasarkan Tingkat Kelas (Tahun Ajaran 2026/2027)
        $tahunAjaran = '2026/2027';

        // Tarif Tingkat X
        $tarifX = [
            'SPP' => 1800000.00,
            'DAFTAR_ULANG' => 500000.00,
            'SERAGAM' => 750000.00,
            'INFAQ' => 400000.00,
            'DANA_KEGIATAN' => 300000.00,
            'PRAKERIN' => 0.00,
            'UKK' => 0.00,
            'STUDY_TOUR' => 350000.00,
        ];

        // Tarif Tingkat XI
        $tarifXI = [
            'SPP' => 1800000.00,
            'DAFTAR_ULANG' => 400000.00,
            'SERAGAM' => 0.00,
            'INFAQ' => 300000.00,
            'DANA_KEGIATAN' => 300000.00,
            'PRAKERIN' => 600000.00,
            'UKK' => 0.00,
            'STUDY_TOUR' => 400000.00,
        ];

        // Tarif Tingkat XII
        $tarifXII = [
            'SPP' => 1800000.00,
            'DAFTAR_ULANG' => 400000.00,
            'SERAGAM' => 0.00,
            'INFAQ' => 300000.00,
            'DANA_KEGIATAN' => 300000.00,
            'PRAKERIN' => 0.00,
            'UKK' => 750000.00,
            'STUDY_TOUR' => 0.00,
        ];

        foreach ($tarifX as $kode => $nominal) {
            TarifPembayaran::create([
                'pos_pembayaran_id' => $posModels[$kode]->id,
                'tahun_ajaran' => $tahunAjaran,
                'tingkat' => 'X',
                'nominal' => $nominal,
            ]);
        }

        foreach ($tarifXI as $kode => $nominal) {
            TarifPembayaran::create([
                'pos_pembayaran_id' => $posModels[$kode]->id,
                'tahun_ajaran' => $tahunAjaran,
                'tingkat' => 'XI',
                'nominal' => $nominal,
            ]);
        }

        foreach ($tarifXII as $kode => $nominal) {
            TarifPembayaran::create([
                'pos_pembayaran_id' => $posModels[$kode]->id,
                'tahun_ajaran' => $tahunAjaran,
                'tingkat' => 'XII',
                'nominal' => $nominal,
            ]);
        }

        // 7. Seed Siswa Contoh
        // Siswa 1 (Telah Claim Akun)
        $userSiswa1 = User::create([
            'role_id' => $roleSiswa->id,
            'name' => 'Ahmad Fauzi',
            'username' => '0061234567', // NISN
            'email' => 'ahmad.fauzi@siswa.sch.id',
            'password' => Hash::make('password123'),
            'status' => 'aktif',
        ]);

        $siswa1 = Siswa::create([
            'user_id' => $userSiswa1->id,
            'kelas_id' => $kelasX_TKJ->id,
            'nisn' => '0061234567',
            'nis' => '20261001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Sekampung',
            'tanggal_lahir' => '2008-05-14',
            'alamat' => 'Desa Giri Kelopo Mulyo, Sekampung',
            'nomor_telepon' => '082111222333',
            'nomor_telepon_wali' => '082199887766',
            'status_siswa' => 'aktif',
        ]);

        // Siswa 2 (Belum Claim Akun / Belum punya User ID)
        $siswa2 = Siswa::create([
            'user_id' => null,
            'kelas_id' => $kelasX_TKJ->id,
            'nisn' => '0069876543',
            'nis' => '20261002',
            'nama_lengkap' => 'Nur Aini Putri',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Metro',
            'tanggal_lahir' => '2008-08-20',
            'alamat' => 'Kec. Sekampung, Lampung Timur',
            'nomor_telepon' => '085211223344',
            'nomor_telepon_wali' => '085288776655',
            'status_siswa' => 'aktif',
        ]);

        // 8. Generate Tagihan Tahunan Contoh untuk Siswa 1
        $totalNominalX = array_sum($tarifX);
        $tagihanSiswa1 = TagihanTahunan::create([
            'nomor_tagihan' => 'TAG-2026-X-0001',
            'siswa_id' => $siswa1->id,
            'tahun_ajaran' => $tahunAjaran,
            'total_tagihan' => $totalNominalX,
            'total_terbayar' => 0.00,
            'sisa_tagihan' => $totalNominalX,
            'status' => 'belum_lunas',
        ]);

        foreach ($tarifX as $kode => $nominal) {
            TagihanItem::create([
                'tagihan_tahunan_id' => $tagihanSiswa1->id,
                'pos_pembayaran_id' => $posModels[$kode]->id,
                'nominal_pos' => $nominal,
                'nominal_terbayar' => 0.00,
                'sisa_pos' => $nominal,
                'status' => $nominal == 0 ? 'lunas' : 'belum_lunas',
            ]);
        }
    }
}
