<?php
require_once __DIR__ . '/../../../config/database.php';
$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
$dashboardPath = BASE_URL . '/pages/dashboard/' . $_SESSION['role'] . ".php";
$siswaPath = BASE_URL . '/pages/siswa/';
$logoutPath = BASE_URL . '/auth/logout.php';
$jenisPelanggaranPath = BASE_URL . '/pages/jenis_pelanggaran/';
$pelanggaranPath = BASE_URL . '/pages/pelanggaran/';
$suratPemanggilanOrtuPath = BASE_URL . '/pages/suratPemanggilanOrtu/';
$suratPerjanjianPath = BASE_URL . '/pages/suratPerjanjian/';
$suratPindahPath = BASE_URL . '/pages/suratPindah/';


// current url
$current_url = $_SERVER['PHP_SELF'];
$isActiveDashboard = (strpos($current_url, 'pages/dashboard/') !== false) ? 'sidebar-link-active' : '';
$isActiveSiswa = (strpos($current_url, 'pages/siswa/') !== false) ? 'sidebar-link-active' : '';
$isActiveUser = (strpos($current_url, 'pages/user/') !== false) ? 'sidebar-link-active' : '';
$isActiveJenisPelanggaran = (strpos($current_url, 'pages/jenis_pelanggaran/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPemanggilanOrtu = (strpos($current_url, 'pages/suratPemanggilanOrtu/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPerjanjian = (strpos($current_url, 'pages/suratPerjanjian/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPindah = (strpos($current_url, 'pages/suratPindah/') !== false) ? 'sidebar-link-active' : '';



?>

<div class="bg-zinc-100 border-r-2 border-r-zinc-200 w-fit sticky h-dvh flex flex-col justify-between py-8 px-10 ">
    <div>
        <div class="space-y-1">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <p class="font-paragraph-14 font-semibold text-zinc-500">Manajemen Pelanggaran</p>
        </div>
        <div class="mt-6 space-y-4">
            <div class="space-y-2">
                <p class="font-paragraph-12 font-semibold text-zinc-500">Utama</p>
                <div class="space-y-1">
                    <a href="<?php echo $dashboardPath; ?>" class="sidebar-link <?php echo $isActiveDashboard; ?>">
                        <div class="icon-home h-5 w-5"></div>
                        <div class="font-paragraph-14 text-700">Dashboard</div>
                    </a>
                    <a href="<?php echo $siswaPath; ?>" class="sidebar-link <?php echo $isActiveSiswa; ?>">
                        <div class="icon-siswa h-5 w-5"></div>
                        <div class="font-paragraph-14 text-700">Data Siswa</div>
                    </a>
                    <a href=""></a>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a href="<?php echo BASE_URL . '/pages/user/'; ?>" class="sidebar-link <?php echo $isActiveUser; ?>">
                            <div class="icon-case h-5 w-5"></div>
                            <div class="font-paragraph-14 text-700">Data User</div>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="space-y-2">
                <p class="font-paragraph-12 font-semibold text-zinc-500">Pelanggaran</p>
                <div class="space-y-1">
                    <a href="<?php echo $jenisPelanggaranPath; ?>" class="sidebar-link <?php echo $isActiveJenisPelanggaran; ?>">
                        <div class="icon-siren h-5 w-5"></div>
                        <div class="font-paragraph-14 text-700">Jenis Pelanggaran</div>
                    </a>
                    <a href="<?php echo $pelanggaranPath; ?>" class="sidebar-link <?php echo $isActivePelanggaran; ?>">
                        <div class="icon-siren h-5 w-5"></div>
                        <div class="font-paragraph-14 text-700"> Pelanggaran</div>
                    </a>
                </div>
            </div>
            <div class="space-y-2">
                <p class="font-paragraph-12 font-semibold text-zinc-500">Surat dan Laporan </p>
                <ul class="menu rounded-box w-56 bg-zinc-100 p-0">
                    <li>
                        <details open>
                            <summary class="sidebar-link rounded-lg py-2">
                                <div class="icon-paperclip h-5 w-5"></div>
                                Surat
                            </summary>
                            <ul class="space-y-2">
                                <li>
                                    <a href="<?php echo $suratPemanggilanOrtuPath; ?>" class="sidebar-link <?php echo $isActiveSuratPemanggilanOrtu; ?>">
                                        <p class="font-paragraph-14 text-700">Pemanggilan Orang Tua</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $suratPerjanjianPath; ?>" class="sidebar-link <?php echo $isActiveSuratPerjanjian; ?>">
                                        <p class="font-paragraph-14 text-700">Perjanjian</p>
                                    </a>
            
                                </li>
                                <li>
                                    <a href="<?php echo $suratPindahPath; ?>" class="sidebar-link <?php echo $isActiveSuratPindah; ?>">
                                        <p class="font-paragraph-14 text-700">Pindah</p>
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>
                <ul class="menu rounded-box w-56 bg-zinc-100 p-0">
                    <li>
                        <details open>
                            <summary class="sidebar-link rounded-lg py-2">
                                <div class="icon-report h-5 w-5"></div>
                                Laporan
                            </summary>
                            <ul>
                                <li>
                                    <div class="sidebar-link">
                                        <a class="font-paragraph-14 text-700">Pemanggilan Orang Tua</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="sidebar-link">
                                        <a class="font-paragraph-14 text-700">Perjanjian</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="sidebar-link">

                                        <a class="font-paragraph-14 text-700">Pindah</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="sidebar-link">

                                        <a class="font-paragraph-14 text-700">Siswa</a>
                                    </div>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mt-auto">
        <a href="<?php echo $logoutPath; ?>" class="sidebar-link font-paragraph-14">Logout</a>
    </div>
</div>