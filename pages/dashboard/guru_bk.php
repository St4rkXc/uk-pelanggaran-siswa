<?php
session_start();
$requiredRole = ['guru_bk'];

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

$totalPelanggaran = dbCount($pdo, 'pelanggaran');

// Query for Student List (Sorted by point ascending)
$querySiswa = "SELECT * FROM siswa ORDER BY point ASC LIMIT 20";
$siswaList = $pdo->query($querySiswa)->fetchAll(PDO::FETCH_ASSOC);

// Query for Violation List (Mirroring pelanggaran/index.php logic)
$queryPelanggaran = "SELECT 
                        p.id_pelanggaran, 
                        p.keterangan, 
                        p.tanggal_pelaporan,
                        s.nama_siswa, 
                        s.kelas, 
                        jp.nama_jenis, 
                        jp.point as bobot_poin,
                        u.name as nama_pelapor
                    FROM pelanggaran p
                    JOIN siswa s ON p.id_siswa = s.id_siswa
                    JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
                    JOIN users u ON p.pelapor = u.id_users
                    ORDER BY p.tanggal_pelaporan DESC 
                    LIMIT 20";
$pelanggaranList = $pdo->query($queryPelanggaran)->fetchAll(PDO::FETCH_ASSOC);

$querySuratLog = "SELECT 
                jenis_surat, 
                nomor_surat,
                CASE 
                    WHEN jenis_surat = 'surat_perjanjian' THEN (SELECT tanggal_surat FROM surat_perjanjian WHERE id_perjanjian = s.id_jenis_surat)
                    WHEN jenis_surat = 'surat_pindah' THEN (SELECT tanggal_surat FROM surat_pindah WHERE id_surat_pindah = s.id_jenis_surat)
                    WHEN jenis_surat = 'surat_panggilan_ortu' THEN (SELECT tanggal_surat FROM surat_panggilan_ortu WHERE id_surat_panggilan_ortu = s.id_jenis_surat)
                END as tanggal_surat
               FROM surat s
               ORDER BY id_surat DESC 
               LIMIT 10";
$suratLog = $pdo->query($querySuratLog)->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

</head>

<body class="bg-zinc-50 overflow-x-hidden">
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
                            <span class="icon-siren h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalPelanggaran ?> Pelanggaran</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Pelanggaran Tercatat</p>
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

                    <div class=" dropdown dropdown-start rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100 cursor-pointer">
                        <div tabindex="0" role="button" class="flex gap-3">
                            <span class="icon-siren h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Pelanggaran</h6>
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm mt-4">
                            <li><a href="<?php echo BASE_URL; ?>/pages/pelanggaran/">Pelanggaran</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/pages/jenis_pelanggaran/">Jenis Pelanggaran</a></li>

                        </ul>
                    </div>

                    <div class=" dropdown dropdown-start rounded-lg border border-zinc-300 p-4 transition-all duration-200 hover:bg-zinc-100 cursor-pointer">
                        <div tabindex="0" role="button" class="flex gap-3">
                            <span class="icon-paperclip h-6 w-6 text-zinc-700"></span>
                            <h6 class="font-paragraph-16 font-semibold text-zinc-800">Kelola Surat</h6>
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm mt-4">
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
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm mt-4">
                            <li><a href="<?php echo BASE_URL; ?>/pages/laporan_siswa/">Laporan Poin Siswa</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/pages/laporan_surat/">Laporan Surat Keluar</a></li>
                        </ul>
                    </div>
                </div>
                <div class="grid grid-cols-6 w-full mt-6 gap-4">
                    <div class="col-span-4 rounded-2xl border border-zinc-300 p-6 h-fit ">
                        <!-- Custom Tabs Header -->
                        <div class="flex items-center gap-4 border-b border-zinc-200 mb-6 ">
                            <button onclick="switchTab('tab-siswa')" id="btn-siswa" class="tab-btn  py-3 font-semibold text-sm transition-all duration-200 border-b-2 border-zinc-900 text-zinc-900">
                                📋 Data Siswa
                            </button>
                            <button onclick="switchTab('tab-pelanggaran')" id="btn-pelanggaran" class="tab-btn  py-3 font-semibold text-sm transition-all duration-200 border-b-2 border-transparent text-zinc-400 hover:text-zinc-600">
                                ⚠️ Pelanggaran
                            </button>
                        </div>

                        <!-- Tab Content 1: Data Siswa -->
                        <div id="tab-siswa" class="tab-pane block">
                            <div class="overflow-x-auto">
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

                        <!-- Tab Content 2: Pelanggaran -->
                        <div id="tab-pelanggaran" class="tab-pane hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left table-auto">
                                    <thead>
                                        <tr class="text-zinc-800 font-paragraph-16 font-medium">
                                            <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500">Nama/Kelas</th>
                                            <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500">Jenis/Keterangan</th>
                                            <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 text-center">Poin</th>
                                            <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 text-right">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100">
                                        <?php foreach ($pelanggaranList as $p): ?>
                                            <tr class="border-b border-b-zinc-50 hover:bg-zinc-50 transition-colors">
                                                <td class="py-4">
                                                    <div class="flex flex-col">
                                                        <span class="text-zinc-800 font-bold font-paragraph-14"><?= htmlspecialchars($p['nama_siswa']); ?></span>
                                                        <span class="text-zinc-400 text-xs font-medium uppercase tracking-tighter"><?= htmlspecialchars($p['kelas']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="py-4">
                                                    <div class="flex flex-col max-w-[250px]">
                                                        <span class="text-zinc-700 font-semibold text-sm"><?= htmlspecialchars($p['nama_jenis']); ?></span>
                                                        <span class="text-zinc-400 text-[11px] truncate" title="<?= htmlspecialchars($p['keterangan']); ?>"><?= htmlspecialchars($p['keterangan']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="py-4 text-center">
                                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-black italic">-<?= htmlspecialchars($p['bobot_poin']); ?></span>
                                                </td>
                                                <td class="py-4 text-right text-zinc-400 font-medium text-xs">
                                                    <?= date('d/m/y H:i', strtotime($p['tanggal_pelaporan'])); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 rounded-2xl border border-zinc-300 p-6 h-fit">
                        <h5 class="font-paragraph-16 font-semibold text-zinc-800">Log Surat Terbaru</h5>
                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                        <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500">Jenis Surat</th>
                                        <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 text-center">No. Surat</th>
                                        <th class="py-3 border-b border-zinc-200 text-[12px] font-bold uppercase tracking-wider text-zinc-500 text-right">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($suratLog as $rowSurat):
                                        $label = str_replace('_', ' ', $rowSurat['jenis_surat']);
                                        $label = ucwords($label);
                                    ?>
                                        <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 transition-colors select-none">
                                            <td class="py-4 font-paragraph-14 font-medium text-zinc-600">
                                                <div class="flex flex-col">
                                                    <span class="text-zinc-800 font-semibold select-none"><?= htmlspecialchars($label); ?></span>
                                                </div>
                                            </td>
                                            <td class="py-4 font-paragraph-14 font-medium text-zinc-600 text-center">
                                                <span class="px-2 py-1 bg-zinc-100 rounded text-zinc-500 text-xs">#<?= htmlspecialchars($rowSurat['nomor_surat']); ?></span>
                                            </td>
                                            <td class="py-4 font-paragraph-14 font-medium text-zinc-400 text-right">
                                                <?= date('d M Y', strtotime($rowSurat['tanggal_surat'] ?? 'now')); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

<script>
    function switchTab(tabId) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('block');
        });

        // Show the selected tab pane
        const selectedPane = document.getElementById(tabId);
        selectedPane.classList.remove('hidden');
        selectedPane.classList.add('block');

        // Reset all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-zinc-900', 'text-zinc-900');
            btn.classList.add('border-transparent', 'text-zinc-400');
        });

        // Update active button styling
        const activeBtnId = tabId === 'tab-siswa' ? 'btn-siswa' : 'btn-pelanggaran';
        const activeBtn = document.getElementById(activeBtnId);
        activeBtn.classList.add('border-zinc-900', 'text-zinc-900');
        activeBtn.classList.remove('border-transparent', 'text-zinc-400');
    }
</script>

</html>