<?php
    $user = filament()->auth()->user();
?>

<div class="custom-hero-banner" style="position: relative; overflow: hidden; border-radius: 24px; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #312e81 100%) !important; color: #ffffff !important; padding: 24px 28px !important; box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.3) !important; border: 1px solid rgba(59, 130, 246, 0.4) !important; margin-bottom: 24px !important; display: block !important;">
    <div style="position: absolute; right: -40px; bottom: -40px; width: 250px; height: 250px; background: rgba(96, 165, 250, 0.2); border-radius: 9999px; filter: blur(50px); pointer-events: none;"></div>
    
    <div style="position: relative; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
        
        <!-- Sisi Kiri: Profil & Sambutan -->
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 60px; height: 60px; border-radius: 16px; background: #ffffff; padding: 4px; box-shadow: 0 6px 16px rgba(0,0,0,0.25); flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <div style="width: 100%; height: 100%; border-radius: 12px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #1e40af; font-weight: 900; font-size: 24px;">
                    <?php echo e(strtoupper(substr($user->name ?? 'B', 0, 1))); ?>

                </div>
            </div>
            <div>
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: rgba(255, 255, 255, 0.15); color: #dbeafe; border: 1px solid rgba(255, 255, 255, 0.25); margin-bottom: 6px;">
                    <span style="width: 6px; height: 6px; border-radius: 9999px; background: #34d399;"></span>
                    Petugas Keuangan Sekolah
                </div>
                <h2 style="font-size: 22px; font-weight: 900; color: #ffffff !important; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                    Selamat Datang, <?php echo e($user->name); ?>! 👋
                </h2>
                <p style="font-size: 13px; color: #bfdbfe !important; margin-top: 4px; font-weight: 500;">
                    Sistem Informasi Administrasi Pembayaran Keuangan &bull; <strong style="color: #ffffff;">SMK Muhammadiyah Sekampung</strong>
                </p>
            </div>
        </div>

        <!-- Sisi Kanan: Tombol Aksi Cepat Putih & Biru -->
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <a href="<?php echo e(route('filament.admin.resources.transaksi-pembayarans.index')); ?>" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px; background: #ffffff !important; color: #1e3a8a !important; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); transition: all 0.2s;">
                <svg style="width: 16px; height: 16px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Verifikasi Transaksi
            </a>
            <a href="<?php echo e(route('filament.admin.resources.tagihan-tahunans.index')); ?>" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 12px; background: rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; font-weight: 700; font-size: 13px; text-decoration: none; border: 1px solid rgba(255, 255, 255, 0.3); transition: all 0.2s;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Data Tagihan
            </a>
        </div>

    </div>
</div>
<?php /**PATH D:\web skripsi\resources\views/filament/widgets/custom-account-widget.blade.php ENDPATH**/ ?>