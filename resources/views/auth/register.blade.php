<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klaim Akun Siswa - SMK Muhammadiyah Sekampung</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        'primary-dark': '#1d4ed8',
                        navy: '#0F172A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-950 via-blue-900 to-indigo-900 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden font-sans text-slate-800">

    <!-- Decorative Background Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/25 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl p-8 border border-blue-100 relative z-10 my-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 rounded-2xl bg-white border border-blue-100 p-2 inline-flex items-center justify-center shadow-lg shadow-blue-500/10 mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Muhammadiyah" class="w-full h-full object-contain">
            </div>
            <h2 class="text-2xl font-extrabold text-blue-950 tracking-tight">Klaim Akun Siswa Mandiri</h2>
            <p class="text-sm text-slate-500 mt-1">SMK Muhammadiyah Sekampung</p>
            <p class="text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-xl p-3 mt-4">
                <i class="fa-solid fa-circle-info mr-1"></i> Masukkan <strong>NISN</strong> dan <strong>Tanggal Lahir</strong> yang telah didaftarkan sekolah untuk mengaktifkan akun login Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl p-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-base mt-0.5"></i>
                <div>
                    <strong class="font-semibold">Terjadi Kesalahan:</strong>
                    <ul class="mt-1 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form Klaim Akun -->
        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Step 1: Validasi Data Master -->
            <div class="space-y-4 border-b border-slate-200 pb-5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px]">1</span>
                    Verifikasi Data Siswa
                </h4>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">NISN (Nomor Induk Siswa Nasional)</label>
                    <div class="relative">
                        <i class="fa-solid fa-address-card absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" required placeholder="Contoh: 0069876543" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Lahir Siswa</label>
                    <div class="relative">
                        <i class="fa-solid fa-calendar-days absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Format: Hari/Bulan/Tahun (Sesuai akta/ijazah)</span>
                </div>
            </div>

            <!-- Step 2: Buat Kredensial Login -->
            <div class="space-y-4 pt-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px]">2</span>
                    Buat Akun & Kata Sandi Baru
                </h4>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Username Login</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: ahmad_fauzi atau gunakan NISN" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Aktif (Opsional)</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@gmail.com" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" x-data="{ showPass: false, showConfirm: false }">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                        <div class="relative flex items-center">
                            <i class="fa-solid fa-lock absolute left-3.5 text-slate-400 text-sm"></i>
                            <input :type="showPass ? 'text' : 'password'" name="password" required placeholder="Minimal 6 karakter" 
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3 text-slate-400 hover:text-blue-600 focus:outline-none" title="Lihat/Sembunyikan Sandi">
                                <i class="fa-solid" :class="showPass ? 'fa-eye-slash text-blue-600' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Ulangi Kata Sandi</label>
                        <div class="relative flex items-center">
                            <i class="fa-solid fa-lock-open absolute left-3.5 text-slate-400 text-sm"></i>
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required placeholder="Ulangi kata sandi" 
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 text-slate-400 hover:text-blue-600 focus:outline-none" title="Lihat/Sembunyikan Sandi">
                                <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash text-blue-600' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 mt-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-blue-600/30 hover:shadow-blue-600/40 transition transform active:scale-95 flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-check"></i>
                Aktivasi Akun Saya
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-200 text-xs text-slate-500">
            Sudah memiliki akun aktif? 
            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline ml-1">Masuk Sekarang</a>
        </div>
    </div>

</body>
</html>
