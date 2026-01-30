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
]


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

</head>

<body class="bg-zinc-50 w-dvw">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class=" flex-1">
            <header class="flex justify-between items-center px-6 py-4 border-b border-zinc-300">
                <p class="font-paragraph-20 font-semibold text-zinc-800">Selamat Datang, <?= htmlspecialchars($currentUser['role']); ?></p>
                <div class="flex justify-end items-center gap-4">
                    <p class="font-paragraph-16 font-semibold text-zinc-800"><?= htmlspecialchars($currentUser['nama']); ?></p>
                    <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center">
                        <span class="icon-user h-6 w-6 "></span>
                    </div>
                </div>
            </header>
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
                <div class="grid grid-cols-6 w-full mt-6 gap-4">
                    <div class="col-span-4 rounded-2xl border border-zinc-300 p-6 h-fit">
                        <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel data siswa</h5>
                        <div class="mt-3">
                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                        <th class="">No</th>
                                        <th class="">Nama</th>
                                        <th class="">Kelas</th>
                                        <th class="">NIS</th>
                                        <th class="">NISN</th>
                                        <th class="">Poin</th>
                                        <th class="">Jurusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    <?php
                                    $no = 1;
                                    // Fetch data pakai PDO
                                    $stmt = $pdo->query("SELECT * FROM siswa ORDER BY id_siswa ");
                                    while ($row = $stmt->fetch()):
                                    ?>
                                        <tr class="border-b border-b-zinc-300 ">
                                            <td class=" py-3 text-zinc-700"><?= $no++; ?></td>
                                            <td class=" py-3 text-zinc-800 font-medium"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                            <td class=" py-3 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                            <td class=" py-3 text-zinc-600"><?= htmlspecialchars($row['nis']); ?></td>
                                            <td class=" py-3 text-zinc-600"><?= htmlspecialchars($row['nisn']); ?></td>
                                            <td class=" py-3 text-zinc-600"><?= htmlspecialchars($row['point']); ?></td>
                                            <td class=" py-3 text-zinc-600"><?= htmlspecialchars($row['jurusan']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if ($no === 1): ?>
                                        <tr>
                                            <td colspan="7" class="p-6 text-center text-zinc-500">Data masih kosong, bro.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-2 rounded-2xl border border-zinc-300 p-6">
                        <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel Guru</h5>
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                    <th class="">Nama</th>
                                    <th class="">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                // Fetch data pakai PDO
                                $stmt = $pdo->query("SELECT * FROM users WHERE role IN ('admin', 'guru_bk', 'guru_mapel') ORDER BY id_users ");
                                while ($row = $stmt->fetch()):
                                ?>
                                    <tr class="border-b border-b-zinc-300 ">
                                        <td class="py-3 font-paragraph-14 font-medium text-zinc-600 "><?= htmlspecialchars($row['name']); ?></td>
                                        <td class="py-3 font-paragraph-14 font-medium text-zinc-600 "><?= htmlspecialchars($row['role']); ?></td>
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