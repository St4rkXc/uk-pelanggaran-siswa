<?php
require_once __DIR__ . '/../../../config/database.php';
$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
$dashboardPath = BASE_URL . '/pages/dashboard/' . $_SESSION['role'] . ".php";
$siswaPath = BASE_URL . '/pages/siswa/';
$userPath = BASE_URL . '/pages/user/';
$logoutPath = BASE_URL . '/auth/logout.php';
$jenisPelanggaranPath = BASE_URL . '/pages/jenis_pelanggaran/';
$pelanggaranPath = BASE_URL . '/pages/pelanggaran/';
$suratPemanggilanOrtuPath = BASE_URL . '/pages/suratPemanggilanOrtu/';
$suratPerjanjianPath = BASE_URL . '/pages/suratPerjanjian/';
$suratPernyataanOrtuPath = BASE_URL . '/pages/suratPernyataanOrtu/';
$suratPindahPath = BASE_URL . '/pages/suratPindah/';
$laporanSurat = BASE_URL . '/pages/laporan_surat/';
$laporanSiswa = BASE_URL . '/pages/laporan_siswa/';



// current url
$currentUrl = $_SERVER['PHP_SELF'];
$isActiveDashboard = (strpos($currentUrl, 'pages/dashboard/') !== false) ? 'sidebar-link-active' : '';
$isActiveSiswa = (strpos($currentUrl, 'pages/siswa/') !== false) ? 'sidebar-link-active' : '';
$isActiveUser = (strpos($currentUrl, 'pages/user/') !== false) ? 'sidebar-link-active' : '';
$isActiveJenisPelanggaran = (strpos($currentUrl, 'pages/jenis_pelanggaran/') !== false) ? 'sidebar-link-active' : '';
$isActivePelanggaran = (strpos($currentUrl, 'pages/pelanggaran/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPemanggilanOrtu = (strpos($currentUrl, 'pages/suratPemanggilanOrtu/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPerjanjian = (strpos($currentUrl, 'pages/suratPerjanjian/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPernyataanOrtu = (strpos($currentUrl, 'pages/suratPernyataanOrtu/') !== false) ? 'sidebar-link-active' : '';
$isActiveSuratPindah = (strpos($currentUrl, 'pages/suratPindah/') !== false) ? 'sidebar-link-active' : '';
$isActiveLaporanSurat = (strpos($currentUrl, 'pages/laporan_surat/') !== false) ? 'sidebar-link-active' : '';
$isActiveLaporanSiswa = (strpos($currentUrl, 'pages/laporan_siswa/') !== false) ? 'sidebar-link-active' : '';

?>

<div class=" bg-zinc-100 border-r-2 border-r-zinc-200 w-fit sticky top-0 h-dvh flex flex-col justify-between py-8 px-10 ">
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
                        <a href="<?php echo $userPath; ?>" class="sidebar-link <?php echo $isActiveUser; ?>">
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
                                    <a href="<?php echo $suratPernyataanOrtuPath; ?>" class="sidebar-link <?php echo $isActiveSuratPernyataanOrtu; ?>">
                                        <p class="font-paragraph-14 text-700">Pernyataan Orang Tua</p>
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
                            <ul class="space-y-2">
                                <li>
                                    <a href="<?php echo $laporanSurat; ?>" class="sidebar-link <?php echo $isActiveLaporanSurat; ?>">
                                        <p class="font-paragraph-14 text-700">Surat</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $laporanSiswa; ?>" class="sidebar-link <?php echo $isActiveLaporanSiswa; ?>">
                                        <p class="font-paragraph-14 text-700">Siswa</p>
                                    </a>
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