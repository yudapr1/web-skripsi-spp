<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sistem Administrasi Pembayaran Sekolah - SMK Muhammadiyah Sekampung')</title>
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
                        'primary-light': '#3b82f6',
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
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #1e40af;
            --accent: #60a5fa;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0284c7;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-width: 260px;
            --header-height: 64px;
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(37, 99, 235, 0.07), 0 2px 4px -2px rgba(37, 99, 235, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(37, 99, 235, 0.1), 0 4px 6px -4px rgba(37, 99, 235, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Styles - Matching Siswa Gradient */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 50%, #312e81 100%);
            color: #ffffff;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(15, 23, 42, 0.15);
            border-right: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            background: rgba(255, 255, 255, 0.04);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .brand-text h3 {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: #fff;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 11px;
            color: #bfdbfe;
            font-weight: 500;
        }

        .sidebar-menu {
            padding: 18px 12px;
            flex: 1;
            overflow-y: auto;
            list-style: none;
        }

        .menu-header {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #93c5fd;
            padding: 14px 12px 6px;
        }

        .menu-item {
            margin-bottom: 4px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #dbeafe;
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .menu-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: #93c5fd;
        }

        .menu-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(3px);
        }

        .menu-link.active {
            color: #1e40af;
            background: #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            font-weight: 700;
        }

        .menu-link.active i {
            color: #2563eb;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.12);
            background: rgba(15, 23, 42, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-mini {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .user-info h4 {
            font-size: 13px;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        .user-info span {
            font-size: 11px;
            color: #bfdbfe;
            text-transform: capitalize;
            font-weight: 600;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        /* Top Header - Matching Siswa Portal Navigation Header */
        .top-navbar {
            height: var(--header-height);
            background: linear-gradient(90deg, #1e3a8a 0%, #1e40af 50%, #312e81 100%);
            border-bottom: 1px solid rgba(29, 78, 216, 0.5);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.25);
            color: #ffffff;
        }

        .page-title h2 {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #ffffff;
            color: #1e3a8a;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Content Area */
        .content-body {
            padding: 30px;
            flex: 1;
        }

        /* Alert / Messages */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Global Card */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.2s;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-success { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .badge-purple { background: #e0e7ff; color: #4338ca; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 16px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.45);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .btn-secondary:hover { background: #dbeafe; }

        .btn-success {
            background: #2563eb;
            color: #fff;
        }
        .btn-success:hover { background: #1d4ed8; }

        .btn-danger {
            background: #ef4444;
            color: #fff;
        }
        .btn-danger:hover { background: #dc2626; }

        .btn-outline {
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: #1e40af;
        }
        .btn-outline:hover { background: #eff6ff; border-color: #93c5fd; }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border-color);
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13.5px;
        }

        .table th {
            background: #eff6ff;
            color: #1e40af;
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            color: #334155;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8fbff;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            padding: 22px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary);
        }

        .stat-card.stat-green::after { background: var(--success); }
        .stat-card.stat-amber::after { background: var(--warning); }
        .stat-card.stat-rose::after { background: var(--danger); }
        .stat-card.stat-cyan::after { background: var(--info); }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-primary .stat-icon { background: #dbeafe; color: #1d4ed8; }
        .stat-green .stat-icon { background: #eff6ff; color: #2563eb; }
        .stat-amber .stat-icon { background: #fef3c7; color: #b45309; }
        .stat-rose .stat-icon { background: #fee2e2; color: #b91c1c; }
        .stat-cyan .stat-icon { background: #e0f2fe; color: #0284c7; }

        .stat-info h5 {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stat-info h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon overflow-hidden p-0.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="brand-text">
                <h3>SPP SEKOLAH</h3>
                <p>SMK Muhammadiyah</p>
            </div>
        </div>

        <ul class="sidebar-menu">
            @if(Auth::check() && Auth::user()->isBendahara())
                <!-- MENU BENDAHARA -->
                <li class="menu-header">Menu Utama</li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.dashboard') }}" class="menu-link {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.pembayaran.index') }}" class="menu-link {{ request()->routeIs('bendahara.pembayaran.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-cash-register"></i> Loket Kasir Tunai
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.pembayaran.verifikasi') }}" class="menu-link {{ request()->routeIs('bendahara.pembayaran.verifikasi') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i> Verifikasi Transfer
                    </a>
                </li>

                <li class="menu-header">Master Data</li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.students.index') }}" class="menu-link {{ request()->routeIs('bendahara.students.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-graduate"></i> Data Siswa
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.kelas.index') }}" class="menu-link {{ request()->routeIs('bendahara.kelas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chalkboard"></i> Data Kelas
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.pos.index') }}" class="menu-link {{ request()->routeIs('bendahara.pos.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i> Pos & Tarif SPP
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.tagihan.generate.form') }}" class="menu-link {{ request()->routeIs('bendahara.tagihan.generate.form') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Generate Tagihan
                    </a>
                </li>

                <li class="menu-header">Laporan & Pengaturan</li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.laporan.index') }}" class="menu-link {{ request()->routeIs('bendahara.laporan.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Rekap Kas Masuk
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.laporan.tunggakan') }}" class="menu-link {{ request()->routeIs('bendahara.laporan.tunggakan') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Rekap Tunggakan
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.settings.index') }}" class="menu-link {{ request()->routeIs('bendahara.settings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gears"></i> Profil Sekolah
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('bendahara.users.index') }}" class="menu-link {{ request()->routeIs('bendahara.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i> Kelola User
                    </a>
                </li>
            @else
                <!-- MENU SISWA -->
                <li class="menu-header">Portal Siswa</li>
                <li class="menu-item">
                    <a href="{{ route('siswa.dashboard') }}" class="menu-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i> Beranda
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('siswa.tagihan') }}" class="menu-link {{ request()->routeIs('siswa.tagihan') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice"></i> Tagihan Saya
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('siswa.riwayat') }}" class="menu-link {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pembayaran
                    </a>
                </li>
            @endif
        </ul>

        <div class="sidebar-footer">
            <div class="user-mini">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'User' }}</h4>
                    <span>{{ Auth::user()->role ?? 'Role' }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="top-navbar">
            <div class="page-title">
                <h2>@yield('header_title', 'Dashboard')</h2>
            </div>
            <div class="top-actions">
                <span style="font-size: 12.5px; color: #bfdbfe; font-weight: 500;">
                    <i class="fa-regular fa-calendar me-1"></i> {{ date('d M Y') }}
                </span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </header>

        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                    <div>
                        <ul style="margin-left: 15px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Chart.js for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
