<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];


$totalSiswa = dbCount($pdo, 'siswa');
$totalPelanggaran = dbCount($pdo, 'pelanggaran');


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
        ORDER BY p.tanggal_pelaporan DESC";

$stmt = $pdo->query($sql);



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
                    <div class="rounded-lg border border-zinc-300 p-8">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="text-zinc-800 font-paragraph-16 font-medium border-b border-zinc-200">
                                    <th class="px-2 py-3">No</th>
                                    <th class="px-2 py-3">Nama Siswa</th>
                                    <th class="px-2 py-3">Kelas</th>
                                    <th class="px-2 py-3">Pelanggaran</th>
                                    <th class="px-2 py-3 text-center">Poin</th>
                                    <th class="px-2 py-3">Pelapor</th>
                                    <th class="px-2 py-3">Waktu Pelaporan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                <?php
                                $no = 1;
                                while ($row = $stmt->fetch()):
                                    // Formatting tanggal biar enak dibaca
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


                                        <td class=" py-4 px-2 text-zinc-600"><?= $no++; ?></td>
                                        <td class="py-4 px-2">
                                            <div class="font-medium text-zinc-900"><?= htmlspecialchars($row['nama_siswa']); ?></div>
                                        </td>
                                        <td class="py-4 px-2 text-zinc-600"><?= htmlspecialchars($row['kelas']); ?></td>
                                        <td class="py-4 px-2">
                                            <div class="text-zinc-800"><?= htmlspecialchars($row['nama_jenis']); ?></div>
                                            <p class="text-[11px] text-zinc-400 italic"><?= htmlspecialchars($row['keterangan']); ?></p>
                                        </td>
                                        <td class="py-4 px-2 text-center">
                                            <span class="text-red-600 font-bold">-<?= $row['bobot_poin']; ?></span>
                                        </td>
                                        <td class="py-4 px-2">
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
                    </div>
                </div>
            </main>
        </div>

</body>

</html>

<!-- modal view pelanggaran -->
<dialog id="modal_view_pelanggaran" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white p-8">
        <div class="flex flex-col gap-2 mb-8">
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
                <span class="icon-arrow-left w-5 h-5"></span> Back
            </button>
        </div>
    </div>
</dialog>

<!-- Modal add pelanggaran -->
<dialog id="modal_add_pelanggaran" class="modal">
    <div class="modal-box max-w-lg bg-white p-10 rounded-3xl border border-zinc-100">
        <div class="flex flex-col gap-2 mb-8">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <h2 class="text-xl font-bold text-zinc-900">Buat Pelanggaran</h2>
            <p class="text-sm text-zinc-500 font-medium">Sistem Pelanggaran Siswa</p>
        </div>

        <form method="POST" action="add_process.php">
            <div class="space-y-4">
                <div class="form-control">
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
                    <label class="label font-bold text-zinc-700">Kelas</label>
                    <select id="select-kelas" name="kelas" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" disabled required>
                        <option value="">Pilih Jurusan Dulu</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label font-bold text-zinc-700">Siswa</label>
                    <select id="select-siswa-final" name="id_siswa" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" disabled required>
                        <option value="">Pilih Kelas Dulu</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label font-bold text-zinc-700">Jenis Pelanggaran</label>
                    <select name="id_jenis" class="select select-bordered w-full rounded-xl bg-zinc-50 border-zinc-200" required>
                        <option value="" disabled selected>Pilih Pelanggaran</option>
                        <?php
                        $jenisStmt = $pdo->query("SELECT id_jenis, nama_jenis, point FROM jenis_pelanggaran ORDER BY nama_jenis ASC");
                        while ($jp = $jenisStmt->fetch()) echo "<option value='{$jp['id_jenis']}'>{$jp['nama_jenis']} (-{$jp['point']} Point)</option>";
                        ?>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label font-bold text-zinc-700">Keterangan</label>
                    <textarea name="keterangan" class="textarea textarea-bordered rounded-xl bg-zinc-50 border-zinc-200 h-24" placeholder="Masukkan detail kejadian..."></textarea>
                </div>
            </div>

            <div class="modal-action flex justify-end gap-3 mt-10">
                <button type="button" class="btn bg-white border-zinc-200 text-zinc-600 hover:bg-zinc-100 rounded-xl px-8 normal-case font-bold" onclick="modal_add_pelanggaran.close()">Cancel</button>
                <button type="submit" class="btn bg-zinc-900 border-none text-white hover:bg-black rounded-xl px-8 normal-case font-bold">
                    <span class="icon-check w-4 h-4 mr-1"></span> Simpan
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

        // 1. Saat Jurusan dipilih -> Cari Kelas yang ada di Jurusan itu
        selJurusan.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            console.log("Fetching kelas for: " + this.value);

            try {
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);
                const data = await res.json();

                selKelas.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
                data.forEach(item => {
                    selKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });

                selKelas.disabled = false; // Aktifin select kelas
                selSiswa.disabled = true; // Riset select siswa
                selSiswa.innerHTML = '<option value="">Pilih Kelas Dulu</option>';
            } catch (err) {
                console.error("Fetch Error (Kelas):", err);
            }
        });

        // 2. Saat Kelas dipilih -> Cari Nama Siswa yang ada di Kelas itu
        selKelas.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            console.log("Fetching siswa for: " + this.value);

            try {
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                selSiswa.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
                data.forEach(item => {
                    selSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });

                selSiswa.disabled = false; // Aktifin select siswa
            } catch (err) {
                console.error("Fetch Error (Siswa):", err);
            }
        });
    });
</script>