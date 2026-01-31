<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$jumlahSiswa = dbCount($pdo, 'Siswa');
$jumlahPelanggaran = dbCount($pdo, 'Pelanggaran');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterType = isset($_GET['filter_type']) ? $_GET['filter_type'] : '';
$query = "SELECT * FROM siswa";
$params = [];

if (!empty($search)) {
    $query .= " WHERE nama_siswa LIKE ? OR nis LIKE ?";
    $params = ["%$search%", "%$search%"];
}
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
                <div class="grid grid-cols-4 gap-4">
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($jumlahSiswa); ?> Siswa</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Siswa Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($jumlahPelanggaran) ?> Pelanggaran</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Pelanggaran Tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="w-full mt-6 gap-4">
                    <div class="flex justify-between items-center">
                        <h5 class="font-paragraph-16 font-semibold  text-zinc-800">Tabel Data Siswa</h5>
                        <div class="flex gap-2">
                            <form method="GET" id="searchForm" class="gap-2 flex">
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

                                <select name="filter_type" onchange="this.form.submit()" class="p-3 rounded-lg border border-zinc-300 focus:outline-none bg-white">
                                    <!-- <option value="" <?= $filterType == '' ? 'selected' : '' ?>>Semua Data</option> -->
                                    <option value="siswa" <?= $filterType == 'siswa' ? 'selected' : '' ?>>Data Siswa</option>
                                    <option value="pelanggaran" <?= $filterType == 'pelanggaran' ? 'selected' : '' ?>>Data Pelanggaran</option>
                                </select>
                            </form>
                            <!-- Modal Open Daisy UI -->
                            <button class="button-primary flex items-center justify-center" onclick="my_modal_1.showModal()">Add</button>

                        </div>
                    </div>
                    <div class="rounded-2xl border border-zinc-300 p-6 h-fit mt-4">
                        <div class="mt-3">
                            <?php if ($filterType === 'siswa' || $filterType === ''): ?>
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
                                        while ($row = $stmt->fetch()):
                                        ?>
                                            <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 transition-colors cursor-pointer "
                                                onclick="openModalSiswa(this)"
                                                data-nama="<?= htmlspecialchars($row['nama_siswa']); ?>"
                                                data-kelas="<?= htmlspecialchars($row['kelas']); ?>"
                                                data-nis="<?= htmlspecialchars($row['nis']); ?>"
                                                data-nisn="<?= htmlspecialchars($row['nisn']); ?>"
                                                data-point="<?= htmlspecialchars($row['point']); ?>"
                                                data-jurusan="<?= htmlspecialchars($row['jurusan']); ?>">

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
                                                <td colspan="7" class="p-6 text-center text-zinc-500 italic">
                                                    Data "<?= htmlspecialchars($search) ?>" nggak nemu Coba kata kunci lain.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php endif;  ?>
                        </div>
                        <?php if ($filterType === 'pelanggaran'): ?>
                            <div class="mt-3">
                                <table class="w-full text-left table-auto">
                                    <thead>
                                        <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                            <th class="">No</th>
                                            <th class="">Nama</th>
                                            <th class="">Kelas</th>
                                            <th class="">Pelanggaran</th>
                                            <th class="">Pelapor</th>
                                            <th class="">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200">

                                        <?php
                                        $no = 1;
                                        $stmtPelanggaran = $pdo->prepare("SELECT p.*, s.nama_siswa, s.kelas FROM pelanggaran p JOIN siswa s ON p.id_siswa = s.id_siswa WHERE s.nama_siswa LIKE ? OR s.nis LIKE ? ORDER BY p.id_pelanggaran DESC");
                                        while ($row = $stmtPelanggaran->fetch()):
                                        ?>
                                            <tr class="border-b border-b-zinc-300 hover:bg-zinc-50 transition-colors">
                                                <td class="py-3 text-zinc-700"><?= $no++; ?></td>
                                                <td class="py-3 text-zinc-800 font-medium"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                <td class="py-3 text-zinc-800 font-medium"><?= htmlspecialchars($row['kelas']); ?></td>
                                                <td class="py-3 text-zinc-600"><?= htmlspecialchars($row['pelanggaran']); ?></td>
                                                <td class="py-3 text-zinc-600"><?= htmlspecialchars($row['pelapor']); ?></td>
                                                <td class="py-3 text-zinc-600"><?= htmlspecialchars($row['waktu']); ?></td>

                                            </tr>
                                        <?php endwhile; ?>

                                        <?php if ($no === 1): ?>
                                            <tr>
                                                <td colspan="7" class="p-6 text-center text-zinc-500 italic">
                                                    Data "<?= htmlspecialchars($search) ?>" Data tidak ada
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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

    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            searchForm.submit();
        }, 500); // submit setiap 500 ms 
    });
    window.onload = () => {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    };

    function openModalSiswa(el) {
        const nama = el.getAttribute('data-nama');
        const kelas = el.getAttribute('data-kelas');
        const nis = el.getAttribute('data-nis');
        const nisn = el.getAttribute('data-nisn');
        const point = el.getAttribute('data-point');
        const jurusan = el.getAttribute('data-jurusan');

        // 2. Masukin data ke dalem elemen modal berdasarkan id
        document.getElementById('m-nama').innerText = nama;
        document.getElementById('m-kelas').innerText = kelas;
        document.getElementById('m-nis').innerText = nis;
        document.getElementById('m-nisn').innerText = nisn;
        document.getElementById('m-jurusan').innerText = jurusan;
        document.getElementById('m-point').innerText = point;

        // 3. Tampilkan modalnya (DaisyUI method)
        modal_view_siswa.showModal();
    }
</script>

<dialog id="my_modal_1" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Ini Modal 1!</h3>
        <p class="py-4">Press ESC key or click the button below to close</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>
</dialog>

<dialog id="modal_view_siswa" class="modal">
    <div class="modal-box ">
        <h3 class="text-lg font-bold border-b pb-2">Detail Siswa</h3>
        <div class="py-4 space-y-2">
            <p><strong>Nama:</strong> <span id="m-nama"></span></p>
            <p><strong>Kelas:</strong> <span id="m-kelas"></span></p>
            <p><strong>NIS/NISN:</strong> <span id="m-nis"></span> / <span id="m-nisn"></span></p>
            <p><strong>Jurusan:</strong> <span id="m-jurusan"></span></p>
            <p><strong>Total Poin:</strong> <span id="m-point" class="badge badge-error text-white font-bold"></span></p>
        </div>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Tutup</button>
            </form>
        </div>
    </div>
</dialog>