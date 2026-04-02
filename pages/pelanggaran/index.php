<?php
session_start();
$requiredRole = ['guru_bk', 'admin'];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];


$totalSiswa = dbCount($pdo, 'siswa', 'status = "aktif"');

// [PAGINATION LOGIC]
// Menentukan jumlah maksimal data yang tampil per halaman
$limit = 10;
// Menangkap nomor halaman aktif dari URL. Jika tidak ada, default ke halaman 1.
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
// Menghitung pergeseran baris (offset) untuk kueri database
$offset = ($page - 1) * $limit;

// Menghitung total seluruh data pelanggaran untuk menentukan jumlah halaman
$totalPelanggaran = dbCount($pdo, 'pelanggaran');
$total_rows = $totalPelanggaran; // Variabel yang dibutuhkan oleh component pagination.php
$total_pages = ceil($total_rows / $limit);

// [QUERY DATA]
// Mengambil data pelanggaran dengan relasi tabel Siswa, Jenis Pelanggaran, dan User (Pelapor)
$sql = "SELECT 
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
        LIMIT $limit OFFSET $offset";

$stmt = $pdo->query($sql);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggaran | Sistem Pelanggaran</title>
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
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalSiswa) ?> Siswa</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Siswa Terdaftar</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-siren h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalPelanggaran) ?> Pelanggaran</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Pelanggaran Tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="font-heading-6 font-semibold text-zinc-800">Daftar Pelanggaran</p>
                        <div class="">
                            <button class="button-primary" onclick="modal_add_pelanggaran.showModal()">Add</button>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-zinc-300 p-8">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="my-th">

                                    <th class="my-th">Nama Siswa</th>
                                    <th class="my-th">Kelas</th>
                                    <th class="my-th">Pelanggaran</th>
                                    <th class="my-th text-center">Poin</th>
                                    <th class="my-th">Pelapor</th>
                                    <th class="my-th">Waktu Pelaporan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                <?php
                                $no = 1;
                                while ($row = $stmt->fetch()):
                                    $waktu = date('d M Y, H:i', strtotime($row['tanggal_pelaporan']));
                                ?>
                                    <tr class="border-b border-b-zinc-300 hover:bg-zinc-50 transition-all cursor-pointer" onclick="openViewPelanggaran(this)"
                                        data-nama="<?= htmlspecialchars($row['nama_siswa']); ?>"
                                        data-kelas="<?= htmlspecialchars($row['kelas']); ?>"
                                        data-jenis="<?= htmlspecialchars($row['nama_jenis']); ?>"
                                        data-pelapor="<?= htmlspecialchars($row['nama_pelapor']); ?>"
                                        data-point="-<?= $row['bobot_poin']; ?>"
                                        data-waktu="<?= date('H:i, d-m-y', strtotime($row['tanggal_pelaporan'])); ?>"
                                        data-keterangan="<?= htmlspecialchars($row['keterangan']); ?>"
                                        data-id="<?= $row['id_pelanggaran']; ?>">


                                        <td class=" p-4 text-zinc-600 hidden"><?= $no++; ?></td>
                                        <td class="p-4">
                                            <div class="font-medium text-zinc-800"><?= htmlspecialchars($row['nama_siswa']); ?></div>
                                        </td>
                                        <td class="p-4 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                        <td class="p-4">
                                            <div class="text-zinc-800"><?= htmlspecialchars($row['nama_jenis']); ?></div>
                                            <p class="text-[11px] text-zinc-400 italic line-clamp-1"><?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '-'; ?></p>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="text-red-600 font-bold">-<?= $row['bobot_poin']; ?></span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-zinc-200 flex items-center justify-center">
                                                    <span class="icon-user w-3 h-3 text-zinc-500"></span>
                                                </div>
                                                <span class="text-zinc-700 text-sm"><?= htmlspecialchars($row['nama_pelapor']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-2 text-zinc-500 text-sm">
                                            <?= $waktu; ?> WITA
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if ($no === 1): ?>
                                    <tr>
                                        <td colspan="7" class="p-10 text-center text-zinc-400 italic">Belum ada data pelanggaran tercatat.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php include BASE_PATH . '/includes/ui/pagination/pagination.php'; ?>
                    </div>
                </div>
            </main>
        </div>

</body>

</html>

<!-- modal view pelanggaran -->
<dialog id="modal_view_pelanggaran" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8 rounded-lg">
        <div class="flex flex-col gap-2 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <h2 class="text-xl font-bold text-zinc-900">Keterangan Pelanggaran</h2>
            <p class="text-sm text-zinc-500 font-medium">Sistem Pelanggaran Siswa</p>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-zinc-800" id="v-p-nama"></h1>
            <p class="text-zinc-500 font-medium" id="v-p-kelas"></p>
        </div>

        <div class="grid grid-cols-1 gap-y-4 text-[15px]">
            <div class="flex items-start">
                <span class="w-44 text-zinc-400 font-medium">Jenis Pelanggaran</span>
                <span class="font-semibold text-zinc-800" id="v-p-jenis"></span>
            </div>
            <div class="flex items-start">
                <span class="w-44 text-zinc-400 font-medium">Pelapor</span>
                <span class="font-semibold text-zinc-800" id="v-p-pelapor"></span>
            </div>
            <div class="flex items-start">
                <span class="w-44 text-zinc-400 font-medium">Pengurangan Point</span>
                <span class="font-bold text-red-600" id="v-p-point"></span>
            </div>
            <div class="flex items-start">
                <span class="w-44 text-zinc-400 font-medium">Waktu</span>
                <span class="font-semibold text-zinc-800" id="v-p-waktu"></span>
            </div>

            <div class="mt-4">
                <p class="text-zinc-400 font-medium mb-2">Keterangan</p>
                <p class="text-zinc-700 leading-relaxed font-medium bg-zinc-50 p-4 rounded-xl border border-zinc-100" id="v-p-keterangan"></p>
            </div>
        </div>

        <div class="flex flex-col gap-3 mt-10">
            <button type="button" id="btn-delete-pelanggaran" class="button-danger border border-red-300">
                <span class="icon-trash w-5 h-5"></span> Delete
            </button>
            <button type="button" class="button-primary " onclick="modal_view_pelanggaran.close()">
                <span class="icon-arrow-left w-5 h-5"></span> Kembali
            </button>
        </div>
    </div>
</dialog>

<!-- Modal add pelanggaran -->
<dialog id="modal_add_pelanggaran" class="modal">
    <div class="modal-box max-w-5xl bg-white p-10 rounded-lg border border-zinc-100">
        <div class="flex flex-col gap-2 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <h2 class="text-xl font-bold text-zinc-900">Buat Pelanggaran</h2>
            <p class="text-sm text-zinc-500 font-medium">Sistem Pelanggaran Siswa</p>
        </div>

        <form method="POST" action="add_process.php" class="w-full">
            <div class="grid grid-cols-5 w-full gap-4">
                <div class="space-y-4 bg-zinc-50 p-6 rounded-lg col-span-2">
                    <div class="form-control">
                        <!-- Dropdown Level 1: Memilih Jurusan Terlebih Dahulu (Datanya diambil langsung dari PHP saat halaman di-load) -->
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
                        <!-- Dropdown Level 2: Memilih Kelas (Awalnya disabled dan kosong. Akan diisi oleh JavaScript Fetch API `get_filter_data.php?type=kelas` setelah Jurusan dipilih) -->
                        <label class="label font-bold text-zinc-700">Kelas</label>
                        <select id="select-kelas" name="kelas" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" disabled required>
                            <option value="">Pilih Jurusan Terlebih Dahulu</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <!-- Dropdown Level 3: Menentukan Siswa (Aktif setelah Kelas dipilih, datanya di-load dari `get_filter_data.php?type=siswa`) -->
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

            <div class="modal-action flex justify-end border-t pt-6 mt-8 border-zinc-200">
                <button type="button" class="button-secondary" onclick="modal_add_pelanggaran.close()">Batal</button>
                <button type="submit" class="button-primary flex flex-row items-center">
                    <span class="icon-check w-5 h-5 mr-1"></span> Simpan
                </button>
            </div>
        </form>
    </div>
</dialog>


<script>
    function openViewPelanggaran(el) {
        const get = (attr) => el.getAttribute('data-' + attr);

        // Mapping Data ke Modal
        document.getElementById('v-p-nama').innerText = get('nama');
        document.getElementById('v-p-kelas').innerText = get('kelas');
        document.getElementById('v-p-jenis').innerText = get('jenis');
        document.getElementById('v-p-pelapor').innerText = get('pelapor');
        document.getElementById('v-p-point').innerText = get('point');
        document.getElementById('v-p-waktu').innerText = get('waktu');
        document.getElementById('v-p-keterangan').innerText = get('keterangan');

        // Handle Delete Action
        const btnDel = document.getElementById('btn-delete-pelanggaran');
        btnDel.onclick = () => {
            if (confirm(`Hapus catatan pelanggaran ${get('nama')}?`)) {
                window.location.href = `delete_process.php?id=${get('id')}`;
            }
        };

        modal_view_pelanggaran.showModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selJurusan = document.getElementById('select-jurusan');
        const selKelas = document.getElementById('select-kelas');
        const selSiswa = document.getElementById('select-siswa-final');

        // [LOGIKA CHAINED DROPDOWN 1 / FETCH API]
        // Trigger: Event listener aktif saat elemen Jurusan berubah nilainya (`onchange`)
        selJurusan.addEventListener('change', async function() {
            // Encode value inputan user agar aman saat dilekatkan pada URL (Mencegah karakter aneh merusak parameter HTTP)
            const val = encodeURIComponent(this.value);
            console.log("Fetching kelas for: " + this.value);

            try {
                // Melakukan HTTP Request secara Async/Await ke Web Service mini kita, meminta daftar Kelas
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);

                // Mengkonversi format balikan dari JSON string menjadi Object/Array Javascript
                const data = await res.json();

                // Bersihkan dropdown Kelas dengan default value
                selKelas.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';

                // Melakukan perulangan, untuk setiap objek `item` di dalam `data`, buatkan elemen HTML <option>
                data.forEach(item => {
                    selKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });

                // UI UX Adjustments:
                selKelas.disabled = false; // Aktifin select kelas (Remove atribut 'disabled')
                selSiswa.disabled = true; // Riset select siswa agar disabled lagi
                selSiswa.innerHTML = '<option value="">Pilih Kelas Dulu</option>'; // Memaksa user menyelesaikan urutan pilihan
            } catch (err) {
                console.error("Fetch Error (Kelas):", err);
            }
        });

        // [LOGIKA CHAINED DROPDOWN 2 / FETCH API]
        // Trigger: Saat Kelas secara spesifik dipilih...
        selKelas.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            console.log("Fetching siswa for: " + this.value);

            try {
                // Tembak request kedua, kali ini ambil daftar Siswa berdasarkan jenis Kelas-nya
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                // Bersihkan tampilan nama-nama siswa dari aksi-aksi sebelumnya
                selSiswa.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';

                // Masukkan ulang element list <option> data murid
                data.forEach(item => {
                    // Perhatikan: Value yang disimpan adalah 'id_siswa', namun yang ditampilkan di UI adalah 'nama_siswa'
                    selSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });

                // Tampilan siap dipilih
                selSiswa.disabled = false;

            } catch (err) {
                // Tangani error, misal internet putus di tengah proses Fetch JSON
                console.error("Fetch Error (Siswa):", err);
            }
        });
    });
</script>