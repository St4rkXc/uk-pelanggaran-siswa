<?php
session_start(); // Memulai/melanjutkan sesi dari pengguna yang login

// [OTORISASI AKSES]
// Cek Role: Pengawasan ini dipasang agar yang dapat membuka page Daftar Siswa hanyalah role 'admin' atau 'guru_bk'
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    // Jika rolenya tidak cocok atau belum login, blokir/tolak akses proses (biasa dikelola oleh router utama)
    exit;
}

// Mengimpor koneksi database beserta fungsi helper dan pengecekan akses (middleware)
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

// Menyiapkan variabel lokal yang dipakai di halaman seperti path gambar dan profile user sekarang
$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';
$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

// [WIDGET STATISTIK] Menggunakan helper dbCount untuk menghitung total baris dan ditampilkan di atas halaman
$jumlahSiswa = dbCount($pdo, 'Siswa');
$jumlahPelanggaran = dbCount($pdo, 'Pelanggaran');

// [LOGIKA FILTER & PENCARIAN (READ DATA)]
// 1. Menangkap inputan pencarian (Jika ada), contoh: mengetikkan nama Budi
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// 2. Menangkap filter pilihan dropdown
$jurusan_filter = isset($_GET['jurusan_filter']) ? trim($_GET['jurusan_filter']) : '';
$kelas_filter = isset($_GET['kelas_filter']) ? trim($_GET['kelas_filter']) : '';

// 3. Mengambil opsi-opsi list Jurusan dan Kelas untuk ditampilkan di dropdown Filter (Tanpa nilai kembar/DISTINCT)
$all_jurusan = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC")->fetchAll(PDO::FETCH_COLUMN);
$all_kelas = $pdo->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC")->fetchAll(PDO::FETCH_COLUMN);

// 4. Menyusul fondasi awal kueri database untuk menampilkan list
$query = "SELECT * FROM siswa WHERE 1=1";
$params = [];

// Menyambungkan perintah `AND` ke query Master apabila salah satu box Search/Filter terisi (Pencarian Dinamis)
if (!empty($search)) {
    // Mencari kriteria nama atau nis yang memiliki huruf mirip
    $query .= " AND (nama_siswa LIKE ? OR nis LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($jurusan_filter)) {
    // Menyaring presisi berdasarkan Jurusan
    $query .= " AND jurusan = ?";
    $params[] = $jurusan_filter;
}

if (!empty($kelas_filter)) {
    // Menyaring presisi berdasarkan Kelas
    $query .= " AND kelas = ?";
    $params[] = $kelas_filter;
}

// 5. Mengurutkan hasil dari Siswa paling baru diregistrasikan. Dan mengeksekusinya di PDO (Prepared Statement)
$query .= " ORDER BY id_siswa DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
    <div class="flex w-full h-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class="flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-6 gap-6">
                <div class="grid grid-cols-4 gap-4">
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6"></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($jumlahSiswa); ?> Siswa</h5>
                            <p class="font-paragraph-14 font-medium text-zinc-600">Total Siswa Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-siren h-6 w-6"></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($jumlahPelanggaran) ?> Pelanggaran</h5>
                            <p class="font-paragraph-14 font-medium text-zinc-600">Total Pelanggaran Tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="w-full mt-6 gap-4">
                    <form method="GET" id="searchForm" class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel Data Siswa</h5>
                            <div class="flex gap-2">
                                <div class="flex gap-2">
                                    <select name="jurusan_filter" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="">Semua Jurusan</option>
                                        <?php foreach ($all_jurusan as $j): ?>
                                            <option value="<?= htmlspecialchars($j) ?>" <?= $jurusan_filter == $j ? 'selected' : '' ?>><?= htmlspecialchars($j) ?></option>
                                        <?php endforeach; ?>
                                    </select>
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
                                            placeholder="Cari nama atau NIS..."
                                            autocomplete="off">
                                        <span class="icon-search h-4 w-4 absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600"></span>
                                    </div>
                                </div>
                                <button type="button" class="button-primary flex items-center justify-center" onclick="modal_add_siswa.showModal()">Add</button>
                            </div>
                        </div>
                    </form>

                    <div class="rounded-2xl border border-zinc-300 p-6 h-fit mt-4">
                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr class="text-zinc-800 font-paragraph-16 font-medium">
                                        <th class="my-th">Nama</th>
                                        <th class="my-th">Kelas</th>
                                        <th class="my-th">NIS</th>
                                        <th class="my-th">NISN</th>
                                        <th class="my-th">Poin</th>
                                        <th class="my-th">Jurusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    <?php
                                    $no = 1;
                                    while ($row = $stmt->fetch()):
                                    ?>
                                        <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 transition-colors cursor-pointer"
                                            onclick="openModalSiswa(this)"
                                            data-id="<?= $row['id_siswa']; ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_siswa']); ?>"
                                            data-kelas="<?= htmlspecialchars($row['kelas']); ?>"
                                            data-nis="<?= htmlspecialchars($row['nis']); ?>"
                                            data-nisn="<?= htmlspecialchars($row['nisn']); ?>"
                                            data-point="<?= htmlspecialchars($row['point']); ?>"
                                            data-jurusan="<?= htmlspecialchars($row['jurusan']); ?>"
                                            data-jk="<?= $row['jenis_kelamin'] == 1 ? 'Laki-laki' : 'Perempuan'; ?>"
                                            data-alamat="<?= htmlspecialchars($row['alamat_rumah'] ?? '-'); ?>"
                                            data-ortu="<?= htmlspecialchars($row['nama_ortu'] ?? '-'); ?>"
                                            data-kerja-ortu="<?= htmlspecialchars($row['pekerjaan_ortu'] ?? '-'); ?>"
                                            data-telp-ortu="<?= htmlspecialchars($row['nomor_ortu'] ?? '-'); ?>"
                                            data-status="<?= $row['status']; ?>">

                                            <td class="p-4 text-zinc-800 font-medium flex gap-4 items-center">
                                                <div class="p-3 bg-zinc-100 border border-zinc-300 rounded-full flex justify-center items-center">
                                                    <span class="icon-user h-4 w-4 text-zinc-500"></span>
                                                </div>
                                                <?= htmlspecialchars($row['nama_siswa']); ?>
                                            </td>
                                            <td class="p-4 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                            <td class="p-4 text-zinc-600"><?= htmlspecialchars($row['nis']); ?></td>
                                            <td class="p-4 text-zinc-600"><?= htmlspecialchars($row['nisn']); ?></td>
                                            <td class="p-4">
                                                <div class="flex items-center gap-2 text-center py-1 px-4 h-fit w-fit rounded-full text-[12px] font-medium <?php
                                                                                                                                                            $point = (int)$row['point'];
                                                                                                                                                            if ($point < 20) {
                                                                                                                                                                echo 'bg-red-200 text-red-800';
                                                                                                                                                            } elseif ($point < 50) {
                                                                                                                                                                echo 'bg-yellow-200 text-yellow-800';
                                                                                                                                                            } else {
                                                                                                                                                                echo 'bg-green-200 text-green-800';
                                                                                                                                                            }
                                                                                                                                                            ?>">
                                                    <span class="relative flex size-2">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full <?php
                                                                                                                                    $point = (int)$row['point'];
                                                                                                                                    if ($point < 20) {
                                                                                                                                        echo 'bg-red-500';
                                                                                                                                    } elseif ($point < 50) {
                                                                                                                                        echo 'bg-yellow-500';
                                                                                                                                    } else {
                                                                                                                                        echo 'bg-green-500';
                                                                                                                                    }
                                                                                                                                    ?> opacity-75"></span>
                                                        <span class="relative inline-flex size-2 rounded-full <?php
                                                                                                                $point = (int)$row['point'];
                                                                                                                if ($point < 20) {
                                                                                                                    echo 'bg-red-600';
                                                                                                                } elseif ($point < 50) {
                                                                                                                    echo 'bg-yellow-600';
                                                                                                                } else {
                                                                                                                    echo 'bg-green-600';
                                                                                                                }
                                                                                                                ?>"></span>
                                                    </span>
                                                    <?= htmlspecialchars($row['point']); ?>
                                                </div>
                                            </td>
                                            <td class="p-4 pl-2 text-zinc-600"><?= htmlspecialchars($row['jurusan']); ?></td>
                                        </tr>
                                    <?php $no++;
                                    endwhile; ?>

                                    <?php if ($no === 1): ?>
                                        <tr>
                                            <td colspan="6" class="p-6 text-center text-zinc-500 italic">
                                                Data tidak ditemukan. Coba kata kunci lain atau filter yang berbeda.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</body>

</html>
<script>
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let timer;

    // Search with debounce
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            searchForm.submit();
        }, 500);
    });

    // Auto focus search input on load
    window.onload = () => {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    };

    /**
     * Opens the student details modal and populates view/edit fields.
     * @param {HTMLElement} el The table row element containing data attributes.
     */
    function openModalSiswa(el) {
        const get = (attr) => el.getAttribute('data-' + attr);

        // Map data attributes to view/edit element IDs
        const fieldMapping = {
            'm-nama': get('nama'),
            'm-kelas': get('kelas'),
            'm-point': get('point'),
            'm-status': get('status'),
            'm-jk': get('jk'),
            'm-nis': get('nis'),
            'm-nisn': get('nisn'),
            'm-alamat': get('alamat'),
            'm-jurusan': get('jurusan'),
            'm-nama-ortu': get('ortu'),
            'm-kerja-ortu': get('kerja-ortu'),
            'm-telp-ortu': get('telp-ortu'),
            'edit-id': get('id'),
            'edit-nama': get('nama'),
            'edit-nis': get('nis'),
            'edit-nisn': get('nisn'),
            'edit-alamat': get('alamat'),
            'edit-nama-ortu': get('ortu'),
            'edit-kerja-ortu': get('kerja-ortu'),
            'edit-telp-ortu': get('telp-ortu'),
            'edit-point': get('point'),
            'edit-status': get('status')
        };

        // Populate text and values
        Object.entries(fieldMapping).forEach(([id, value]) => {
            const target = document.getElementById(id);
            if (!target) return;

            if (target.tagName === 'INPUT' || target.tagName === 'SELECT') {
                target.value = value || '';
            } else {
                target.innerText = value || '-';
            }

            // Handle status badge styling
            if (id === 'm-status') {
                target.className = 'badge font-medium ' + (
                    value === 'Aktif' ? 'badge-success' :
                    value === 'Pindah' ? 'badge-warning' :
                    'badge-error text-white'
                );
            }
        });

        // Handle special selects
        const setSelect = (id, val) => {
            const target = document.getElementById(id);
            if (target) target.value = val;
        };
        setSelect('edit-kelas', get('kelas'));
        setSelect('edit-jurusan', get('jurusan'));
        setSelect('edit-jenis-kelamin', get('jk') === 'Laki-laki' ? '1' : '0');

        modal_view_siswa.showModal();
    }
</script>

<dialog id="modal_add_siswa" class="modal">
    <div class="modal-box w-11/12 max-w-4xl bg-white p-8">
        <div class="space-y-1 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Tambah data siswa</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Sistem Pelanggaran Siswa</p>
            </div>
        </div>

        <form method="POST" action="add_process.php">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nama Lengkap</span></label>
                        <input type="text" name="nama_siswa" placeholder="Masukkan nama siswa" class="my-input" required />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Jurusan</span></label>
                        <select name="jurusan" class="my-select w-full" required>
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                            <option value="Teknik Komputer Jaringan">Teknik Komputer Jaringan</option>
                            <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                            <option value="Animasi">Animasi</option>
                            <option value="Bisnis Digital">Bisnis Digital</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Kelas</span></label>
                        <select name="kelas" class="my-select w-full" required>
                            <option value="" disabled selected>Pilih Kelas</option>
                            <option value="XII RPL 1">XII RPL 1</option>
                            <option value="XII RPL 2">XII RPL 2</option>
                            <option value="XII RPL 3">XII RPL 3</option>
                            <option value="XII RPL 4">XII RPL 4</option>
                            <option value="XII RPL 5">XII RPL 5</option>
                            <option value="XII DKV 1">XII DKV 1</option>
                            <option value="XII DKV 2">XII DKV 2</option>
                            <option value="XII DKV 3">XII DKV 3</option>
                            <option value="XII DKV 4">XII DKV 4</option>
                            <option value="XII TKJ 1">XII TKJ 1</option>
                            <option value="XII TKJ 2">XII TKJ 2</option>
                            <option value="XII BD 1">XII BD 1</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">NIS</span></label>
                        <input type="number" name="nis" placeholder="Contoh: 12345" class="my-input w-full" required />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">NISN</span></label>
                        <input type="number" name="nisn" placeholder="Contoh: 00123456" class="my-input w-full" required />
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Jenis Kelamin</span></label>
                        <select name="jenis_kelamin" class="my-select w-full" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="1">Laki-laki</option>
                            <option value="0">Perempuan</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Alamat Rumah</span></label>
                        <input type="text" name="alamat_rumah" placeholder="Jl. Kamboja No. 12" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nama Orang Tua</span></label>
                        <input type="text" name="nama_ortu" placeholder="Nama ayah/ibu" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Pekerjaan Orang Tua</span></label>
                        <input type="text" name="pekerjaan_ortu" placeholder="Contoh: PNS / Wiraswasta" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nomor HP Orang Tua</span></label>
                        <input type="text" name="nomor_ortu" placeholder="08123456789" class="my-input w-full" />
                    </div>

                    <div class="space-y-2 hidden">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Poin Awal</span></label>
                        <input type="number" name="point" value="100" class="my-input w-full font-bold text-zinc-800 bg-zinc-100" />
                    </div>
                </div>
            </div>

            <div class="modal-action mt-10 gap-2 border-t border-zinc-200 pt-6 ">
                <button type="button" class="button-secondary" onclick="modal_add_siswa.close()">
                    Batal
                </button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-6 h-6 text-white"></span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_view_siswa" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="space-y-1 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Lihat Data Siswa</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Sistem Pelanggaran Siswa</p>
            </div>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-zinc-800" id="m-nama"></h1>
            <p class="text-zinc-500"><span id="m-kelas"></span>, <span id="m-point" class="font-bold text-zinc-800"></span> Point</p>
        </div>

        <div class="grid grid-cols-1 gap-y-3 text-sm">
            <div class="flex"><span class="w-40 text-zinc-500">Jenis Kelamin</span><span class="font-medium" id="m-jk"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">NIS</span><span class="font-medium" id="m-nis"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">NISN</span><span class="font-medium" id="m-nisn"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Alamat Rumah</span><span class="font-medium" id="m-alamat"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Jurusan</span><span class="font-medium" id="m-jurusan"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Orang Tua</span><span class="font-medium" id="m-nama-ortu"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Pekerjaan</span><span class="font-medium" id="m-kerja-ortu"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Nomor Orang Tua</span><span class="font-medium" id="m-telp-ortu"></span></div>
            <div class="flex"><span class="w-40 text-zinc-500">Status Siswa</span><span class="font-medium text-zinc-900" id="m-status"></span></div>
        </div>
        <div class="modal-action grid grid-cols-2 gap-4 py-6 mt-6 border-t border-zinc-200">
            <button type="button" class="btn bg-zinc-100 border-zinc-200 text-zinc-800"
                onclick="modal_view_siswa.close(); modal_edit_siswa.showModal()">
                <span class="icon-edit"></span> Edit
            </button>
            <button type="button" class="btn bg-white border-zinc-300 text-zinc-700" onclick="modal_view_siswa.close()">
                Kembali
            </button>
        </div>
    </div>
    </div>
</dialog>

<dialog id="modal_edit_siswa" class="modal">
    <div class="modal-box w-11/12 max-w-4xl bg-white p-8">
        <div class="space-y-1 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Ubah data siswa</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Perbarui informasi data siswa di bawah ini.</p>
            </div>
        </div>

        <form method="POST" action="edit_process.php">
            <input type="hidden" name="id_siswa" id="edit-id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-left">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nama Lengkap</span></label>
                        <input type="text" name="nama_siswa" id="edit-nama" placeholder="Masukkan nama siswa" class="my-input w-full" required />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Jurusan</span></label>
                        <select name="jurusan" id="edit-jurusan" class="my-select w-full" required>
                            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                            <option value="Teknik Komputer Jaringan">Teknik Komputer Jaringan</option>
                            <option value="Multimedia">Multimedia</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Kelas</span></label>
                        <select name="kelas" id="edit-kelas" class="my-select w-full" required>
                            <option value="XII RPL 1">XII RPL 1</option>
                            <option value="XII RPL 2">XII RPL 2</option>
                            <option value="XII RPL 3">XII RPL 3</option>
                            <option value="XII TKJ 1">XII TKJ 1</option>
                            <option value="XII TKJ 2">XII TKJ 2</option>
                            <option value="XII MM 1">XII MM 1</option>
                            <option value="XII MM 2">XII MM 2</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">NIS</span></label>
                        <input type="number" name="nis" id="edit-nis" placeholder="Contoh: 12345" class="my-input w-full" required />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">NISN</span></label>
                        <input type="number" name="nisn" id="edit-nisn" placeholder="Contoh: 00123456" class="my-input w-full" required />
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Jenis Kelamin</span></label>
                        <select name="jenis_kelamin" id="edit-jenis-kelamin" class="my-select w-full" required>
                            <option value="1">Laki-laki</option>
                            <option value="0">Perempuan</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Alamat Rumah</span></label>
                        <input type="text" name="alamat_rumah" id="edit-alamat" placeholder="Jl. Kamboja No. 12" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nama Orang Tua</span></label>
                        <input type="text" name="nama_ortu" id="edit-nama-ortu" placeholder="Nama ayah/ibu" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Pekerjaan Orang Tua</span></label>
                        <input type="text" name="pekerjaan_ortu" id="edit-kerja-ortu" placeholder="Contoh: PNS / Wiraswasta" class="my-input w-full" />
                    </div>

                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Nomor HP Orang Tua</span></label>
                        <input type="text" name="nomor_ortu" id="edit-telp-ortu" placeholder="08123456789" class="my-input w-full" />
                    </div>

                    <div class="space-y-2 hidden">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Poin</span></label>
                        <input type="number" name="point" id="edit-point" class="my-input w-full font-bold text-zinc-800 bg-zinc-100" />
                    </div>
                    <div class="space-y-2">
                        <label class="label"><span class="label-text font-semibold text-zinc-600">Status</span></label>
                        <select name="status" id="edit-status" class="my-select w-full" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Pindah">Pindah</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-action mt-gap-2 pb-6 mt-6 pt-6 border-t border-zinc-200">
                <button type="button" class="btn btn-ghost" onclick="modal_edit_siswa.close()">Batal</button>
                <button type="submit" class="btn bg-zinc-900 hover:bg-zinc-800 text-white w-40 border-none">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</dialog>