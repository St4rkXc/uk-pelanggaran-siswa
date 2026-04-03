<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';


// inget include database dlu ya biar bisa make $pdo
$totalSiswa = dbCount($pdo, 'Users', "role IN ('siswa')");
$totalGuruMapel = dbCount($pdo, 'Users', "role IN ('guru_mapel')");
$totalGuruBk = dbCount($pdo, 'Users', "role IN ('guru_bk')");
$totalMaster = dbcount($pdo, 'Users', "role IN ('admin')");
$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterRole = isset($_GET['filter_role']) ? $_GET['filter_role'] : '';

// [PAGINATION LOGIC]
// Menentukan jumlah maksimal data yang tampil per halaman
$limit = 10;
// Menangkap nomor halaman aktif dari URL. Jika tidak ada, default ke halaman 1.
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
// Menghitung pergeseran baris (offset) untuk kueri database
$offset = ($page - 1) * $limit;

// [FILTER DATA]
// Menyusun kondisi pencarian dan filter role agar bisa digunakan di COUNT dan SELECT
$condition = "1=1";
$params = [];

if (!empty($search)) {
    $condition .= " AND (name LIKE ?)";
    $params[] = "%$search%";
}

if (!empty($filterRole)) {
    $condition .= " AND role = ?";
    $params[] = $filterRole;
}

// Menghitung total seluruh data Users yang sesuai kriteria untuk menentukan jumlah halaman
$total_rows = dbCount($pdo, 'Users', $condition, $params);
$total_pages = ceil($total_rows / $limit);

// [QUERY DATA]
// Mengambil data Users dengan limit dan offset sesuai halaman aktif
$query = "SELECT * FROM Users WHERE $condition ORDER BY id_users DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
</head>

<body class="bg-zinc-50 overflow-x-hidden">
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class=" flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-6">
                <!-- quick info can be closed -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalSiswa ?> Siswa</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total User Siswa Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalGuruMapel ?> Guru Mapel</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total User Guru Mapel Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalGuruBk ?> Guru BK</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total User Guru BK Tercatat</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col rounded-lg border border-zinc-300 p-6 gap-6">
                        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center w-fit">
                            <span class="icon-user h-6 w-6 "></span>
                        </div>
                        <div>
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?php echo $totalMaster ?> Master</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total User Master Tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 ">
                    <div class="flex justify-between items-center">
                        <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel data User</h5>
                        <div class="flex items-center gap-2">
                            <form method="GET" id="searchForm" class="flex gap-2">
                                <div class="relative flex items-center">
                                    <input type="text"
                                        id="searchInput"
                                        name="search"
                                        value="<?= htmlspecialchars($search) ?>"
                                        class="rounded-lg border border-zinc-300 py-3 px-4 pr-10 w-65 placeholder:text-[14px] placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Cari nama..."
                                        autocomplete="off">
                                    <span class="icon-search h-4 w-4 absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600"></span>
                                </div>

                                <select name="filter_role" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 py-3 px-4 w-40 focus:outline-none bg-white">
                                    <option value="">Semua Role</option>
                                    <option value="siswa" <?= $filterRole == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                    <option value="guru_mapel" <?= $filterRole == 'guru_mapel' ? 'selected' : '' ?>>Guru Mapel</option>
                                    <option value="guru_bk" <?= $filterRole == 'guru_bk' ? 'selected' : '' ?>>Guru BK</option>
                                    <option value="admin" <?= $filterRole == 'admin' ? 'selected' : '' ?>>Master</option>
                                </select>
                            </form>
                            <div class="button-primary" onclick="modal_add_user.showModal()">Add</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="p-6 rounded-2xl border border-zinc-300">
                            <table class="w-full text-left mt-3">
                                <thead>
                                    <tr class="bg-zinc-50/50">
                                        <th class=" my-th">Nama User</th>
                                        <th class=" my-th">Access Level</th>
                                        <th class="py-3 px-4 border-b border-zinc-200"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    <?php
                                    // Use the prepared statement above so search and role filter are applied.
                                    while ($row = $stmt->fetch()):
                                        // Logic warna badge berdasarkan role
                                        $roleStyles = [
                                            'admin' => 'bg-violet-50 text-violet-700 border-violet-200',
                                            'guru_bk' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'guru_mapel' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
                                            'siswa' => 'bg-green-50 text-green-700 border-green-200'
                                        ];
                                        $roleName = [
                                            'admin' => 'Administrator',
                                            'guru_bk' => 'Guru BK',
                                            'guru_mapel' => 'Guru Mapel',
                                            'siswa' => 'Siswa'
                                        ];
                                        $style = $roleStyles[$row['role']] ?? 'bg-zinc-50 text-zinc-600 border-zinc-200';
                                        $label = $roleName[$row['role']] ?? $row['role'];
                                    ?>
                                        <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 duration-100 cursor-pointer" 
                                            onclick="openModalUser(this)"
                                            data-id="<?= $row['id_users'] ?>"
                                            data-name="<?= htmlspecialchars($row['name']) ?>"
                                            data-role="<?= htmlspecialchars($row['role']) ?>"
                                            data-password="<?= htmlspecialchars($row['password']) ?>">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="h-9 w-9 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-300  transition-colors">
                                                        <span class="text-sm font-bold text-zinc-500"><?= strtoupper(substr($row['name'], 0, 2)); ?></span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-zinc-800 font-medium"><?= htmlspecialchars($row['name']); ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[12px] font-bold tracking-tight <?= $style ?>">
                                                    <?= htmlspecialchars($label); ?>
                                                </span>
                                            </td>

                                            <td class="py-3 px-2 flex gap-3">
                                                <button type="button" class="px-3 py-1 button-secondary hover:bg-zinc-200 rounded-lg transition-all gap-2 flex items-center"
                                                    onclick="event.stopPropagation(); openEditUserModal(this.closest('tr'))">
                                                    <span class="icon-edit h-4 w-4 text-zinc-600"></span>
                                                    <p class="">Edit</p>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                             </table>
                             <?php include BASE_PATH . '/includes/ui/pagination/pagination.php'; ?>
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
        }, 500);
    });


    window.onload = () => {
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }
    };

    function openModalUser(el) {
        const get = (attr) => el.getAttribute('data-' + attr);
        const name = get('name');
        const role = get('role');
        const id = get('id');
        const password = get('password');

        document.getElementById('v-name').innerText = name;
        document.getElementById('v-role').innerText = role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ');
        document.getElementById('v-username-display').innerText = name;
        document.getElementById('v-password-display').innerText = password.substring(0, 20) + '...';

        const btnDel = document.getElementById('btn-delete-user');
        btnDel.onclick = () => {
            if (confirm(`Apakah Anda yakin ingin menghapus user "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
                window.location.href = `delete_process.php?id=${id}`;
            }
        };

        modal_view_user.showModal();
    }


    function openEditUserModal(el) {
        const get = (attr) => el.getAttribute('data-' + attr);

        document.getElementById('edit-user-id').value = get('id');
        document.getElementById('edit-user-name').value = get('name');
        document.getElementById('edit-user-role').value = get('role');

        modal_edit_user.showModal();
    }
</script>

<dialog id="modal_add_user" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="space-y-1 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Tambah data User</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Sistem Pelanggaran Siswa</p>
            </div>
        </div>

        <form method="POST" action="add_process.php">
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Username / Name</label>
                    <input type="text" name="name" placeholder="Masukkan nama/username" class="my-input w-full" required />
                </div>
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" class="my-input w-full" required />
                </div>
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Role</label>
                    <select name="role" class="my-select w-full" required>
                        <option disabled selected>Pilih Role</option>
                        <option value="admin">Master</option>
                        <option value="guru_bk">Guru BK</option>
                        <option value="guru_mapel">Guru Mapel</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Link ke Profil Siswa (Opsional)</label>
                    <select name="id_siswa" id="select-siswa" placeholder="Ketik nama siswa..." autocomplete="off" class="my-select">
                        <option value="">-- Cari Nama Siswa --</option>
                        <?php
                        $siswaQuery = $pdo->query("SELECT id_siswa, nama_siswa, kelas FROM siswa ORDER BY nama_siswa ASC");
                        while ($s = $siswaQuery->fetch()) {
                            // Kita tambahin info kelas biar gak ketuker kalau ada nama yang sama
                            echo "<option value='{$s['id_siswa']}'>{$s['nama_siswa']} ({$s['kelas']})</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="modal-action border-t pt-6 mt-8 border-zinc-200">
                <button type="button" class="button-secondary" onclick="modal_add_user.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Buat User
                </button>
            </div>
        </form>
    </div>
</dialog>
<script>
    // Inisialisasi Tom-Select
    var settings = {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        render: {
            option: function(data, escape) {
                return '<div class=" hover:bg-zinc-100 cursor-pointer border-0 font-paragraph-14">' + escape(data.text) + '</div>';
            },
            item: function(data, escape) {
                return '<div class="text-zinc-800 font-medium font-paragraph-14">' + escape(data.text) + '</div>';
            }
        }
    };

    var tomSelect = new TomSelect("#select-siswa", settings);

    document.querySelector('select[name="role"]').addEventListener('change', function() {
        if (this.value !== 'siswa') {
            tomSelect.disable();
            tomSelect.clear();
        } else {
            tomSelect.enable();
        }
    });

    // Pas dropdown siswa berubah, otomatis isi input 'name'
    tomSelect.on('change', function(value) {
        if (value) {
            // Ambil label teks dari opsi yang dipilih
            const selectedLabel = tomSelect.getItem(value).innerText;
            // Bersihin string (ilangin info kelas dsb)
            const cleanName = selectedLabel.split('(')[0].trim();

            // Isi ke input name
            document.querySelector('input[name="name"]').value = cleanName;
        }
    });
</script>

<!-- User Details Modal -->
<dialog id="modal_view_user" class="modal">
    <div class="modal-box w-11/12 max-w-md bg-white p-0 overflow-hidden rounded-3xl border border-zinc-200">
        <!-- Header with Background -->
        <div class="h-24 bg-zinc-300 relative">
            <div class="absolute -bottom-10 left-8">
                <div class="p-1 bg-white rounded-2xl shadow-sm">
                    <div class="h-20 w-20 rounded-2xl bg-zinc-100 flex items-center justify-center border border-zinc-200">
                        <span class="icon-user h-10 w-10 text-zinc-600"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-14 p-8">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-zinc-900" id="v-name"></h3>
                <p class="text-indigo-600 font-semibold" id="v-role"></p>
            </div>

            <div class="space-y-4 py-6 border-y border-zinc-100">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 font-medium text-sm text-[14px]">Username</span>
                    <span class="text-zinc-800 font-semibold" id="v-username-display"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 font-medium text-sm text-[14px]">Password Hash</span>
                    <span class="text-zinc-400 font-mono text-[11px] truncate max-w-48" id="v-password-display"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 font-medium text-sm text-[14px]">Status Akun</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-bold border border-green-100">AKTIF</span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
                <button type="button" class="px-4 py-3 rounded-xl font-semibold border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition-all text-sm" onclick="modal_view_user.close()">
                    Tutup
                </button>
                <button type="button" id="btn-delete-user" class="px-4 py-3 rounded-xl font-semibold bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all text-sm flex items-center justify-center gap-2">
                    <span class="icon-delete h-4 w-4"></span>
                    Hapus User
                </button>
            </div>
        </div>
    </div>
</dialog>

<dialog id="modal_edit_user" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8 text-left">
        <div class="space-y-1 border-b pb-6 mb-6 border-zinc-200">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-12.5  ">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Ubah data User</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Sistem Pelanggaran Siswa</p>
            </div>
        </div>
        <form method="POST" action="edit_process.php">
            <input type="hidden" name="id_users" id="edit-user-id">
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Username / Name</label>
                    <input type="text" name="name" id="edit-user-name" class="my-input w-full" required />
                </div>
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Password Baru (Kosongkan jika tidak ganti)</label>
                    <input type="password" name="password" placeholder="********" class="my-input w-full" />
                </div>
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Role</label>
                    <select name="role" id="edit-user-role" class="my-select w-full" required>
                        <!-- <option disabled selected>Pilih Role</option> -->
                        <option value="admin">Master</option>
                        <option value="guru_bk">Guru BK</option>
                        <option value="guru_mapel">Guru Mapel</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>
            <div class="modal-action border-t pt-6 mt-8 border-zinc-200">
                <button type="button" class="button-secondary" onclick="modal_edit_user.close()">Batal</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</dialog>