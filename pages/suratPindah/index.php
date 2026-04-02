<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$totalSuratPindah = dbCount($pdo, 'surat_pindah');


$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

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
            sp.id_surat_pindah,
            sp.id_siswa,
            sp.id_sekolah, 
            sp.alasan_pindah,
            sp.tanggal_surat,
            s.nomor_surat,
            sw.nama_siswa,
            sw.kelas,
            sw.jurusan,
            sk.nama_sekolah AS sekolah_tujuan
          FROM surat_pindah sp
          JOIN surat s ON sp.id_surat_pindah = s.id_jenis_surat 
                       AND s.jenis_surat = 'surat_pindah'
          JOIN siswa sw ON sp.id_siswa = sw.id_siswa
          JOIN sekolah sk ON sp.id_sekolah = sk.id_sekolah
          WHERE $condition
          ORDER BY s.nomor_surat DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pindahList = $stmt->fetchAll(PDO::FETCH_ASSOC); // Pastiin variabel ini yang dipake di foreach card
// Ambil angka terbesar dari seluruh tabel surat (Global)
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
    <title>Surat Pindah | Sistem Pelanggaran</title>
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
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalSuratPindah) ?> Surat</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Surat Pindah</p>
                        </div>
                    </div>

                </div>
                <div class="mt-6 space-y-4">
                    <!-- filter & information goes here -->
                    <form method="GET" id="searchForm" class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <p class="font-heading-6 font-semibold text-zinc-800"> Tabel Surat Pindah</p>
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
                                <button type="button" class="button-primary" onclick="modal_add_pindah.showModal()">Add</button>
                            </div>
                        </div>
                    </form>
                    <!-- table goes here -->
                    <div class="mt-6 space-y-4">
                        <?php if (empty($pindahList)): ?>
                            <div class="p-10 text-center border-2 border-dashed border-zinc-200 rounded-xl text-zinc-400">
                                Belum ada data siswa pindah sekolah.
                            </div>
                        <?php else: ?>
                            <?php foreach ($pindahList as $item): ?>
                                <div class="group flex items-center justify-between px-5 py-6 bg-white border border-zinc-200 rounded-xl hover:border-orange-500 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-6">
                                        <div class="flex flex-col items-center justify-center h-16 w-16 bg-zinc-50 rounded-lg border border-zinc-100 group-hover:bg-orange-50 group-hover:border-orange-100 transition-colors">
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter mb-0.5">No. Surat</span>
                                            <span class="text-xl font-bold text-zinc-800">#<?= $item['nomor_surat'] ?></span>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-zinc-900 text-lg leading-tight"><?= htmlspecialchars($item['nama_siswa']) ?></h4>
                                            <p class="text-sm text-zinc-500 font-medium mt-1">
                                                Ke: <span class="text-zinc-800 font-bold"><?= htmlspecialchars($item['sekolah_tujuan']) ?></span> • <?= $item['kelas'] ?>
                                            </p>
                                        </div>

                                        <div class="h-10 w-px bg-zinc-200"></div>

                                        <div class="max-w-md">
                                            <div class="flex justify-start items-start gap-2 mb-1 text-orange-600">
                                                <span class="text-[11px] font-extrabold uppercase tracking-widest">
                                                    Status: Non-Aktif (Pindah)
                                                </span>
                                            </div>
                                            <p class="text-sm text-zinc-600 line-clamp-1 italic">"<?= htmlspecialchars($item['alasan_pindah']) ?>"</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick='openEditPindah( "<?= $item["id_surat_pindah"] ?>", <?= json_encode($item["nama_siswa"] ?? "") ?>, <?= json_encode($item["jurusan"] ?? "") ?>, <?= json_encode($item["kelas"] ?? "") ?>, "<?= $item["id_sekolah"] ?>", <?= json_encode($item["alasan_pindah"] ?? "") ?> )' class="button-secondary p-3">
                                            <span class="icon-edit w-5 h-5"></span>
                                        </button>

                                        <a href="delete_process.php?id=<?= $item['id_surat_pindah'] ?>"
                                            class="button-danger p-3"
                                            onclick="return confirm('Yakin ingin menghapus data pindah ini? Status siswa tidak akan otomatis kembali aktif.')">
                                            <span class="icon-delete w-5 h-5"></span>
                                        </a>

                                        <div class="w-px h-8 bg-zinc-100 mx-1"></div>

                                        <a href="print_surat.php?id=<?= $item['id_surat_pindah'] ?>" target="_blank" class="button-primary flex items-center gap-2 px-5 py-2.5 shadow-lg ">
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

<dialog id="modal_add_pindah" class="modal">
    <div class="modal-box max-w-5xl bg-white p-10 rounded-lg border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 mb-8 border-b border-zinc-200 pb-6">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Buat Surat Pindah Sekolah</h2>
                <p class="text-sm text-zinc-500 font-medium">Siswa akan otomatis dinonaktifkan setelah surat diterbitkan</p>
            </div>
        </div>

        <form method="POST" action="add_process.php" class="space-y-6">
            <input type="hidden" name="nomor_surat" value="<?= $nextNum ?>">

            <div class="grid grid-cols-3 gap-6">
                <div class="flex flex-col gap-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-100">
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase tracking-wider">1. Jurusan</label>
                        <select id="pindah-jurusan" name="jurusan" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-orange-500" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <?php
                            $jurs = $pdo->query("SELECT DISTINCT jurusan FROM siswa WHERE status = 'aktif' ORDER BY jurusan ASC");
                            while ($j = $jurs->fetch()) echo "<option value='{$j['jurusan']}'>{$j['jurusan']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase tracking-wider">2. Kelas</label>
                        <select id="pindah-kelas" name="kelas" class="select select-bordered w-full rounded-xl border-zinc-200" disabled required>
                            <option value="">Pilih Jurusan Dulu</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase tracking-wider">3. Nama Siswa</label>
                        <select id="pindah-siswa" name="id_siswa" class="select select-bordered w-full rounded-xl border-zinc-200" disabled required>
                            <option value="">Pilih Kelas Dulu</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="flex flex-col gap-6">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Sekolah Tujuan</label>
                            <select name="id_sekolah" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-orange-500" required>
                                <option value="" disabled selected>Pilih Sekolah Tujuan</option>
                                <?php
                                $skul = $pdo->query("SELECT * FROM sekolah ORDER BY nama_sekolah ASC");
                                while ($s = $skul->fetch()) echo "<option value='{$s['id_sekolah']}'>{$s['nama_sekolah']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-control flex flex-col gap-2">
                            <label class="label font-bold text-zinc-700 text-sm">Alasan Pindah</label>
                            <textarea name="alasan_pindah" class="textarea textarea-bordered rounded-xl border-zinc-200 h-28 w-full focus:ring-2 focus:ring-orange-500" placeholder="Contoh: Mengikuti perpindahan tugas orang tua ke luar kota..." required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t border-zinc-200 pt-6">
                <button type="button" class="button-secondary" onclick="modal_add_pindah.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Nonaktifkan & Terbitkan
                </button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_edit_pindah" class="modal">
    <div class="modal-box max-w-5xl bg-white p-10 rounded-lg border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 mb-8 border-b border-zinc-200 pb-6">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Edit Surat Pindah Sekolah</h2>
                <p class="text-sm text-zinc-500 font-medium">Data akan diubah berdasarkan data baru yang diinputkan</p>
            </div>
        </div>
        <form method="POST" action="edit_process.php" class="space-y-6">
            <input type="hidden" name="id_surat_pindah" id="edit-pindah-id">

            <div class="grid grid-cols-3 gap-6">
                <div class="flex flex-col gap-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Data Siswa (Locked)</p>

                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">Jurusan</label>
                        <input type="text" id="edit-pindah-jurusan" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
                    </div>

                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">Kelas</label>
                        <input type="text" id="edit-pindah-kelas" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
                    </div>

                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-xs uppercase">Nama Siswa</label>
                        <input type="text" id="edit-pindah-nama" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="flex flex-col gap-6">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Sekolah Tujuan</label>
                            <select name="id_sekolah" id="edit-pindah-sekolah" class="select select-bordered w-full rounded-xl border-zinc-200" required>
                                <?php
                                $skul = $pdo->query("SELECT * FROM sekolah ORDER BY nama_sekolah ASC");
                                while ($s = $skul->fetch()) echo "<option value='{$s['id_sekolah']}'>{$s['nama_sekolah']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-control flex flex-col gap-2">
                            <label class="label font-bold text-zinc-700 text-sm">Alasan Pindah</label>
                            <textarea name="alasan_pindah" id="edit-pindah-alasan" class="textarea textarea-bordered rounded-xl border-zinc-200 h-28 w-full" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="button" class="button-secondary" onclick="modal_edit_pindah.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Update Data</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pJurusan = document.getElementById('pindah-jurusan');
        const pKelas = document.getElementById('pindah-kelas');
        const pSiswa = document.getElementById('pindah-siswa');

        pJurusan?.addEventListener('change', async function() {
            const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${this.value}`);
            const data = await res.json();
            pKelas.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
            data.forEach(item => pKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`);
            pKelas.disabled = false;
            pSiswa.disabled = true;
        });

        pKelas?.addEventListener('change', async function() {
            const res = await fetch(`get_filter_data.php?type=siswa&kelas=${this.value}`);
            const data = await res.json();
            pSiswa.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
            data.forEach(item => pSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`);
            pSiswa.disabled = false;
        });
    });

    function openEditPindah(id, nama, jurusan, kelas, idSekolah, alasan) {
        const modal = document.getElementById('modal_edit_pindah');
        const targetModal = document.getElementById('modal_edit_pindah');

        // Isi Sisi Kiri (Locked)
        document.getElementById('edit-pindah-id').value = id;
        document.getElementById('edit-pindah-nama').value = nama;
        document.getElementById('edit-pindah-jurusan').value = jurusan;
        document.getElementById('edit-pindah-kelas').value = kelas;

        // Isi Sisi Kanan (Editable)
        document.getElementById('edit-pindah-sekolah').value = idSekolah;
        document.getElementById('edit-pindah-alasan').value = alasan;

        if (targetModal) targetModal.showModal();
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