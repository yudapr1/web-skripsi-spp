<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Portal Pembayaran Siswa - SMK Muhammadiyah Sekampung'); ?></title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-blue-50/40 text-slate-800 font-sans min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 border-b border-blue-700/50 text-white sticky top-0 z-40 shadow-lg shadow-blue-900/15">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shadow-blue-950/20">
                        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-sm font-extrabold tracking-tight uppercase leading-none text-white">SMK Muhammadiyah Sekampung</h1>
                        <p class="text-[11px] text-blue-200 font-medium tracking-wide">Portal Pembayaran Keuangan Siswa</p>
                    </div>
                </div>

                <!-- Nav Menu & User Profile -->
                <div class="flex items-center gap-2 sm:gap-6">
                    <nav class="hidden md:flex items-center gap-1.5">
                        <a href="<?php echo e(route('siswa.dashboard')); ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition <?php echo e(request()->routeIs('siswa.dashboard') ? 'bg-white text-blue-900 shadow-md' : 'text-blue-100 hover:text-white hover:bg-white/10'); ?>">
                            <i class="fa-solid fa-house mr-1.5"></i> Dashboard
                        </a>
                        <a href="<?php echo e(route('siswa.tagihan')); ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition <?php echo e(request()->routeIs('siswa.tagihan') || request()->routeIs('siswa.bayar.*') ? 'bg-white text-blue-900 shadow-md' : 'text-blue-100 hover:text-white hover:bg-white/10'); ?>">
                            <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i> Tagihan Saya
                        </a>
                        <a href="<?php echo e(route('siswa.riwayat')); ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition <?php echo e(request()->routeIs('siswa.riwayat') ? 'bg-white text-blue-900 shadow-md' : 'text-blue-100 hover:text-white hover:bg-white/10'); ?>">
                            <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Riwayat Pembayaran
                        </a>
                    </nav>

                    <!-- User Pill & Logout -->
                    <div class="flex items-center gap-3 border-l border-blue-700/60 pl-3 sm:pl-6">
                        <div class="hidden sm:block text-right">
                            <p class="text-xs font-bold text-white leading-tight"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-[10px] text-blue-200">NISN: <?php echo e(Auth::user()->siswa?->nisn ?? '-'); ?></p>
                        </div>
                        
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" onclick="return confirm('Yakin ingin keluar dari akun?')" 
                                    class="px-3 py-1.5 rounded-xl text-white bg-blue-700/60 hover:bg-white hover:text-blue-900 border border-blue-600 text-xs font-bold flex items-center gap-1.5 transition shadow-sm" title="Keluar">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Nav -->
        <div class="md:hidden bg-blue-950 border-t border-blue-900 px-4 py-2 flex justify-around text-xs">
            <a href="<?php echo e(route('siswa.dashboard')); ?>" class="flex flex-col items-center gap-1 py-1 <?php echo e(request()->routeIs('siswa.dashboard') ? 'text-white font-bold' : 'text-blue-300'); ?>">
                <i class="fa-solid fa-house"></i> Beranda
            </a>
            <a href="<?php echo e(route('siswa.tagihan')); ?>" class="flex flex-col items-center gap-1 py-1 <?php echo e(request()->routeIs('siswa.tagihan') ? 'text-white font-bold' : 'text-blue-300'); ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan
            </a>
            <a href="<?php echo e(route('siswa.riwayat')); ?>" class="flex flex-col items-center gap-1 py-1 <?php echo e(request()->routeIs('siswa.riwayat') ? 'text-white font-bold' : 'text-blue-300'); ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
            </a>
        </div>
    </header>

    <!-- Main Body Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Flash Messages -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-blue-600 text-lg mt-0.5"></i>
                <div class="font-medium"><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
                <div class="font-medium"><?php echo e(session('error')); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($errors) && $errors->any()): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-lg mt-0.5"></i>
                <div>
                    <strong class="font-bold">Mohon periksa data formulir:</strong>
                    <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($err); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-blue-100 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-bold text-blue-900">SMK Muhammadiyah Sekampung</p>
            <p class="mt-0.5 text-slate-500">Sistem Informasi Pembayaran & Administrasi Keuangan Siswa Terintegrasi</p>
            <p class="text-[11px] text-slate-400 mt-2">&copy; <?php echo e(date('Y')); ?> SMK Muhammadiyah Sekampung. All rights reserved.</p>
        </div>
    </footer>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\web skripsi\resources\views/layouts/siswa.blade.php ENDPATH**/ ?>