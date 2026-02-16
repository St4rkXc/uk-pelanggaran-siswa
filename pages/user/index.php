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
// Ganti filter_type jadi filter_role biar sesuai konteks
$filterRole = isset($_GET['filter_role']) ? $_GET['filter_role'] : '';

$query = "SELECT * FROM Users WHERE 1=1"; // Pakai 1=1 biar gampang nambahin AND
$params = [];

// Filter Search (Username/Name)
if (!empty($search)) {
    $query .= " AND (name LIKE ?)";
    $params[] = "%$search%";
}

// Filter Role
if (!empty($filterRole)) {
    $query .= " AND role = ?";
    $params[] = $filterRole;
}

$query .= " ORDER BY id_users DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
</head>

<body class="bg-zinc-50 w-dvw">
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
                        <div class="p-6 rounded-xl border border-zinc-300">
                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr class="bg-zinc-50 text-zinc-800 font-paragraph-16 font-medium">
                                        <th class="py-3 px-2">No</th>
                                        <th class="py-3 px-2">Username</th>
                                        <th class="py-3 px-2">Role</th>
                                        <th class="py-3 px-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    <?php $no = 1;
                                    while ($row = $stmt->fetch()): ?>
                                        <tr class="border-b border-b-zinc-300 hover:bg-zinc-100 transition-colors cursor-pointer"
                                            onclick="openModalUser(this)"
                                            data-id="<?= $row['id_users']; ?>"
                                            data-name="<?= htmlspecialchars($row['name']); ?>"
                                            data-role="<?= htmlspecialchars($row['role']); ?>"
                                            data-id-siswa="<?= $row['id_siswa'] ?? ''; ?>">

                                            <td class="py-3 px-2 text-zinc-700"><?= $no++; ?></td>
                                            <td class="py-3 px-2 text-zinc-800 font-medium"><?= htmlspecialchars($row['name'] ?? ''); ?></td>
                                            <td class="py-3 px-2">
                                                <span class="badge badge-ghost capitalize"><?= htmlspecialchars($row['role']); ?></span>
                                            </td>

                                            <td class="py-3 px-2">
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
        }, 500); // Tunggu 500ms setelah ngetik baru submit
    });

    // Biar kursor nggak pindah ke depan setelah reload
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

        document.getElementById('v-name').innerText = get('name');
        document.getElementById('v-role').innerText = get('role');

        const btnDel = document.getElementById('btn-delete-user');
        btnDel.onclick = () => {
            if (confirm(`Hapus akun user ${get('name')}?`)) {
                window.location.href = `delete_process.php?id=${get('id')}`;
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
        <div class="space-y-1 pb-8">
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

            <div class="modal-action mt-8">
                <button type="button" class="button-secondary" onclick="modal_add_user.close()">Batal</button>
                <button type="submit" class="button-primary">Buat User</button>
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

<dialog id="modal_view_user" class="modal">
    <div class="modal-box w-11/12 max-w-md bg-white p-8">
        <div class="flex flex-col items-center gap-4 mb-8">
            <div class="p-4 rounded-full bg-zinc-50 border border-zinc-200 flex justify-center items-center">
                <span class="icon-user h-12 w-12 text-zinc-600"></span>
            </div>
            <div class="text-center">
                <h3 class="text-xl font-bold text-zinc-900" id="v-name"></h3>
                <p class="text-zinc-500 capitalize" id="v-role"></p>
            </div>
        </div>

        <div class="modal-action grid grid-cols-2 gap-4">
            <button type="button" id="btn-delete-user" class="btn bg-red-50 text-red-600 border-red-200 hover:bg-red-600 hover:text-white transition-all rounded-lg">
                Hapus User
            </button>
            <button type="button" class="button-secondary" onclick="modal_view_user.close()">
                Kembali
            </button>
        </div>
    </div>
</dialog>

<dialog id="modal_edit_user" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8 text-left">
        <div class="space-y-1 pb-8">
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
            <div class="modal-action mt-8">
                <button type="button" class="button-secondary" onclick="modal_edit_user.close()">Batal</button>
                <button type="submit" class="button-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>