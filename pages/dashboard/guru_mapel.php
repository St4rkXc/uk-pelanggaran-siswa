<?php
session_start();
$requiredRole = ['guru_mapel'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$deletePelanggaranPath = BASE_URL . '/pages/pelanggaran/delete_process.php';
$logoPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
$addPelanggaranPath = BASE_URL . '/pages/pelanggaran/add_process.php';

// inget include database dlu ya biar bisa make $pdo
$totalSiswa = dbCount($pdo, 'Siswa');
$totalGuru = dbCount($pdo, 'Users', "role IN ('admin', 'guru_bk', 'guru_mapel')");
$totalSurat = dbCount($pdo, 'Surat');
$currentUserId = $_SESSION['id_users']; // Pastiin session ini ada pas login
$totalLaporanSaya = dbCount($pdo, 'pelanggaran', "pelapor = ?", [$currentUserId]);

$selectedKelas = trim($_GET['kelas'] ?? '');
$searchNama = trim($_GET['search_nama'] ?? '');

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];


$queryKelasFilter = "SELECT DISTINCT sw.kelas
                    FROM pelanggaran p
                    JOIN siswa sw ON p.id_siswa = sw.id_siswa
                    WHERE p.pelapor = ?
                    ORDER BY sw.kelas ASC";
$stmtKelasFilter = $pdo->prepare($queryKelasFilter);
$stmtKelasFilter->execute([$currentUserId]);
$kelasFilterOptions = $stmtKelasFilter->fetchAll(PDO::FETCH_COLUMN);


// Ambil riwayat laporan terbaru yang dibuat oleh guru ini
$queryRiwayat = "SELECT p.*, sw.nama_siswa, sw.kelas, jp.nama_jenis 
                 FROM pelanggaran p
                 JOIN siswa sw ON p.id_siswa = sw.id_siswa
                 JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
                 WHERE p.pelapor = ?";

$queryParams = [$currentUserId];

if ($selectedKelas !== '') {
    $queryRiwayat .= " AND sw.kelas = ?";
    $queryParams[] = $selectedKelas;
}

if ($searchNama !== '') {
    $queryRiwayat .= " AND sw.nama_siswa LIKE ?";
    $queryParams[] = '%' . $searchNama . '%';
}

$queryRiwayat .= "
                 ORDER BY p.tanggal_pelaporan DESC 
                 LIMIT 10";

$stmtRiwayat = $pdo->prepare($queryRiwayat);
$stmtRiwayat->execute($queryParams);
$riwayatLaporan = $stmtRiwayat->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru Mapel - <?= htmlspecialchars($currentUser['nama']) ?></title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="bg-zinc-50 overflow-x-hidden h-screen">
    <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
    <main class="container mx-auto bg-zinc-50 p-6">
        <div class="grid grid-cols-4 gap-4">

            <div class="flex items-center hover:-translate-y-1 hover:shadow-xl transition-all cursor-pointer duration-200 rounded-lg border border-zinc-300 p-6 gap-6 bg-white">
                <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center h-fit w-fit">
                    <span class="icon-siren h-6 w-6 text-zinc-600 rounded-full"></span>
                </div>
                <div>
                    <h5 class="font-heading-5 font-semibold text-zinc-800"><?= $totalLaporanSaya ?> Laporan</h5>
                    <p class="font-paragraph-14 font-medium text-zinc-600">Total Pelaporan</p>
                </div>
            </div>
        </div>
        <div class="p-6 rounded-lg border border-zinc-300 bg-white mt-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h5 class="font-paragraph-18 font-semibold text-zinc-800 tracking-tight">Riwayat Pelaporan Saya</h5>
                    <p class="text-xs text-zinc-500 font-medium">Menampilkan 10 laporan pelanggaran terakhir yang dibuat</p>
                </div>
                <button class="button-primary" onclick="modal_add_pelanggaran.showModal()">Laporan Baru</button>
            </div>

            <form id="riwayat-filter-form" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
                <div class="form-control md:col-span-1">
                    <label class="label text-xs font-bold text-zinc-700">Filter Kelas</label>
                    <select id="filter-kelas" name="kelas" class="my-select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelasFilterOptions as $kelasOption): ?>
                            <option value="<?= htmlspecialchars($kelasOption) ?>" <?= $selectedKelas === $kelasOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelasOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-control md:col-span-2">
                    <label class="label text-xs font-bold text-zinc-700 placeholder:text-xs">Cari Nama Siswa</label>
                    <input
                        id="filter-search-nama"
                        type="text"
                        name="search_nama"
                        value="<?= htmlspecialchars($searchNama) ?>"
                        placeholder="Contoh: Andi"
                        class="my-input input-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" />
                </div>

                
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-100">
                            <th class="py-4 px-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Tanggal</th>
                            <th class="py-4 px-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Siswa</th>
                            <th class="py-4 px-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Pelanggaran</th>
                            <th class="py-4 px-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        <?php if (empty($riwayatLaporan)): ?>
                            <tr>
                                <td colspan="5" class="py-10 text-center text-sm text-zinc-400 italic">
                                    Belum ada laporan yang Anda buat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayatLaporan as $row): ?>
                                <tr class="group hover:bg-zinc-50/50 transition-colors">
                                    <td class="py-4 px-2 text-sm text-zinc-600">
                                        <?= date('d/m/Y', strtotime($row['tanggal_pelaporan'])) ?>
                                    </td>
                                    <td class="py-4 px-2">
                                        <p class="text-sm font-bold text-zinc-800"><?= htmlspecialchars($row['nama_siswa']) ?></p>
                                        <p class="text-[12px] pt-1 text-zinc-500 font-medium uppercase"><?= $row['kelas'] ?></p>
                                    </td>
                                    <td class="py-4 px-2">
                                        <span class="px-2 py-1 bg-zinc-100 rounded text-[10px] font-bold text-zinc-600 uppercase">
                                            <?= htmlspecialchars($row['nama_jenis']) ?>
                                        </span>
                                        <p class="text-[12px] pt-1 line-clamp-1 text-zinc-500 mt-1"><?= htmlspecialchars($row['keterangan']) ?></p>
                                    </td>

                                    <td class="py-4 px-2 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="../pelanggaran/detail.php?id=<?= $row['id_pelanggaran'] ?>"
                                                class="text-zinc-400 hover:text-zinc-900 transition-colors p-1"
                                                title="Lihat Detail">
                                                <span class="icon-eye w-5 h-5"></span>
                                            </a>

                                            <a href="<?= $deletePelanggaranPath ?>?id=<?= $row['id_pelanggaran'] ?>"
                                                class="button-danger p-4"
                                                title="Hapus Laporan">

                                                <span class="icon-delete w-5 h-5"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>

<dialog id="modal_add_pelanggaran" class="modal">
    <div class="modal-box max-w-6xl bg-white p-10 rounded-lg border border-zinc-100">
        <div class="flex flex-col gap-2 mb-8">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $logoPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <h2 class="text-xl font-bold text-zinc-900">Buat Pelanggaran</h2>
            <p class="text-sm text-zinc-500 font-medium">Sistem Pelanggaran Siswa</p>
        </div>

        <form method="POST" action="<?= $addPelanggaranPath ?>" class="w-full">
            <div class="grid grid-cols-5 w-full gap-4">
                <div class="space-y-4 bg-zinc-50 p-6 rounded-lg col-span-2">
                    <div class="form-control">
                        <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Informasi Siswa</p>
                        <label class="label font-bold text-zinc-700">Jurusan</label>
                        <select id="select-jurusan" name="jurusan" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php
                            $jurusanStmt = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC");
                            while ($j = $jurusanStmt->fetch()) echo "<option value='{$j['jurusan']}'>{$j['jurusan']}</option>";
                            ?>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label font-bold text-zinc-700">Kelas</label>
                        <select id="select-kelas" name="kelas" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" disabled required>
                            <option value="">Pilih Jurusan Terlebih Dahulu</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label font-bold text-zinc-700">Siswa</label>
                        <select id="select-siswa-final" name="id_siswa" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" disabled required>
                            <option value="">Pilih Kelas Terlebih Dahulu</option>
                        </select>
                    </div>


                </div>
                <div class="flex-1 col-span-3 space-y-3">
                    <div class="form-control space-y-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Informasi Pelanggaran</p>
                        <label class="label font-bold text-zinc-700">Jenis Pelanggaran</label>
                        <select name="id_jenis" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" required>
                            <option value="" disabled selected>Pilih Pelanggaran</option>
                            <?php
                            $jenisStmt = $pdo->query("SELECT id_jenis, nama_jenis, point FROM jenis_pelanggaran ORDER BY nama_jenis ASC");
                            while ($jp = $jenisStmt->fetch()) echo "<option value='{$jp['id_jenis']}'>{$jp['nama_jenis']} (-{$jp['point']} Point)</option>";
                            ?>
                        </select>
                    </div>

                    <div class="form-control flex flex-col space-y-2">
                        <label class="label font-bold text-zinc-700">Keterangan</label>
                        <textarea name="keterangan" class="textarea textarea-bordered rounded-xl bg-zinc-50 border-zinc-200 h-24 w-full" placeholder="Masukkan detail kejadian..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-action flex justify-end gap-3 mt-10">
                <button type="button" class="button-primary" onclick="modal_add_pelanggaran.close()">Cancel</button>
                <button type="submit" class="button-secondary flex flex-row items-center">
                    <span class="icon-check w-5 h-5 mr-1"></span> Simpan
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('riwayat-filter-form');
        const filterKelas = document.getElementById('filter-kelas');
        const filterSearchNama = document.getElementById('filter-search-nama');
        let filterSubmitTimer = null;

        if (filterForm && filterKelas && filterSearchNama) {
            const submitFilter = () => {
                if (filterSubmitTimer) {
                    clearTimeout(filterSubmitTimer);
                }
                filterForm.requestSubmit();
            };

            filterKelas.addEventListener('change', submitFilter);

            filterSearchNama.addEventListener('input', function() {
                if (filterSubmitTimer) {
                    clearTimeout(filterSubmitTimer);
                }

                filterSubmitTimer = setTimeout(() => {
                    filterForm.requestSubmit();
                }, 400);
            });
        }

        const selJurusan = document.getElementById('select-jurusan');
        const selKelas = document.getElementById('select-kelas');
        const selSiswa = document.getElementById('select-siswa-final');

        // 1. Saat Jurusan dipilih -> Cari Kelas yang ada di Jurusan itu
        selJurusan.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            console.log("Fetching kelas for: " + this.value);

            try {
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);
                const data = await res.json();

                selKelas.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
                data.forEach(item => {
                    selKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });

                selKelas.disabled = false; // Aktifin select kelas
                selSiswa.disabled = true; // Riset select siswa
                selSiswa.innerHTML = '<option value="">Pilih Kelas Dulu</option>';
            } catch (err) {
                console.error("Fetch Error (Kelas):", err);
            }
        });

        // 2. Saat Kelas dipilih -> Cari Nama Siswa yang ada di Kelas itu
        selKelas.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            console.log("Fetching siswa for: " + this.value);

            try {
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                selSiswa.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
                data.forEach(item => {
                    selSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });

                selSiswa.disabled = false; // Aktifin select siswa
            } catch (err) {
                console.error("Fetch Error (Siswa):", err);
            }
        });
    });
</script>