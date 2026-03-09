<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
$kopPath = BASE_URL . '/src/public/assets/img/kop_surat.jpg';


$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

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

<body class="flex">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class="flex-1">
            <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            <main class="p-8">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-3 space-y-4 p-5 bg-white rounded-xl border border-zinc-200 shadow-sm h-fit">
                        <h3 class="font-bold text-zinc-800 text-sm uppercase mb-4">Filter Siswa</h3>

                        <div class="space-y-2">
                            <label class="label font-bold text-zinc-500 text-[10px] uppercase tracking-wider">Jurusan</label>
                            <select id="filter-jurusan" class="my-select w-full">
                                <option value="">Pilih Jurusan</option>
                                <?php
                                $stmt = $pdo->query("SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC");
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['jurusan']}'>{$row['jurusan']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="label font-bold text-zinc-500 text-[10px] uppercase tracking-wider">Kelas</label>
                            <select id="filter-kelas" class="my-select w-full" disabled>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="label font-bold text-zinc-500 text-[10px] uppercase tracking-wider">Siswa</label>
                            <select id="filter-siswa" class="my-select w-full" disabled>
                                <option value="">Pilih Siswa</option>
                            </select>
                        </div>

                        <button onclick="window.print()" class="w-full bg-zinc-900 text-white py-2 rounded-lg text-sm font-medium hover:bg-zinc-800 transition-all mt-4">
                            Print Laporan
                        </button>
                    </div>

                    <div id="display-area" class="col-span-9 space-y-6">
                        <div id="empty-state" class="flex flex-col items-center justify-center h-64 bg-zinc-50 rounded-xl border-2 border-dashed border-zinc-200">
                            <p class="text-zinc-400 text-sm">Silahkan pilih siswa untuk melihat laporan lengkap.</p>
                        </div>

                        <div id="report-content" class="hidden space-y-6">
                            <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                                <div class="p-4 border-b border-zinc-100 bg-zinc-50/50">
                                    <h4 class="font-bold text-zinc-800 text-xs uppercase">Riwayat Pelanggaran</h4>
                                </div>
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-zinc-50 text-zinc-500 text-[10px] uppercase">
                                        <tr>
                                            <th class="p-4">Tanggal</th>
                                            <th class="p-4">Jenis Pelanggaran</th>
                                            <th class="p-4">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-pelanggaran"></tbody>
                                </table>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-blue-600 text-[10px] uppercase mb-3">Surat Perjanjian</h4>
                                    <div id="list-perjanjian" class="space-y-3 text-xs"></div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-orange-600 text-[10px] uppercase mb-3">Panggilan Orang Tua</h4>
                                    <div id="list-panggilan" class="space-y-3 text-xs"></div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-red-600 text-[10px] uppercase mb-3">Surat Pindah</h4>
                                    <div id="list-pindah" class="space-y-3 text-xs"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selJurusan = document.getElementById('filter-jurusan');
        const selKelas = document.getElementById('filter-kelas');
        const selSiswa = document.getElementById('filter-siswa');
        const reportContent = document.getElementById('report-content');
        const emptyState = document.getElementById('empty-state');

        // 1. Jurusan -> Kelas
        selJurusan.addEventListener('change', async () => {
            if (!selJurusan.value) return;
            const res = await fetch(`get_filter_data.php?type=kelas&jurusan=${encodeURIComponent(selJurusan.value)}`);
            const data = await res.json();

            selKelas.innerHTML = '<option value="">Pilih Kelas</option>';
            data.forEach(item => {
                selKelas.innerHTML += `<option value="${item.kelas}">${item.kelas}</option>`;
            });
            selKelas.disabled = false;
            selSiswa.disabled = true;
            selSiswa.innerHTML = '<option value="">Pilih Siswa</option>';
        });

        // 2. Kelas -> Siswa
        selKelas.addEventListener('change', async () => {
            if (!selKelas.value) return;
            const res = await fetch(`get_filter_data.php?type=siswa&kelas=${encodeURIComponent(selKelas.value)}`);
            const data = await res.json();

            selSiswa.innerHTML = '<option value="">Pilih Siswa</option>';
            data.forEach(item => {
                selSiswa.innerHTML += `<option value="${item.id_siswa}">${item.nama_siswa}</option>`;
            });
            selSiswa.disabled = false;
        });

        // 3. Siswa -> Load All Data (One Stop Fetch)
        selSiswa.addEventListener('change', async () => {
            const idSiswa = selSiswa.value;
            if (!idSiswa) return;

            // UI Feedback
            emptyState.classList.add('hidden');
            reportContent.classList.remove('hidden');

            try {
                // Kita pake type=full_report karena ini yang narik SEMUA data (pelanggaran + 3 jenis surat)
                const res = await fetch(`get_filter_data.php?type=full_report&id_siswa=${idSiswa}`);
                const data = await res.json();

                // RENDER PELANGGARAN
                let htmlP = '';
                data.pelanggaran.forEach(p => {
                    htmlP += `
                <tr class="border-t border-zinc-100">
                    <td class="p-4 text-zinc-500">${p.tanggal_pelaporan}</td>
                    <td class="p-4 font-bold text-zinc-800">${p.nama_jenis}</td>
                    <td class="p-4 text-zinc-500">${p.keterangan || '-'}</td>
                </tr>`;
                });
                document.getElementById('table-pelanggaran').innerHTML = htmlP || '<tr><td colspan="3" class="p-4 text-center text-zinc-400 italic">Tidak ada riwayat pelanggaran</td></tr>';

                // RENDER SURAT PERJANJIAN
                let htmlSPJ = '';
                data.perjanjian.forEach(s => {
                    htmlSPJ += `
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="font-bold text-blue-800 text-[10px] uppercase">Tgl Surat: ${s.tanggal_surat}</p>
                    <p class="text-zinc-700 mt-1 leading-relaxed">${s.isi_perjanjian}</p>
                </div>`;
                });
                document.getElementById('list-perjanjian').innerHTML = htmlSPJ || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';

                // RENDER PANGGILAN ORTU
                let htmlSPO = '';
                data.panggilan.forEach(s => {
                    htmlSPO += `
                <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-orange-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                        <p class="font-bold text-orange-800 text-[10px] uppercase">${s.tanggal_temu}</p>
                    </div>
                    <p class="text-zinc-700 mt-1 leading-relaxed"><span class="font-medium text-orange-700">Keperluan:</span> ${s.keperluan}</p>
                </div>`;
                });
                document.getElementById('list-panggilan').innerHTML = htmlSPO || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';

                // RENDER SURAT PINDAH
                let htmlSPD = '';
                data.pindah.forEach(s => {
                    htmlSPD += `
                <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                    <p class="font-bold text-red-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                    <p class="text-zinc-800 font-medium mt-1">Ke: ${s.nama_sekolah}</p>
                    <p class="text-zinc-600 mt-1 text-[11px] italic">Alasan: ${s.alasan_pindah || '-'}</p>
                </div>`;
                });
                document.getElementById('list-pindah').innerHTML = htmlSPD || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';

            } catch (error) {
                console.error("Gagal narik data:", error);
                alert("Terjadi kesalahan saat mengambil data laporan.");
            }
        });
    });
</script>