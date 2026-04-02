<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
$kopPath = BASE_URL . '/src/public/assets/img/kop_surat.jpg';


$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$jenisSurat = $_GET['jenis_surat'] ?? 'surat_pindah'; // Default ke surat pindah
$rekapBy    = $_GET['rekap_by'] ?? 'bulan';          // Default rekap per bulan

// Mapping tabel detail berdasarkan jenis surat
$tabelDetail = [
    'surat_pindah' => 'surat_pindah',
    'surat_panggilan_ortu' => 'surat_panggilan_ortu',
    'surat_perjanjian' => 'surat_perjanjian',
    'surat_pernyataan' => 'surat_pernyataan_ortu'
];

$targetTable = $tabelDetail[$jenisSurat];


// Pilih kolom pengelompokan (Grouping)
if ($rekapBy === 'jurusan') {
    $selectColumn = "sw.jurusan AS label";
    $groupBy = "GROUP BY sw.jurusan";
    $orderBy = "ORDER BY label ASC"; // Sortir alfabetis kalau jurusan
} elseif ($rekapBy === 'kelas') {
    $selectColumn = "sw.kelas AS label";
    $groupBy = "GROUP BY sw.kelas";
    $orderBy = "ORDER BY label ASC"; // Sortir alfabetis kalau kelas
} else {
    // FIX BUAT BULAN: Pake MAX(tanggal_surat) buat sorting
    $selectColumn = "DATE_FORMAT(td.tanggal_surat, '%M %Y') AS label";
    $groupBy = "GROUP BY label";
    $orderBy = "ORDER BY MAX(td.tanggal_surat) DESC";
}

// Query Utama yang sudah di-fix
$query = "SELECT $selectColumn, COUNT(*) AS total 
          FROM $targetTable td
          JOIN siswa sw ON td.id_siswa = sw.id_siswa
          $groupBy
          $orderBy";

try {
    $stmt = $pdo->query($query);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error Database: " . $e->getMessage());
}
// Query Utama: Hitung total berdasarkan Join
$query = "SELECT $selectColumn, COUNT(*) AS total 
          FROM $targetTable td
          JOIN siswa sw ON td.id_siswa = sw.id_siswa
          $groupBy";

try {
    $stmt = $pdo->query($query);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error Database: " . $e->getMessage());
}
$grandTotal = array_sum(array_column($reports, 'total'));
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Surat | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

</head>
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            background: white !important;
        }

        #print-section {
            font-family: 'Times New Roman', serif;
        }

        @page {
            size: A4;
            margin: 1cm;
        }
    }
</style>

<body class="flex w-vdw">
    <div class="flex w-full">
        <aside class="sidebar print:hidden">
            <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        </aside>
        <div class="flex-1">
            <div class="print:hidden">
                <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            </div>
            <main class="p-8 print:hidden">


                <div class="mb-8 no-print">
                    <h1 class="text-2xl font-black text-zinc-950 uppercase tracking-tight">Sistem Laporan & Rekapitulasi</h1>
                    <p class="text-zinc-500 text-sm">Pilih jenis surat dan kategori rekap untuk melihat data.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm mb-8 no-print">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Jenis Surat</label>
                            <select name="jenis_surat" class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl font-bold text-zinc-900 outline-none focus:ring-2 focus:ring-orange-600 transition-all">
                                <option value="surat_pindah" <?= $jenisSurat == 'surat_pindah' ? 'selected' : '' ?>>Surat Pindah Sekolah</option>
                                <option value="surat_panggilan_ortu" <?= $jenisSurat == 'surat_panggilan_ortu' ? 'selected' : '' ?>>Surat Panggilan Ortu</option>
                                <option value="surat_perjanjian" <?= $jenisSurat == 'surat_perjanjian' ? 'selected' : '' ?>>Surat Perjanjian Siswa</option>
                                <option value="surat_pernyataan" <?= $jenisSurat == 'surat_pernyataan' ? 'selected' : '' ?>>Surat Pernyataan Orang Tua</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Rekap Berdasarkan</label>
                            <select name="rekap_by" class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl font-bold text-zinc-900 outline-none focus:ring-2 focus:ring-orange-600 transition-all">
                                <option value="bulan" <?= $rekapBy == 'bulan' ? 'selected' : '' ?>>Bulan & Tahun</option>
                                <option value="jurusan" <?= $rekapBy == 'jurusan' ? 'selected' : '' ?>>Jurusan</option>
                                <option value="kelas" <?= $rekapBy == 'kelas' ? 'selected' : '' ?>>Kelas</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-3 bg-zinc-900 text-white rounded-xl font-bold hover:bg-black transition-all">
                                Tampilkan Data
                            </button>
                            <button onclick="window.print()" class="no-print px-5 py-3 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition-all flex items-center gap-2">
                                <span class="icon-printer"></span> Cetak Laporan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden " id="printable-area">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                        <h2 class="font-black text-zinc-900 uppercase">
                            Rekap <?= str_replace('_', ' ', $jenisSurat) ?> Per <?= ucfirst($rekapBy) ?>
                        </h2>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-zinc-100/50 border-b border-zinc-200">
                            <tr>
                                <th class="px-8 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Kategori (<?= $rekapBy ?>)</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Jumlah Terbit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <?php foreach ($reports as $r): ?>
                                <tr class="hover:bg-zinc-50/50 transition-all">
                                    <td class="px-8 py-5 font-bold text-zinc-900 italic uppercase"><?= $r['label'] ?></td>
                                    <td class="px-8 py-5 text-right font-black text-2xl text-zinc-950">
                                        <?= $r['total'] ?> <span class="text-[10px] text-zinc-400 font-normal">Surat</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="2" class="p-16 text-center">
                                        <p class="text-zinc-400 italic">Data tidak ditemukan untuk kategori ini.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>

            <!-- PRINT SECTION -->
            <div id="print-section" class="hidden print:block w-full bg-white max-w-[21cm] min-h-[29.7cm] mx-auto p-12">
                <img src="<?= $kopPath ?>" alt="Kop Surat Sekolah" class="w-full h-auto object-contain mb-6">

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase underline tracking-tight text-zinc-950">
                        Laporan Rekapitulasi <?= str_replace('_', ' ', $jenisSurat) ?>
                    </h2>
                    <p class="text-sm mt-1">Berdasarkan <?= ucfirst($rekapBy) ?></p>
                </div>

                <table class="w-full text-left border-collapse border border-zinc-900 mb-8">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 border border-zinc-900 font-bold text-zinc-900 uppercase">Kategori (<?= $rekapBy ?>)</th>
                            <th class="px-4 py-3 border border-zinc-900 font-bold text-zinc-900 uppercase text-center">Jumlah Terbit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td class="px-4 py-3 border border-zinc-900 text-zinc-900"><?= $r['label'] ?></td>
                                <td class="px-4 py-3 border border-zinc-900 text-zinc-900 text-center font-bold">
                                    <?= $r['total'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="2" class="px-4 py-3 border border-zinc-900 text-center">
                                    <p class="text-zinc-600 italic">Data tidak ditemukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="px-4 py-3 border border-zinc-900 font-bold text-right">TOTAL KESELURUHAN</td>
                            <td class="px-4 py-3 border border-zinc-900 font-bold text-center"><?= $grandTotal ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-end mt-12">
                    <!-- <div class="text-center">
                        <p class="mb-20 text-zinc-900">Denpasar, <?= date('d M Y') ?></p>
                        <p class="font-bold underline text-zinc-900"><?= htmlspecialchars($currentUser['nama']) ?></p>
                        <p class="text-sm text-zinc-900"><?= ucfirst(str_replace('_', ' ', $currentUser['role'])) ?></p>
                    </div> -->
                </div>
            </div>
            <!-- END PRINT SECTION -->

        </div>

    </div>

</body>

</html>

<script>
    function printLaporan() {
        window.print();
    }
</script>