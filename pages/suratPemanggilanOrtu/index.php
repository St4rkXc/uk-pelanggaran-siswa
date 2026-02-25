<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$totalPemanggilanOrtu = dbCount($pdo, 'surat_panggilan_ortu');
// Query SQL: Mencocokkan tahun dan minggu dari kolom created_at dengan waktu SEKARANG
$condition = "YEARWEEK(tanggal_surat, 1) = YEARWEEK(CURDATE(), 1)";
$totalPemanggilanOrtuMingguIni = dbCount($pdo, 'surat_panggilan_ortu', $condition);
// echo $totalPemanggilanOrtuMingguIni . " Surat";

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="flex w-dvw">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class="flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-6">
                <!-- quick information -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalPemanggilanOrtu) ?> Surat</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Surat Pemanggilan Ortu</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-siren h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalPemanggilanOrtuMingguIni) ?> Surat</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Surat Minggu Ini</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 space-y-4">
                    <!-- filter & information goes here -->
                    <div class="flex justify-between items-center">
                        <p class="font-heading-6 font-semibold text-zinc-800"> Tabel Surat Pemanggilan Orang Tua</p>
                        <div class="flex items-center">
                            <button class="button-primary">Add</button>
                        </div>
                    </div>
                    <!-- table goes here -->
                    <div></div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>