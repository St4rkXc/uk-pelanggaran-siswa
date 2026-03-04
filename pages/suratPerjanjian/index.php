<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

$totalSuratPerjanjian = dbCount($pdo, 'surat_perjanjian');


$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];

$query = "SELECT 
    sp.id_perjanjian, 
    sp.id_siswa,           -- Tambahin ini buat parameter edit
    sp.id_pelanggaran,     -- Tambahin ini (Penyebab Error 1)
    sp.tanggal_perjanjian, -- Tambahin ini (Penyebab Error 2)
    sw.nama_siswa, 
    sw.nama_ortu, 
    sw.kelas, 
    jp.nama_jenis AS jenis_pelanggaran, 
    p.keterangan, 
    sp.isi_perjanjian,
    sp.tanggal_surat
FROM surat_perjanjian sp
JOIN siswa sw ON sp.id_siswa = sw.id_siswa
JOIN pelanggaran p ON sp.id_pelanggaran = p.id_pelanggaran
JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
ORDER BY sp.id_perjanjian DESC;";

$stmt = $pdo->query($query);
$suratList = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
                            <h5 class="font-heading-5 font-semibold text-zinc-800"><?= htmlspecialchars($totalSuratPerjanjian) ?> Surat</h5>
                            <p class="font-paragraph-14 font font-medium text-zinc-600">Total Surat Perjanjian</p>
                        </div>
                    </div>

                </div>
                <div class="mt-6 space-y-4">
                    <!-- filter & information goes here -->
                    <div class="flex justify-between items-center">
                        <p class="font-heading-6 font-semibold text-zinc-800"> Tabel Surat Perjanjian</p>
                        <div class="flex items-center">
                            <button class="button-primary" onclick="modal_add_surat_perjanjian.showModal()">Add</button>
                        </div>
                    </div>
                    <!-- table goes here -->
                    <div class="mt-6 space-y-2">
                        <?php if (empty($suratList)): ?>
                            <div class="p-10 text-center border-2 border-dashed border-zinc-200 rounded-xl text-zinc-400">
                                Belum ada data surat perjanjian.
                            </div>
                        <?php else: ?>
                            <?php foreach ($suratList as $item):
                                // Logic ambil inisial nama buat gantiin No. Surat
                                $words = explode(" ", $item['nama_siswa']);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ""));
                            ?>
                                <div class="group flex items-center justify-between px-5 py-6 bg-white border border-zinc-200 rounded-xl hover:border-blue-500 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-6">
                                        <div class="flex items-center justify-center h-16 w-16 bg-zinc-50 rounded-lg border border-zinc-100 group-hover:bg-blue-50 group-hover:border-blue-100 transition-colors">
                                            <span class="text-xl font-bold text-zinc-400 group-hover:text-blue-500"><?= $initials ?></span>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-zinc-900 text-lg leading-tight"><?= htmlspecialchars($item['nama_siswa']) ?></h4>
                                            <p class="text-sm text-zinc-500 font-medium mt-1">
                                                <?= $item['kelas'] ?> •
                                                <span class="text-zinc-400">Tgl:</span>
                                                <span class="text-zinc-700"><?= date('d M Y', strtotime($item['tanggal_surat'])) ?></span>
                                            </p>
                                        </div>

                                        <div class="h-10 w-px bg-zinc-200"></div>

                                        <div class="max-w-md">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-red-600">
                                                    <?= $item['jenis_pelanggaran'] ?>
                                                </span>
                                            </div>
                                            <p class="text-sm text-zinc-600 line-clamp-1 italic">"<?= htmlspecialchars($item['isi_perjanjian']) ?>"</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditSurat('<?= $item['id_perjanjian'] ?>', '<?= $item['id_siswa'] ?>', '<?= addslashes($item['nama_siswa']) ?>', '<?= $item['id_pelanggaran'] ?>', '<?= $item['tanggal_perjanjian'] ?>', '<?= addslashes($item['isi_perjanjian']) ?>')"
                                            class="button-secondary p-3">
                                            <span class="icon-edit w-5 h-5"></span>
                                        </button>

                                        <button class="button-danger p-3" onclick="if(confirm('Yakin mau hapus surat perjanjian <?= addslashes($item['nama_siswa']) ?>?')) { window.location.href='delete_process.php?id=<?= $item['id_perjanjian'] ?>'; }">
                                            <span class=" icon-delete w-5 h-5"></span>
                                        </button>

                                        <div class="w-px h-8 bg-zinc-100 mx-1"></div>

                                        <a href="print_surat.php?id=<?= $item['id_perjanjian'] ?>" class="button-primary flex items-center gap-2 px-5 py-2.5 shadow-lg shadow-zinc-100" target="_blank">
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

<dialog id="modal_add_surat_perjanjian" class="modal">
    <div class="modal-box max-w-6xl bg-white p-10 rounded-3xl border border-zinc-100 shadow-2xl">
        <div class="flex flex-col gap-2 mb-8 border-b border-zinc-100 pb-6">
            <div class="p-2 border border-zinc-200 rounded-xl w-fit">
                <img src="<?= $imgPath; ?>" class="h-12 w-12 object-contain">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Buat Surat Perjanjian</h2>
                <p class="text-sm text-zinc-500 font-medium">Generate dokumen pernyataan siswa secara otomatis</p>
            </div>
        </div>

        <form method="POST" action="add_process.php" class="space-y-6">
            <div class="flex gap-4">
                <div class="bg-zinc-50 p-6 rounded-2xl border border-zinc-100 space-y-4 h-fit flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Informasi Siswa</p>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Jurusan</label>
                            <select id="adj-jurusan" name="jurusan" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" required>
                                <option value="" disabled selected>Pilih Jurusan</option>
                                <?php
                                $jStmt = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC");
                                while ($j = $jStmt->fetch()) echo "<option value='{$j['jurusan']}'>{$j['jurusan']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label font-bold text-zinc-700 text-sm">Kelas</label>
                            <select id="adj-kelas" name="kelas" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" disabled required>
                                <option value="">Pilih Jurusan Terlebih Dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label font-bold text-zinc-700 text-sm">Nama Siswa</label>
                        <select id="adj-siswa" name="id_siswa" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500 transition-all" disabled required>
                            <option value="">Pilih Kelas Terlebih Dahulu</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-4 flex-2">
                    <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Detail Perjanjian</p>

                    <div class="form-control space-y-2">
                        <label class="label font-bold text-zinc-700 text-sm">Dasar Pelanggaran</label>
                        <select id="adj-pelanggaran" name="id_pelanggaran" class="select select-bordered w-full rounded-xl border-zinc-200" disabled required>
                            <option value="">Pilih Siswa Dulu</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control space-y-2">
                            <label class="label font-bold text-zinc-700 text-sm">Tanggal Surat (Hari Ini)</label>
                            <input type="text" value="<?= date('d/m/Y'); ?>" class="my-input" readonly>
                            <input type="hidden" name="tanggal_surat" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="form-control space-y-2">
                            <label class="label font-bold text-zinc-700 text-sm">Tanggal Perjanjian</label>
                            <input type="date" name="tanggal_perjanjian" class="my-input" required>
                        </div>
                    </div>

                    <div class="form-control flex flex-col space-y-2">
                        <label class="label font-bold text-zinc-700 text-sm">Isi Perjanjian / Pernyataan</label>
                        <textarea name="isi_perjanjian" class="textarea textarea-bordered rounded-xl border-zinc-200 h-32 w-full focus:ring-2 focus:ring-blue-500 leading-relaxed" placeholder="Contoh: Siswa berjanji tidak akan mengulangi perbuatannya dan bersedia menerima sanksi yang lebih berat jika melanggar lagi." required></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-zinc-100">
                <button type="button" class="button-secondary" onclick="modal_add_surat_perjanjian.close()">Cancel</button>
                <button type="submit" class="button-primary flex items-center gap-2">
                    <span class="icon-check w-5 h-5"></span>
                    Simpan & Generate
                </button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="modal_edit_surat_perjanjian" class="modal">
    <div class="modal-box max-w-2xl bg-white p-10 rounded-3xl border border-zinc-100">
        <h2 class="text-2xl font-bold text-zinc-900 mb-6 tracking-tight">Edit Detail Perjanjian</h2>

        <form method="POST" action="edit_process.php" class="space-y-6">
            <input type="hidden" name="id_perjanjian" id="edit-id-perjanjian">

            <div class="form-control">
                <label class="label font-bold text-zinc-700 text-sm">Siswa Terkait</label>
                <input type="text" id="edit-nama-siswa-display" class="my-input bg-zinc-100 text-zinc-500 cursor-not-allowed" readonly>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="form-control">
                    <label class="label font-bold text-zinc-700 text-sm">Dasar Pelanggaran</label>
                    <select name="id_pelanggaran" id="edit-id-pelanggaran" class="select select-bordered w-full rounded-xl border-zinc-200 focus:ring-2 focus:ring-blue-500" required>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label font-bold text-zinc-700 text-sm">Tanggal Perjanjian Baru</label>
                    <input type="date" name="tanggal_perjanjian" id="edit-tgl-perjanjian" class="my-input w-full" required>
                </div>

                <div class="form-control">
                    <label class="label font-bold text-zinc-700 text-sm">Isi Perjanjian Baru</label>
                    <textarea name="isi_perjanjian" id="edit-isi-perjanjian" class="textarea textarea-bordered rounded-xl border-zinc-200 h-32 w-full focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="button" class="button-secondary" onclick="modal_edit_surat_perjanjian.close()">Cancel</button>
                <button type="submit" class="button-primary px-10">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    function openEditSurat(id, id_siswa, id_pelanggaran, tgl_perjanjian, isi) {
        // Mapping ke modal edit (Pastiin modal-box lo punya ID-ID ini)
        const modal = document.getElementById('modal_edit_surat_perjanjian');
        if (!modal) return console.error('Modal edit not found!');

        document.getElementById('edit-id-perjanjian').value = id;
        document.getElementById('edit-id-siswa').value = id_siswa;
        document.getElementById('edit-id-pelanggaran').value = id_pelanggaran;
        document.getElementById('edit-tgl-perjanjian').value = tgl_perjanjian;
        document.getElementById('edit-isi-perjanjian').value = isi;

        modal.showModal();
    }

    async function openEditSurat(id, id_siswa, nama_siswa, id_pelanggaran, tgl_perjanjian, isi) {
        // 1. Isi data dasar
        document.getElementById('edit-id-perjanjian').value = id;
        document.getElementById('edit-nama-siswa-display').value = nama_siswa;
        document.getElementById('edit-tgl-perjanjian').value = tgl_perjanjian;
        document.getElementById('edit-isi-perjanjian').value = isi;

        // 2. Fetch Pelanggaran khusus siswa ini
        const selPelanggaran = document.getElementById('edit-id-pelanggaran');
        selPelanggaran.innerHTML = '<option>Loading...</option>';

        try {
            const res = await fetch(`get_filter_data.php?type=pelanggaran&id_siswa=${id_siswa}`);
            const data = await res.json();

            selPelanggaran.innerHTML = '';
            data.forEach(item => {
                const selected = (item.id_pelanggaran == id_pelanggaran) ? 'selected' : '';
                const tgl = new Date(item.tanggal_pelaporan).toLocaleDateString('id-ID');
                selPelanggaran.innerHTML += `<option value="${item.id_pelanggaran}" ${selected}>${item.nama_jenis} (${tgl})</option>`;
            });
        } catch (err) {
            console.error("Gagal load pelanggaran edit:", err);
        }

        modal_edit_surat_perjanjian.showModal();
    }



    document.addEventListener('DOMContentLoaded', function() {
        // ID buat Modal Surat Perjanjian
        const selJurusanAdj = document.getElementById('adj-jurusan');
        const selKelasAdj = document.getElementById('adj-kelas');
        const selSiswaAdj = document.getElementById('adj-siswa');

        // Filter Kelas berdasarkan Jurusan
        selJurusanAdj.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${val}`);
                const data = await res.json();

                selKelasAdj.innerHTML = '<option value="" disabled selected>Pilih Kelas</option>';
                data.forEach(item => {
                    selKelasAdj.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
                });
                selKelasAdj.disabled = false;
                selSiswaAdj.disabled = true;
                selSiswaAdj.innerHTML = '<option value="">Pilih Kelas Dulu</option>';
            } catch (err) {
                console.error(err);
            }
        });

        // Filter Siswa berdasarkan Kelas
        selKelasAdj.addEventListener('change', async function() {
            const val = encodeURIComponent(this.value);
            try {
                const res = await fetch(`get_filter_data.php?type=siswa&kelas=${val}`);
                const data = await res.json();

                selSiswaAdj.innerHTML = '<option value="" disabled selected>Pilih Siswa</option>';
                data.forEach(item => {
                    selSiswaAdj.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
                });
                selSiswaAdj.disabled = false;
            } catch (err) {
                console.error(err);
            }
        });
        const selPelanggaranAdj = document.getElementById('adj-pelanggaran');

        selSiswaAdj.addEventListener('change', async function() {
            const idSiswa = encodeURIComponent(this.value);

            try {
                const res = await fetch(`get_filter_data.php?type=pelanggaran&id_siswa=${idSiswa}`);
                const data = await res.json();

                if (data.length > 0) {
                    selPelanggaranAdj.innerHTML = '<option value="" disabled selected>Pilih Pelanggaran Terkait</option>';
                    data.forEach(item => {
                        // nampilin tanggal pelaporan biar lebih informatif, soalnya satu siswa bisa punya banyak pelanggaran
                        const tgl = new Date(item.tanggal_pelaporan).toLocaleDateString('id-ID');
                        selPelanggaranAdj.innerHTML += `<option value="${item.id_pelanggaran}">${item.nama_jenis} (${tgl})</option>`;
                    });
                    selPelanggaranAdj.disabled = false;
                } else {
                    selPelanggaranAdj.innerHTML = '<option value="">Siswa ini belum memiliki catatan pelanggaran</option>';
                    selPelanggaranAdj.disabled = true;
                }
            } catch (err) {
                console.error("Fetch Error (Pelanggaran):", err);
            }
        });


    });
</script>