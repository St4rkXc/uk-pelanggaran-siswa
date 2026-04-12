<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$totalSuratPernyataanOrtu = dbCount($pdo, 'surat_pernyataan_ortu');

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$jurusanFilter = isset($_GET['jurusan_filter']) ? trim($_GET['jurusan_filter']) : '';
$kelasFilter = isset($_GET['kelas_filter']) ? trim($_GET['kelas_filter']) : '';

$allJurusan = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC")->fetchAll(PDO::FETCH_COLUMN);
$allKelas = $pdo->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC")->fetchAll(PDO::FETCH_COLUMN);

$condition = "1=1";
$params = [];

if (!empty($search)) {
    $condition .= " AND sw.nama_siswa LIKE ?";
    $params[] = "%$search%";
}
if (!empty($jurusanFilter)) {
    $condition .= " AND sw.jurusan = ?";
    $params[] = $jurusanFilter;
}
if (!empty($kelasFilter)) {
    $condition .= " AND sw.kelas = ?";
    $params[] = $kelasFilter;
}

$query = "SELECT 
    spo.id_surat_pernyataan_ortu, 
    spo.id_siswa,           
    sw.nama_siswa, 
    sw.nama_ortu, 
    sw.kelas, 
    spo.tanggal_surat
FROM surat_pernyataan_ortu spo
JOIN siswa sw ON spo.id_siswa = sw.id_siswa
WHERE $condition
ORDER BY spo.id_surat_pernyataan_ortu DESC;";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$suratList = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan Orang Tua | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="flex w-dvw bg-zinc-50 overflow-x-hidden">
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
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
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalSuratPernyataanOrtu) ?> Surat</h5>
                            <p class="font-paragraph-14 font-medium text-zinc-600">Total Surat Pernyataan Ortu</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <form method="GET" id="searchForm" class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <p class="font-heading-6 font-semibold text-zinc-800"> Tabel Surat Pernyataan Orang Tua</p>
                            <div class="flex items-center gap-2">
                                <select name="jurusan_filter" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">Semua Jurusan</option>
                                    <?php foreach ($allJurusan as $j): ?>
                                        <option value="<?= htmlspecialchars($j) ?>" <?= $jurusanFilter == $j ? 'selected' : '' ?>><?= htmlspecialchars($j) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="kelas_filter" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">Semua Kelas</option>
                                    <?php foreach ($allKelas as $k): ?>
                                        <option value="<?= htmlspecialchars($k) ?>" <?= $kelasFilter == $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="relative flex items-center">
                                    <input type="text"
                                        id="searchInput"
                                        name="search"
                                        value="<?= htmlspecialchars($search) ?>"
                                        class="rounded-lg border border-zinc-300 py-3 px-4 pr-10 w-65 placeholder:text-[14px] placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Cari nama siswa..."
                                        autocomplete="off">
                                    <span class="icon-search h-4 w-4 absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600"></span>
                                </div>
                                <button type="button" class="button-primary" onclick="modal_add_surat_pernyataan.showModal()">Add</button>
                            </div>
                        </div>
                    </form>

                    <div id="table-need-focus" class="mt-6 space-y-3">
                    </div>

                    <div id="surat-pernyataan" class="mt-6 space-y-3">
                        <?php if (empty($suratList)): ?>
                            <div class="p-10 text-center border-2 border-dashed border-zinc-200 rounded-xl text-zinc-400">
                                Belum ada data surat pernyataan orang tua.
                            </div>
                        <?php else: ?>
                            <?php foreach ($suratList as $item):
                                $words = explode(" ", $item['nama_siswa']);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ""));
                            ?>
                                <div class="group flex items-center justify-between px-5 py-6 bg-white border border-zinc-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-6">
                                        <div class="flex items-center justify-center h-16 w-16 bg-zinc-50 rounded-lg border border-zinc-100 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
                                            <span class="text-xl font-bold text-zinc-400 group-hover:text-blue-500"><?= $initials ?></span>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-zinc-900 text-lg leading-tight"><?= htmlspecialchars($item['nama_siswa']) ?></h4>
                                            <p class="text-sm text-zinc-500 font-medium mt-1">
                                                <?= htmlspecialchars($item['kelas']) ?> •
                                                <span class="text-zinc-400">Tgl:</span>
                                                <span class="text-zinc-700"><?= date('d M Y', strtotime($item['tanggal_surat'])) ?></span>
                                            </p>
                                        </div>

                                        <div class="h-10 w-px bg-zinc-200 mx-4"></div>

                                        <div class="max-w-md">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-600">
                                                    Orang Tua
                                                </span>
                                            </div>
                                            <p class="text-sm text-zinc-600 line-clamp-1 italic">Nama Wali / Ortu : <?= htmlspecialchars($item['nama_ortu']) ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button class="button-danger p-3" onclick="if(confirm('Yakin mau hapus surat pernyataan atas nama <?= addslashes($item['nama_siswa']) ?>?')) { window.location.href='delete_process.php?id=<?= $item['id_surat_pernyataan_ortu'] ?>'; }">
                                            <span class=" icon-delete w-5 h-5"></span>
                                        </button>

                                        <div class="w-px h-8 bg-zinc-100 mx-1"></div>

                                        <a href="print_surat.php?id=<?= $item['id_surat_pernyataan_ortu'] ?>" class="button-primary flex items-center gap-2 px-5 py-2.5 shadow-lg shadow-zinc-100" target="_blank">
                                            <span class="icon-print h-5 w-5"></span>
                                            <span class="font-bold">Cetak</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>

<dialog id="modal_add_surat_pernyataan" class="modal">
    <div class="modal-box max-w-4xl bg-white p-10 rounded-lg border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 mb-8 border-b border-zinc-200 pb-6">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Buat Surat Pernyataan Orang Tua</h2>
                <p class="text-sm text-zinc-500 font-medium">Generate dokumen pernyataan orang tua secara otomatis</p>
            </div>
        </div>

        <form method="POST" action="add_process.php" class="space-y-6">
            <div class="flex gap-4">
                <div class="bg-zinc-50 p-6 rounded-2xl border border-zinc-100 space-y-4 h-fit w-full">
                    <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Pilih Siswa</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Jurusan</label>
                            <select id="adj-jurusan" name="jurusan" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" required>
                                <option value="" disabled selected>Pilih Jurusan</option>
                                <?php
                                $jStmt = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC");
                                while ($j = $jStmt->fetch()) echo "<option value='{$j['jurusan']}'>{$j['jurusan']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Kelas</label>
                            <select id="adj-kelas" name="kelas" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" disabled required>
                                <option value="">Pilih Jurusan Terlebih Dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-sm">Nama Siswa</label>
                        <select id="adj-siswa" name="id_siswa" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" disabled required>
                            <option value="">Pilih Kelas Terlebih Dahulu</option>
                        </select>
                    </div>
                    <div class="form-control space-y-2 mt-4">
                        <label class="label font-bold text-zinc-700 text-sm">Tanggal Surat (Hari Ini)</label>
                        <input type="text" value="<?= date('d/m/Y'); ?>" class="my-input bg-zinc-100 cursor-not-allowed w-full" readonly>
                        <input type="hidden" name="tanggal_surat" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t pt-6 mt-8 border-zinc-200">
                <button type="button" class="button-secondary" onclick="modal_add_surat_pernyataan.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Simpan & Generate
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selJurusanAdj = document.getElementById('adj-jurusan');
        const selKelasAdj = document.getElementById('adj-kelas');
        const selSiswaAdj = document.getElementById('adj-siswa');

        // Filter Kelas berdasarkan Jurusan
        selJurusanAdj.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);
                const data = await res.json();

                selKelasAdj.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
                data.forEach(item => {
                    selKelasAdj.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });
                selKelasAdj.disabled = false;
                selSiswaAdj.disabled = true;
                selSiswaAdj.innerHTML = '<option value="">Pilih Kelas Dulu</option>';
            } catch (err) {
                console.error(err);
            }
        });

        // Filter Siswa berdasarkan Kelas
        selKelasAdj.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                selSiswaAdj.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
                data.forEach(item => {
                    selSiswaAdj.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });
                selSiswaAdj.disabled = false;
            } catch (err) {
                console.error(err);
            }
        });
    });

    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let timer;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });

        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    }
</script>