<?php
require_once __DIR__ . '/../../config/database.php';
$logoPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= $logoPath ?>" type="image/x-icon">
    <title>403 - Akses Ditolak | Sistem Pelanggaran</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-zinc-100 min-h-screen flex items-center justify-center p-6 font-sans">
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <!-- Left Side: Illustration -->
        <div class="relative flex justify-center">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-red-400 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-orange-400 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-pulse transition-delay-2000"></div>
            <div class="float-animation">
                <h1 class="text-[180px] font-black text-transparent bg-clip-text bg-linear-to-br from-red-600 to-orange-900 leading-none select-none">
                    403
                </h1>
            </div>
        </div>

        <!-- Right Side: Content -->
        <div class="space-y-8 text-center md:text-left">
            <div class="space-y-2">
                <h2 class="text-4xl font-bold text-zinc-900 leading-tight">Area Terlarang!</h2>
                <p class="text-lg text-zinc-600">Ups! Sepertinya kamu tidak punya izin untuk masuk ke sini. Halaman ini hanya untuk mereka yang punya akses khusus.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="<?= BASE_URL ?>" class="inline-flex items-center justify-center px-8 py-4 bg-zinc-900 text-white font-semibold rounded-2xl hover:bg-zinc-800 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-zinc-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Kembali ke Beranda
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center px-8 py-4 bg-white text-zinc-700 font-semibold rounded-2xl border border-zinc-200 hover:bg-zinc-50 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 shadow-sm">
                    Kembali Sebelumnya
                </button>
            </div>

            <div class="pt-8 border-t border-zinc-200">
                <div class="flex items-center justify-center md:justify-start space-x-3">
                    <div class="p-2 glass rounded-xl">
                        <img src="<?= $logoPath ?>" alt="Logo" class="h-8 w-auto">
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-800 leading-none">SMK TI Bali Global Denpasar</p>
                        <p class="text-xs text-zinc-500 mt-1">Sistem Informasi Pelanggaran Siswa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
