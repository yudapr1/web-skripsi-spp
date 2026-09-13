<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Administrasi Pembayaran Sekolah</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --dark: #1e3a8a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle glowing background orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
        }
        .orb-1 { width: 450px; height: 450px; background: #60a5fa; top: -120px; left: -120px; }
        .orb-2 { width: 400px; height: 400px; background: #93c5fd; bottom: -80px; right: -80px; }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(30, 58, 138, 0.25);
            padding: 40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(219, 234, 254, 0.8);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-badge {
            width: 96px;
            height: 96px;
            border-radius: 24px;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
            margin-bottom: 16px;
            padding: 6px;
        }

        .login-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13.5px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #93c5fd;
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border-radius: 12px;
            border: 1.5px solid #dbeafe;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s;
            background: #f8fbff;
        }

        .form-input:focus {
            background: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.18);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transition: all 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    @php
        $settingSekolah = \App\Models\PengaturanSekolah::first();
        $namaSekolah = $settingSekolah->nama_sekolah ?? 'SMK Muhammadiyah Sekampung';
    @endphp

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-badge" style="background: #ffffff; padding: 4px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ $namaSekolah }}" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h2>{{ $namaSekolah }}</h2>
            <p>Silakan masuk menggunakan akun Bendahara atau Siswa</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Username / Email / NISN</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="login" class="form-input" placeholder="Masukkan username, email, atau NISN" value="{{ old('login') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="passwordInput" name="password" class="form-input" style="padding-right: 44px;" placeholder="••••••••" required>
                    <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility('passwordInput', 'passwordEyeIcon')" style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px; display: flex; align-items: center; justify-content: center; outline: none;" title="Tampilkan/Sembunyikan Kata Sandi">
                        <i id="passwordEyeIcon" class="fa-solid fa-eye" style="position: static; font-size: 15px; color: #94a3b8;"></i>
                    </button>
                </div>
            </div>

            <script>
                function togglePasswordVisibility(inputId, iconId) {
                    const passwordInput = document.getElementById(inputId);
                    const eyeIcon = document.getElementById(iconId);
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                        eyeIcon.style.color = '#2563eb';
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                        eyeIcon.style.color = '#94a3b8';
                    }
                }
            </script>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #475569;">
                    <input type="checkbox" name="remember" value="1"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
            </button>

            <div style="text-align: center; margin-top: 16px; font-size: 13px; color: #64748b;">
                Siswa baru belum punya akun? 
                <a href="{{ route('register') }}" style="color: #2563eb; font-weight: 700; text-decoration: none;">Klaim Akun Siswa (NISN) ↗</a>
            </div>
        </form>
    </div>

</body>
</html>
