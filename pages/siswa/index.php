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

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

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
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
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
                            <span class="icon-siren h-6 w-6 "></span>
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
                                    <option value="siswa" <?= $filterType == 'siswa' ? 'selected' : '' ?>>Data Siswa</option>
                                    <option value="pelanggaran" <?= $filterType == 'pelanggaran' ? 'selected' : '' ?>>Data Pelanggaran</option>
                                </select>
                            </form>

                            <button class="button-primary flex items-center justify-center" onclick="modal_add_siswa.showModal()">Add</button>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-zinc-300 p-6 h-fit mt-4">
                        <div class="mt-3">
                            <?php if ($filterType === 'siswa' || $filterType === ''): ?>
                                <table class="w-full text-left table-auto">
                                    <thead>
                                        <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                            <th class="px-2">No</th>
                                            <th class="px-2">Nama</th>
                                            <th class="px-2">Kelas</th>
                                            <th class="px-2">NIS</th>
                                            <th class="px-2">NISN</th>
                                            <th class="px-2">Poin</th>
                                            <th class="px-2">Jurusan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200">
                                        <?php
                                        $no = 1;
                                        while ($row = $stmt->fetch()):
                                        ?>
                                            <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 transition-colors cursor-pointer "
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

                                                <td class="px-2 py-3 text-zinc-700"><?= $no++; ?></td>
                                                <td class="px-2 py-3 text-zinc-800 font-medium"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                <td class="px-2 py-3 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                                <td class="px-2 py-3 text-zinc-600"><?= htmlspecialchars($row['nis']); ?></td>
                                                <td class="px-2 py-3 text-zinc-600"><?= htmlspecialchars($row['nisn']); ?></td>
                                                <td class="px-2 py-3 text-zinc-600"><?= htmlspecialchars($row['point']); ?></td>
                                                <td class="px-2 py-3 text-zinc-600"><?= htmlspecialchars($row['jurusan']); ?></td>
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
        const get = (attr) => el.getAttribute('data-' + attr);


        const viewFields = {
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
            'm-telp-ortu': get('telp-ortu')
        };

        Object.keys(viewFields).forEach(id => {
            const target = document.getElementById(id);
            if (target) {
                target.innerText = viewFields[id] || '-';

                if (id === 'm-status') {
                    target.className = 'badge font-medium ';
                    if (viewFields[id] === 'Aktif') target.classList.add('badge-success');
                    else if (viewFields[id] === 'Pindah') target.classList.add('badge-warning');
                    else target.classList.add('badge-error', 'text-white');
                }
            }
        });


        const editFields = {
            'edit-id': get('id'),
            'edit-nama': get('nama'),
            'edit-nis': get('nis'),
            'edit-nisn': get('nisn'),
            'edit-alamat': get('alamat'),
            'edit-nama-ortu': get('ortu'),
            'edit-kerja-ortu': get('kerja-ortu'),
            'edit-telp-ortu': get('telp-ortu'),
            'edit-point': get('point'),
            'edit-status': get('status') // Set value select status
        };

        Object.keys(editFields).forEach(id => {
            const target = document.getElementById(id);
            if (target) target.value = editFields[id] || '';
        });


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
        <div class="space-y-1 pb-8">
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

            <div class="modal-action mt-10 gap-2">
                <button type="button" class="btn bg-white border-zinc-300 text-zinc-700" onclick="modal_add_siswa.close()">
                    Batal
                </button>
                <button type="submit" class="btn bg-zinc-900 hover:bg-zinc-800 text-white w-32 border-none">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_view_siswa" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="flex flex-col gap-2 mb-8">
            <div class="p-2 border border-zinc-200 rounded-lg w-fit">
                <img src="<?= $imgPath; ?>" class="h-10">
            </div>
            <h2 class="text-xl font-bold text-zinc-900">Data Siswa</h2>
            <p class="text-sm text-zinc-500">Sistem Pelanggaran Siswa</p>
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
        <div class="modal-action grid grid-cols-2 gap-4 mt-10">
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
        <div class="space-y-1 pb-8">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5">
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

            <div class="modal-action mt-10 gap-2">
                <button type="button" class="btn btn-ghost" onclick="modal_edit_siswa.close()">Batal</button>
                <button type="submit" class="btn bg-zinc-900 hover:bg-zinc-800 text-white w-40 border-none">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</dialog>