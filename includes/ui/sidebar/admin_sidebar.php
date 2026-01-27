<?php
require_once __DIR__ . '/../../../config/database.php';
$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
$dashboardPath = BASE_PATH . '/dashboard/guru_bk.php';
$logoutPath = BASE_URL . '/auth/logout.php';
?>

<div class="bg-zinc-100 border-r-2 border-r-zinc-200 w-fit sticky h-dvh flex flex-col justify-between py-8 px-10 ">
    <div>
        <div class="space-y-1">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-[50px]  ">
            </div>
            <p class="font-paragraph-14 font-semibold text-zinc-500">Manajemen Pelanggaran</p>
        </div>
        <div class="mt-6 space-y-4">
            <div class="space-y-2">
                <p class="font-paragraph-12 font-semibold text-zinc-500">Utama</p>
                <div class="space-y-1">
                    <div class="sidebar-link sidebar-link-active">
                        <div class="icon-home h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Dashboard</a>
                    </div>
                    <div class="sidebar-link">
                        <div class="icon-siswa h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Data Siswa</a>
                    </div>
                    <div class="sidebar-link">
                        <div class="icon-case h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Data User</a>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <p class="font-paragraph-12 font-semibold text-zinc-500">Surat </p>
                <div class="space-y-1">
                    <div class="sidebar-link">
                        <div class="icon-paperclip h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Pemanggilan Orang Tua</a>
                    </div>
                    <div class="sidebar-link">
                        <div class="icon-paperclip h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Perjanjian</a>
                    </div>
                    <div class="sidebar-link">
                        <div class="icon-paperclip h-5 w-5"></div>
                        <a class="font-paragraph-14 text-700">Pindah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-auto">
        <a href="<?php echo $logoutPath; ?>" class="sidebar-link font-paragraph-14">Logout</a>
    </div>
</div>