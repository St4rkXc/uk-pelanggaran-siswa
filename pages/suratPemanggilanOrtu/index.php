<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$totalPemanggilanOrtu = dbCount($pdo, 'surat_panggilan_ortu');
$condition = "YEARWEEK(tanggal_surat, 1) = YEARWEEK(CURDATE(), 1)";
$totalPemanggilanOrtuMingguIni = dbCount($pdo, 'surat_panggilan_ortu', $condition);


$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

// ... code lo sebelumnya ...


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kelas_filter = isset($_GET['kelas_filter']) ? trim($_GET['kelas_filter']) : '';

$all_kelas = $pdo->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC")->fetchAll(PDO::FETCH_COLUMN);

$condition = "1=1";
$params = [];

if (!empty($search)) {
    $condition .= " AND sw.nama_siswa LIKE ?";
    $params[] = "%$search%";
}
if (!empty($kelas_filter)) {
    $condition .= " AND sw.kelas = ?";
    $params[] = $kelas_filter;
}

$query = "SELECT 
    spo.id_surat_panggilan_ortu,
    spo.id_siswa,
    spo.keperluan, 
    spo.tanggal_temu,
    spo.tanggal_surat,
    s.nomor_surat,
    sw.nama_siswa,
    sw.nama_ortu,
    sw.kelas,
    sw.jurusan
FROM surat_panggilan_ortu spo
JOIN surat s ON spo.id_surat_panggilan_ortu = s.id_jenis_surat AND s.jenis_surat = 'surat_panggilan_ortu'
JOIN siswa sw ON spo.id_siswa = sw.id_siswa
WHERE $condition
ORDER BY s.nomor_surat DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$panggilanList = $stmt->fetchAll(PDO::FETCH_ASSOC);


$allSiswa = $pdo->query("SELECT id_siswa, nama_siswa, kelas, jurusan FROM siswa ORDER BY nama_siswa")->fetchAll(PDO::FETCH_ASSOC);
$jurusans = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan")->fetchAll(PDO::FETCH_ASSOC);
$kelases = $pdo->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas")->fetchAll(PDO::FETCH_ASSOC);

$stmtNext = $pdo->query("SELECT MAX(nomor_surat) as max_num FROM surat");
$rowNext = $stmtNext->fetch();

// Jika hasil MAX() adalah null (tabel kosong), mulai dari 1
$nextNum = ($rowNext['max_num'] !== null) ? (int)$rowNext['max_num'] + 1 : 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pemanggilan Orang Tua | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="flex w-dvw overflow-x-hidden">
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
                    <form method="GET" id="searchForm" class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <p class="font-heading-6 font-semibold text-zinc-800"> Tabel Surat Pemanggilan Orang Tua</p>
                            <div class="flex items-center gap-2">
                                <select name="kelas_filter" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">Semua Kelas</option>
                                    <?php foreach ($all_kelas as $k): ?>
                                        <option value="<?= htmlspecialchars($k) ?>" <?= $kelas_filter == $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
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
                                <button type="button" class="button-primary" onclick="modal_add_panggilan.showModal()">Add</button>
                            </div>
                        </div>
                    </form>
                    <!-- table goes here -->
                    <div class="mt-6 space-y-3">
                        <?php if (empty($panggilanList)): ?>
                            <div class="p-10 text-center border-2 border-dashed border-zinc-200 rounded-xl text-zinc-400">
                                Belum ada jadwal pemanggilan orang tua.
                            </div>
                        <?php else: ?>
                            <?php foreach ($panggilanList as $item):
                                $initials = strtoupper(substr($item['nama_siswa'], 0, 2));
                            ?>
                                <div class="group flex items-center justify-between px-5 py-6 bg-white border border-zinc-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-6">
                                        <div class="flex flex-col items-center justify-center h-16 w-16 bg-zinc-50 rounded-lg border border-zinc-100 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter mb-0.5">No. Surat</span>
                                            <span class="text-xl font-bold text-zinc-800">#<?= $item['nomor_surat'] ?></span>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-zinc-900 text-lg leading-tight"><?= htmlspecialchars($item['nama_siswa']) ?></h4>
                                            <p class="text-sm text-zinc-500 font-medium mt-1">
                                                Ortu: <span class="text-zinc-800 font-bold"><?= htmlspecialchars($item['nama_ortu']) ?></span> • <?= $item['kelas'] ?>
                                            </p>
                                        </div>

                                        <div class="h-10 w-px bg-zinc-200"></div>

                                        <div class="max-w-md">
                                            <div class="flex justify-start items-start gap-2 mb-1 text-blue-600">
                                                <span class="text-[11px] font-extrabold uppercase tracking-widest">
                                                    Temu: <?= date('d M Y | H:i', strtotime($item['tanggal_temu'])) ?> WITA
                                                </span>
                                            </div>
                                            <p class="text-sm text-zinc-600 line-clamp-1 italic">"<?= htmlspecialchars($item['keperluan']) ?>"</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditPanggilan(
    '<?= $item['id_surat_panggilan_ortu'] ?>', 
    '<?= addslashes($item['nama_siswa'] ?? '') ?>', 
    '<?= $item['kelas'] ?? '' ?>', 
    '<?= $item['tanggal_temu'] ?? '' ?>', 
    '<?= addslashes($item['keperluan'] ?? '') ?>'
)" class="button-secondary p-3">
                                            <span class="icon-edit w-5 h-5"></span>
                                        </button>
                                        <a href="delete_process.php?id=<?= $item['id_surat_panggilan_ortu'] ?>"
                                            class="button-danger p-3">
                                            <span class="icon-delete w-5 h-5"></span>
                                        </a>
                                        <div class="w-px h-8 bg-zinc-100 mx-1"></div>
                                        <a href="print_surat.php?id=<?= $item['id_surat_panggilan_ortu'] ?>" target="_blank" class="button-primary flex items-center gap-2 px-5 py-2.5 shadow-lg shadow-zinc-100">
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

<dialog id="modal_add_panggilan" class="modal">
    <div class="modal-box max-w-5xl bg-white p-10 rounded-lg border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 border-b pb-6 mb-8 border-zinc-200">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Buat Surat Pemanggilan Orang Tua</h2>
                <p class="text-sm text-zinc-500 font-medium">Generate dokumen pemanggilan orang tua secara otomatis</p>
            </div>
        </div>

        <form method="POST" action="add_process.php" class="space-y-6">
            <input type="hidden" name="nomor_surat" value="<?= $nextNum ?>">
            <div class="grid grid-cols-3 gap-6 ">
                <div class="flex flex-col gap-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-100">
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">1. Jurusan</label>
                        <select id="adj-jurusan-panggilan" name="jurusan" class="select select-bordered w-full rounded-xl border-zinc-200" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php
                            $jurs = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC");
                            while ($j = $jurs->fetch()) echo "<option value='{$j['jurusan']}'>{$j['jurusan']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">2. Kelas</label>
                        <select id="adj-kelas-panggilan" name="kelas" class="select select-bordered w-full rounded-xl border-zinc-200" disabled required>
                            <option value="">Pilih Jurusan Dulu</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">3. Nama Siswa</label>
                        <select id="adj-siswa-panggilan" name="id_siswa" class="select select-bordered w-full rounded-xl border-zinc-200" disabled required>
                            <option value="">Pilih Kelas Dulu</option>
                        </select>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex flex-col gap-6 ">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Jadwal Pertemuan</label>
                            <input type="datetime-local" name="tanggal_temu" class="my-input w-full" required>
                        </div>
                        <div class="form-control flex flex-col gap-2">
                            <label class="label font-bold text-zinc-700 text-sm">Keperluan Panggilan</label>
                            <textarea name="keperluan" class="textarea textarea-bordered rounded-xl border-zinc-200 h-24 w-full" placeholder="Alasan pemanggilan orang tua..." required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t pt-6 mt-8 border-zinc-200">
                <button type="button" class="button-secondary" onclick="modal_add_panggilan.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-6 h-6 text-white"></span>
                    Simpan & Terbitkan</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_edit_panggilan" class="modal">
    <div class="modal-box max-w-5xl bg-white p-10 rounded-lg border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 border-b pb-6 mb-8 border-zinc-200">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Edit Surat Pemanggilan Orang Tua</h2>
                <p class="text-sm text-zinc-500 font-medium">Edit dokumen pemanggilan orang tua secara otomatis</p>
            </div>
        </div>

        <form method="POST" action="edit_process.php" class="space-y-6">
            <input type="hidden" name="id_surat_panggilan_ortu" id="edit-id-panggilan">

            <div class="grid grid-cols-3 gap-6">
                <div class="flex flex-col gap-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Data Siswa (Locked)</p>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">Nama Siswa</label>
                        <input type="text" id="edit-nama-siswa" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">Kelas</label>
                        <input type="text" id="edit-kelas-siswa" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="flex flex-col gap-6 ">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Jadwal Pertemuan Baru</label>
                            <input type="datetime-local" name="tanggal_temu" id="edit-tanggal-temu" class="my-input w-full" required>
                        </div>
                        <div class="form-control flex flex-col gap-2">
                            <label class="label font-bold text-zinc-700 text-sm">Keperluan Panggilan Baru</label>
                            <textarea name="keperluan" id="edit-keperluan" class="textarea textarea-bordered rounded-xl border-zinc-200 h-24 w-full focus:ring-2 focus:ring-blue-500" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t pt-6 mt-8 border-zinc-200 ">
                <button type="button" class="button-secondary" onclick="modal_edit_panggilan.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-6 h-6 text-white"></span>
                    Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selJurusan = document.getElementById('adj-jurusan-panggilan');
        const selKelas = document.getElementById('adj-kelas-panggilan');
        const selSiswa = document.getElementById('adj-siswa-panggilan');

        // 1. Fetch Kelas berdasarkan Jurusan
        selJurusan.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);
                const data = await res.json();

                selKelas.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
                data.forEach(item => {
                    selKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });
                selKelas.disabled = false;
                selSiswa.disabled = true;
                selSiswa.innerHTML = '<option value="">Pilih Kelas Dulu</option>';
            } catch (err) {
                console.error(err);
            }
        });

        // 2. Fetch Siswa berdasarkan Kelas
        selKelas.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                selSiswa.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
                data.forEach(item => {
                    selSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });
                selSiswa.disabled = false;
            } catch (err) {
                console.error(err);
            }
        });
    });

    function openEditPanggilan(id, nama, kelas, tgl, keperluan) {
        const modal = document.getElementById('modal_edit_panggilan');
        if (!modal) return;

        // 1. Set ID Hidden & Data Locked
        document.getElementById('edit-id-panggilan').value = id;
        document.getElementById('edit-nama-siswa').value = nama;
        document.getElementById('edit-kelas-siswa').value = kelas;

        // 2. Set Tanggal (Format ISO untuk datetime-local)
        if (tgl) {
            let date = new Date(tgl);
            let isoStr = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
            document.getElementById('edit-tanggal-temu').value = isoStr;
        }

        // 3. FIX: Masukin teks ke Textarea Keperluan
        // Pastikan ID 'edit-keperluan' ini sama dengan ID di HTML lo
        const textareaKeperluan = document.getElementById('edit-keperluan');
        if (textareaKeperluan) {
            textareaKeperluan.value = keperluan;
        }

        modal.showModal();
    }

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