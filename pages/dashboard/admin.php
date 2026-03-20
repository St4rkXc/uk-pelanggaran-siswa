<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';


// inget include database dlu ya biar bisa make $pdo
$totalSiswa = dbCount($pdo, 'Siswa');
$totalGuru = dbCount($pdo, 'Users', "role IN ('admin', 'guru_bk', 'guru_mapel')");
$totalSurat = dbCount($pdo, 'Surat');
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
    <title>Dashboard Admin</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

</head>

<body class="bg-zinc-50 w-dvw overflow-x-hidden">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class=" flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-6  gap-6">
                <div class="flex gap-4">
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalSiswa ?> Siswa</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Siswa Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalGuru ?> Guru</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Guru Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-paperclip h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalSurat ?> Surat</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total surat Tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4 mt-6">
                    <a href="<?php echo BASE_URL; ?>/pages/siswa" class="group flex justify-between gap-3 rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100">
                        <div class="flex gap-4">
                            <span class="icon-user h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Siswa</h6>
                        </div>
                        <span class="icon-arrow-up-right h-5 w-5 rotate-45 transition-all duration-200 group-hover:rotate-0 group-hover:scale-110"></span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>/pages/user" class="group flex justify-between gap-3 rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100">
                        <div class="flex gap-4">
                            <span class="icon-user h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Users</h6>
                        </div>
                        <span class="icon-arrow-up-right h-5 w-5 rotate-45 transition-all duration-200 group-hover:rotate-0 group-hover:scale-110"></span>
                    </a>

                    <div class=" dropdown dropdown-start rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100 cursor-pointer">
                        <div tabindex="0" role="button" class="flex gap-3">
                            <span class="icon-paperclip h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Surat</h6>
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li><a href="<?php echo BASE_URL; ?>/pages/suratPemanggilanOrtu/">Surat Pemanggilan Ortu</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/pages/suratPerjanjian/">Surat Perjanjian Siswa</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/pages/suratPindah/">Surat Pindah</a></li>
                        </ul>
                    </div>

                    <div class=" dropdown dropdown-start rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100 cursor-pointer">
                        <div tabindex="0" role="button" class="flex gap-3">
                            <span class="icon-report h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Laporan</h6>
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li><a href="<?php echo BASE_URL; ?>/pages/laporan_siswa/">Laporan Poin Siswa</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/pages/laporan_surat/">Laporan Surat Keluar</a></li>
                        </ul>
                    </div>
                </div>
                <div class=" grid grid-cols-6 w-full mt-6 gap-4">
                    <div class="col-span-4 rounded-2xl border border-zinc-300 p-6 h-fit">
                        <div class="flex justify-between items-center">
                            <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel data siswa</h5>
                            <a href="<?php echo $siswaPath; ?>" class="icon-arrow-up-right rotate-45 hover:rotate-0 transition-all duration-200 hover:scale-110 w-6 h-6"></a>
                        </div>
                        <div class="mt-3">
                            <table class="w-full text-left table-auto">
                                <thead class="p-4">
                                    <tr class=" text-zinc-800 font-paragraph-16 font-medium  ">
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">Nama</th>
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">Kelas</th>
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">NIS</th>
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">NISN</th>
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">Poin</th>
                                        <th class=" py-3  border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 ">Jurusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    <?php
                                    $no = 1;
                                    // Fetch data pakai PDO
                                    $stmt = $pdo->query("SELECT * FROM siswa ORDER BY point LIMIT 20");
                                    while ($row = $stmt->fetch()):
                                    ?>
                                        <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 hover:cursor-pointer duration-200  ">
                                            <td class=" py-4 text-zinc-800 font-medium flex gap-4 items-center">
                                                <div class="p-3 bg-zinc-100 border border-zinc-300 rounded-full flex justify-center items-center">
                                                    <span class="icon-user h-4 w-4 text-zinc-500"></span>
                                                </div>
                                                <?= htmlspecialchars($row['nama_siswa']); ?>
                                            </td>
                                            <td class=" py-4 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                            <td class=" py-4 text-zinc-600"><?= htmlspecialchars($row['nis']); ?></td>
                                            <td class=" py-4 text-zinc-600"><?= htmlspecialchars($row['nisn']); ?></td>
                                            <td class=" py-4  ">
                                                <div class="text-center p-1 rounded-full text-[12px] font-medium <?php
                                                                                                                    $point = (int)$row['point'];
                                                                                                                    if ($point < 20) {
                                                                                                                        echo 'bg-red-200 text-red-800';
                                                                                                                    } elseif ($point < 50) {
                                                                                                                        echo 'bg-yellow-200 text-yellow-800';
                                                                                                                    } else {
                                                                                                                        echo 'bg-green-200 text-green-800';
                                                                                                                    }
                                                                                                                    ?>">
                                                    <?= htmlspecialchars($row['point']); ?>
                                                </div>
                                            </td>
                                            <td class=" py-4 pl-2 text-zinc-600"><?= htmlspecialchars($row['jurusan']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-2 rounded-2xl border border-zinc-300 p-6">
                        <div class="flex justify-between items-center pb-4">
                            <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel Guru</h5>
                            <a href="<?php echo $userPath; ?>" class="icon-arrow-up-right rotate-45 hover:rotate-0 transition-all duration-200 hover:scale-110 w-6 h-6"></a>
                        </div>
                        <table class="w-full text-left ">
                            <thead>
                                <tr class="bg-zinc-50/50">
                                    <th class="py-3 px-4 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500">Nama User</th>
                                    <th class="py-3 px-4 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500">Access Level</th>
                                    <th class="py-3 px-4 border-b border-zinc-200"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM users WHERE role IN ('admin', 'guru_bk', 'guru_mapel') ORDER BY role ASC");
                                while ($row = $stmt->fetch()):
                                    // Logic warna badge berdasarkan role
                                    $roleStyles = [
                                        'admin' => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'guru_bk' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'guru_mapel' => 'bg-zinc-100 text-zinc-700 border-zinc-200'
                                    ];
                                    $roleName = [
                                        'admin' => 'Administrator',
                                        'guru_bk' => 'Guru BK',
                                        'guru_mapel' => 'Guru Mapel'
                                    ];
                                    $style = $roleStyles[$row['role']] ?? 'bg-zinc-50 text-zinc-600 border-zinc-200';
                                    $label = $roleName[$row['role']] ?? $row['role'];
                                ?>
                                    <tr class="border-b border-b-zinc-300">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-300  transition-colors">
                                                    <span class="text-xs font-bold text-zinc-500"><?= strtoupper(substr($row['name'], 0, 2)); ?></span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-semibold text-zinc-900 leading-tight"><?= htmlspecialchars($row['name']); ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-bold tracking-tight <?= $style ?>">
                                                <?= htmlspecialchars($label); ?>
                                            </span>
                                        </td>

                                        <td class="py-4 px-4 text-right">
                                            <button class="opacity-0 group-hover:opacity-100 transition-opacity p-2 hover:bg-zinc-200 rounded-md text-zinc-500">
                                                <span class="icon-settings text-sm"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>