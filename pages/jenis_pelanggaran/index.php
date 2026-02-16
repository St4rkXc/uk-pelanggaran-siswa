<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="flex w-dvw">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class="flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-6">
                <div class="flex justify-between items-center">
                    <h5 class="font-paragraph-16 font-semibold text-zinc-800">Tabel data Jenis Pelanggaran</h5>
                    <div class="flex items-center gap-3">
                        <div class="button-primary" onclick="modal_add_jenis_penaggaran.showModal()">Add</div>
                    </div>
                </div>
                <div class="p-8 rounded-lg border border-zinc-300 mt-4">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr class=" text-zinc-800 font-paragraph-16 font-medium">
                                <th class="px-2 py-3">No</th>
                                <th class="px-2 py-3">Jenis Pelanggaran</th>
                                <th class="px-2 py-3">Deskripsi</th>
                                <th class="px-2 py-3">Pengurangan Poin</th>
                                <th class="px-2 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            <?php
                            $no = 1;
                            $stmt = $pdo->query("SELECT * FROM jenis_pelanggaran ORDER BY id_jenis DESC");
                            while ($row = $stmt->fetch()):
                            ?>
                                <tr class="border-b border-b-zinc-300 hover:bg-zinc-50 transition-all"
                                    data-id="<?= $row['id_jenis']; ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_jenis']); ?>"
                                    data-deskripsi="<?= htmlspecialchars($row['deskripsi']); ?>"
                                    data-point="<?= $row['point']; ?>">

                                    <td class="py-3 px-2 text-zinc-600"><?= $no++; ?></td>
                                    <td class="py-3 px-2 text-zinc-800 font-medium"><?= htmlspecialchars($row['nama_jenis']); ?></td>
                                    <td class="py-3 px-2 text-zinc-600"><?= htmlspecialchars($row['deskripsi']); ?></td>
                                    <td class="py-3 px-2 text-zinc-600 font-bold"><?= htmlspecialchars($row['point']); ?> Poin</td>
                                    <td class="py-3 px-2">
                                        <div class="flex gap-2">
                                            <button type="button" class="button-secondary px-3 py-1 flex items-center gap-2" onclick="openEditJenisModal(this.closest('tr'))">
                                                <span class="icon-edit w-4 h-4 text-zinc-800"></span>
                                                <p class="">Edit</p>
                                            </button>
                                            <button type="button" class="button-danger px-3 py-1 flex items-center gap-2" onclick="deleteJenisPelanggaran(<?= $row['id_jenis']; ?>, '<?= htmlspecialchars($row['nama_jenis']); ?>')">
                                                <span class="icon-delete w-4 h-4 "></span>
                                                <p class="">Delete</p>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>

</body>

</html>

<script>
    function openEditJenisModal(el) {
        const get = (attr) => el.getAttribute('data-' + attr);

        document.getElementById('edit-id-jenis').value = get('id');
        document.getElementById('edit-nama-jenis').value = get('nama');
        document.getElementById('edit-deskripsi-jenis').value = get('deskripsi');
        document.getElementById('edit-point-jenis').value = get('point');

        modal_edit_jenis_pelanggaran.showModal();
    }

    function deleteJenisPelanggaran(id, nama) {
        if (confirm(`Hapus jenis pelanggaran "${nama}"? Data ini mungkin terhubung dengan riwayat pelanggaran siswa.`)) {
            window.location.href = `delete_process.php?id=${id}`;
        }
    }
</script>

<dialog id="modal_add_jenis_penaggaran" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="space-y-1 pb-8">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit bg-zinc-50">
                <img src="<?= $imgPath; ?>" alt="Logo" class="h-13 w-12.5">
            </div>
            <div>
                <h5 class="font-heading-5 text-zinc-900 font-bold">Tambah Jenis Pelanggaran</h5>
                <p class="font-paragraph-15 font-medium text-zinc-500">Master data bobot poin pelanggaran.</p>
            </div>
        </div>

        <form method="POST" action="add_process.php">
            <div class="space-y-4 text-left">
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Nama Pelanggaran</label>
                    <input type="text" name="nama_jenis" placeholder="Contoh: Terlambat, Atribut..." class="my-input w-full" required />
                </div>

                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Deskripsi</label>
                    <textarea name="deskripsi" placeholder="Penjelasan singkat pelanggaran..." class="my-input w-full h-24 pt-3" required></textarea>
                </div>

                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Bobot Poin</label>
                    <input type="number" name="point" placeholder="Contoh: 10, 25, 50..." class="my-input w-full" required />
                    <p class="text-[10px] text-zinc-400 italic">*Poin ini akan otomatis mengurangi poin total siswa.</p>
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="button-secondary" onclick="modal_add_jenis_penaggaran.close()">Batal</button>
                <button type="submit" class="button-primary">Simpan Master Data</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_edit_jenis_pelanggaran" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="space-y-1 pb-8 text-left">
            <h5 class="font-heading-5 text-zinc-900 font-bold">Ubah Jenis Pelanggaran</h5>
            <p class="font-paragraph-15 font-medium text-zinc-500">Perbarui master data bobot poin.</p>
        </div>

        <form method="POST" action="edit_process.php">
            <input type="hidden" name="id_jenis" id="edit-id-jenis">
            <div class="space-y-4 text-left">
                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Nama Pelanggaran</label>
                    <input type="text" name="nama_jenis" id="edit-nama-jenis" class="my-input w-full" required />
                </div>

                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Deskripsi</label>
                    <textarea name="deskripsi" id="edit-deskripsi-jenis" class="my-input w-full h-24 pt-3" required></textarea>
                </div>

                <div class="space-y-2">
                    <label class="label font-semibold text-zinc-600">Bobot Poin</label>
                    <input type="number" name="point" id="edit-point-jenis" class="my-input w-full font-bold" required />
                </div>
            </div>

            <div class="modal-action mt-8">
                <button type="button" class="button-secondary" onclick="modal_edit_jenis_pelanggaran.close()">Batal</button>
                <button type="submit" class="button-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>