<?php
session_start();
$requiredRole = ['guru_bk', 'admin'];

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
    <title>Laporan Poin Siswa | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white !important;
            }

            #print-section {
                font-family: 'Times New Roman', serif;
            }

            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body class="flex bg-zinc-50 border-0 m-0">
    <div class="flex w-full flex-row">
        <aside class="sidebar print:hidden shrink-0 border-r border-zinc-200">
            <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        </aside>
        <div class="flex-1 overflow-x-hidden w-full bg-zinc-50">
            <div class="print:hidden">
                <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            </div>
            <main class="p-8 print:hidden flex-1 overflow-y-auto w-full">
                <!-- Flex container for Layout: Form Left (w-1/4), Output Right (w-3/4) -->
                <div class="grid grid-cols-6 gap-6 w-full items-start">
                    <!-- Left: Input Form -->
                    <div class=" col-span-2 space-y-4 p-5 bg-white rounded-xl border border-zinc-200 shadow-sm sticky top-8">
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
                                <option value="">Pilih Jurusan Terlebih Dahulu</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="label font-bold text-zinc-500 text-[10px] uppercase tracking-wider">Siswa</label>
                            <select id="filter-siswa" class="my-select w-full" disabled>
                                <option value="">Pilih Kelas Terlebih Dahulu</option>
                            </select>
                        </div>

                        <button onclick="window.print()" class="w-full bg-zinc-900 text-white py-2 rounded-lg text-sm font-medium hover:bg-zinc-800 transition-all mt-4 flex items-center justify-center gap-2">
                            <span class="icon-printer"></span> Cetak Laporan
                        </button>
                    </div>

                    <!-- Right: Output Area -->
                    <div id="display-area" class="col col-span-4 space-y-6">
                        <div id="empty-state" class="flex flex-col items-center justify-center h-64 bg-zinc-50 rounded-xl border-2 border-dashed border-zinc-200">
                            <p class="text-zinc-400 text-sm">Silahkan pilih siswa untuk melihat laporan lengkap.</p>
                        </div>

                        <div id="report-content" class="hidden space-y-6">
                            <!-- Tabs Navigation -->
                            <div class="border-b border-zinc-200">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <button class="tab-btn border-orange-500 text-orange-600 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-all">All</button>
                                    <button class="tab-btn border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-pelanggaran">Pelanggaran</button>
                                    <button class="tab-btn border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-perjanjian">Surat Perjanjian</button>
                                    <button class="tab-btn border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-pindah">Surat Pindah</button>
                                    <button class="tab-btn border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-panggilan">Surat Panggilan</button>
                                    <button class="tab-btn border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium uppercase transition-colors" data-target="tab-pernyataan-ortu">Pernyataan Ortu</button>
                                </nav>
                            </div>

                            <!-- Tabs Content -->
                            <!-- Tab: All -->
                            <div id="tab-all" class="tab-pane space-y-6 block">
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

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                                    <div class="bg-white p-4 rounded-xl border border-zinc-200">
                                        <h4 class="font-bold text-emerald-600 text-[10px] uppercase mb-3">Pernyataan Ortu</h4>
                                        <div id="list-pernyataan-ortu" class="space-y-3 text-xs"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Pelanggaran -->
                            <div id="tab-pelanggaran" class="tab-pane hidden space-y-6">
                                <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                                    <div class="p-4 border-b border-zinc-100 bg-zinc-50/50">
                                        <h4 class="font-bold text-zinc-800 text-xs uppercase">Semua Riwayat Pelanggaran</h4>
                                    </div>
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-zinc-50 text-zinc-500 text-[10px] uppercase">
                                            <tr>
                                                <th class="p-4">Tanggal</th>
                                                <th class="p-4">Jenis Pelanggaran</th>
                                                <th class="p-4">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-pelanggaran-tab"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab: Surat Perjanjian -->
                            <div id="tab-perjanjian" class="tab-pane hidden space-y-6">
                                <div class="bg-white p-6 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-blue-600 text-sm uppercase mb-4 border-b pb-2">Surat Perjanjian</h4>
                                    <div id="list-perjanjian-tab" class="space-y-4"></div>
                                </div>
                            </div>

                            <!-- Tab: Surat Pindah -->
                            <div id="tab-pindah" class="tab-pane hidden space-y-6">
                                <div class="bg-white p-6 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-red-600 text-sm uppercase mb-4 border-b pb-2">Surat Pindah</h4>
                                    <div id="list-pindah-tab" class="space-y-4"></div>
                                </div>
                            </div>

                            <!-- Tab: Surat Panggilan -->
                            <div id="tab-panggilan" class="tab-pane hidden space-y-6">
                                <div class="bg-white p-6 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-orange-600 text-sm uppercase mb-4 border-b pb-2">Panggilan Orang Tua</h4>
                                    <div id="list-panggilan-tab" class="space-y-4"></div>
                                </div>
                            </div>
                            
                            <!-- Tab: Surat Pernyataan Ortu -->
                            <div id="tab-pernyataan-ortu" class="tab-pane hidden space-y-6">
                                <div class="bg-white p-6 rounded-xl border border-zinc-200">
                                    <h4 class="font-bold text-emerald-600 text-sm uppercase mb-4 border-b pb-2">Pernyataan Orang Tua</h4>
                                    <div id="list-pernyataan-ortu-tab" class="space-y-4"></div>
                                </div>
                            </div>
                        </div> <!-- end report content -->
                    </div> <!-- end right area -->
                </div>
            </main>

            <!-- PRINT SECTION FOR DETAIL SISWA -->
            <div id="print-section" class="hidden print:block w-full bg-white max-w-[21cm] min-h-[29.7cm] mx-auto p-12">
                <img src="<?= $kopPath ?>" alt="Kop Surat Sekolah" class="w-full h-auto object-contain mb-6">

                <div class="text-center mb-8">
                    <h2 class="text-xl font-bold uppercase underline tracking-tight text-zinc-950">
                        Laporan Detail Siswa
                    </h2>
                    <p class="text-md mt-2 font-bold" id="print-siswa-name">-</p>
                </div>

                <div class="mb-2 text-md font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 uppercase">Riwayat Pelanggaran</div>
                <table class="w-full text-left border-collapse border border-zinc-900 mb-8 mt-2">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border border-zinc-900 font-bold text-zinc-900 uppercase text-xs">Tanggal</th>
                            <th class="px-4 py-2 border border-zinc-900 font-bold text-zinc-900 uppercase text-xs">Jenis Pelanggaran</th>
                            <th class="px-4 py-2 border border-zinc-900 font-bold text-zinc-900 uppercase text-xs">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="print-pelanggaran"></tbody>
                </table>

                <div class="mb-2 text-md font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 uppercase mt-8">Data Surat Perjanjian</div>
                <div id="print-perjanjian" class="mb-8 mt-2"></div>

                <div class="mb-2 text-md font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 uppercase mt-8">Data Panggilan Orang Tua</div>
                <div id="print-panggilan" class="mb-8 mt-2"></div>

                <div class="mb-2 text-md font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 uppercase mt-8">Data Surat Pindah</div>
                <div id="print-pindah" class="mb-8 mt-2"></div>
                
                <div class="mb-2 text-md font-bold text-zinc-900 border-b-2 border-zinc-900 pb-1 uppercase mt-8">Data Surat Pernyataan Orang Tua</div>
                <div id="print-pernyataan-ortu" class="mb-8 mt-2"></div>
            </div>
            <!-- END PRINT SECTION -->
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

        // Tabs Logic
        const tabsBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabsBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Reset styling
                tabsBtns.forEach(b => {
                    b.classList.remove('border-orange-500', 'text-orange-600');
                    b.classList.add('border-transparent', 'text-zinc-500');
                });

                // Add active styling
                btn.classList.add('border-orange-500', 'text-orange-600');
                btn.classList.remove('border-transparent', 'text-zinc-500');

                // Hide all panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('block');
                    pane.classList.add('hidden');
                });

                // Show target pane
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.remove('hidden');
                document.getElementById(targetId).classList.add('block');
            });
        });

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
            selSiswa.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
        });

        // 2. Kelas -> Siswa
        selKelas.addEventListener('change', async () => {
            if (!selKelas.value) return;
            const res = await fetch(`get_filter_data.php?type=siswa&kelas=${encodeURIComponent(selKelas.value)}`);
            const data = await res.json();

            selSiswa.innerHTML = '<option value="">Pilih Siswa</option>';
            data.forEach(item => {
                // Add data-name for print layout
                selSiswa.innerHTML += `<option value="${item.id_siswa}" data-name="${item.nama_siswa}">${item.nama_siswa}</option>`;
            });
            selSiswa.disabled = false;
        });

        // 3. Siswa -> Load All Data
        selSiswa.addEventListener('change', async () => {
            const idSiswa = selSiswa.value;
            if (!idSiswa) return;

            const selectedOption = selSiswa.options[selSiswa.selectedIndex];
            const siswaName = selectedOption.getAttribute('data-name');
            document.getElementById('print-siswa-name').innerText = "Nama Siswa: " + (siswaName ? siswaName : idSiswa);

            // UI Feedback
            emptyState.classList.add('hidden');
            reportContent.classList.remove('hidden');

            try {
                // Tarik SEMUA data (pelanggaran + 3 jenis surat)
                const res = await fetch(`get_filter_data.php?type=full_report&id_siswa=${idSiswa}`);
                const data = await res.json();

                // RENDER PELANGGARAN
                let htmlP = '';
                let printHtmlP = '';
                data.pelanggaran.forEach(p => {
                    htmlP += `
                <tr class="border-t border-zinc-100">
                    <td class="p-4 text-zinc-500">${p.tanggal_pelaporan}</td>
                    <td class="p-4 font-bold text-zinc-800">${p.nama_jenis}</td>
                    <td class="p-4 text-zinc-500">${p.keterangan || '-'}</td>
                </tr>`;
                    printHtmlP += `
                <tr>
                    <td class="px-4 py-2 border border-zinc-900 text-sm text-zinc-900">${p.tanggal_pelaporan}</td>
                    <td class="px-4 py-2 border border-zinc-900 font-bold text-sm text-zinc-900">${p.nama_jenis}</td>
                    <td class="px-4 py-2 border border-zinc-900 text-sm text-zinc-900">${p.keterangan || '-'}</td>
                </tr>`;
                });

                const finalHtmlP = htmlP || '<tr><td colspan="3" class="p-4 text-center text-zinc-400 italic">Tidak ada riwayat pelanggaran</td></tr>';
                document.getElementById('table-pelanggaran').innerHTML = finalHtmlP;
                document.getElementById('table-pelanggaran-tab').innerHTML = finalHtmlP;
                document.getElementById('print-pelanggaran').innerHTML = printHtmlP || '<tr><td colspan="3" class="px-4 py-2 border border-zinc-900 text-center italic text-sm text-zinc-600">Tidak ada riwayat pelanggaran</td></tr>';

                // RENDER SURAT PERJANJIAN
                let htmlSPJ = '';
                let printHtmlSPJ = '';
                data.perjanjian.forEach(s => {
                    htmlSPJ += `
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 mb-2">
                    <p class="font-bold text-blue-800 text-[10px] uppercase">Tgl Surat: ${s.tanggal_surat}</p>
                    <p class="text-zinc-700 mt-1 leading-relaxed">${s.isi_perjanjian}</p>
                </div>`;
                    printHtmlSPJ += `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">Tanggal: ${s.tanggal_surat}</span><br>${s.isi_perjanjian}</div>`;
                });
                const finalHtmlSPJ = htmlSPJ || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';
                document.getElementById('list-perjanjian').innerHTML = finalHtmlSPJ;
                document.getElementById('list-perjanjian-tab').innerHTML = finalHtmlSPJ;
                document.getElementById('print-perjanjian').innerHTML = printHtmlSPJ || '<p class="text-sm italic text-zinc-600">Tidak ada data surat perjanjian.</p>';

                // RENDER PANGGILAN ORTU
                let htmlSPO = '';
                let htmlSPOTab = '';
                let printHtmlSPO = '';
                data.panggilan.forEach(s => {
                    const content = `
                <div class="p-3 bg-orange-50 rounded-lg border border-orange-100 mb-2">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-orange-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                        <p class="font-bold text-orange-800 text-[10px] uppercase">${s.tanggal_temu}</p>
                    </div>
                    <p class="text-zinc-700 mt-1 leading-relaxed"><span class="font-medium text-orange-700">Keperluan:</span> ${s.keperluan}</p>
                </div>`;
                    htmlSPO += content;
                    htmlSPOTab += content;
                    printHtmlSPO += `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">No: ${s.nomor_surat || '-'} | Tanggal Temu: ${s.tanggal_temu}</span><br>Keperluan: ${s.keperluan}</div>`;
                });
                const finalHtmlSPO = htmlSPO || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';
                document.getElementById('list-panggilan').innerHTML = finalHtmlSPO;
                document.getElementById('list-panggilan-tab').innerHTML = finalHtmlSPO;
                document.getElementById('print-panggilan').innerHTML = printHtmlSPO || '<p class="text-sm italic text-zinc-600">Tidak ada data surat panggilan.</p>';

                // RENDER SURAT PINDAH
                let htmlSPD = '';
                let htmlSPDTab = '';
                let printHtmlSPD = '';
                data.pindah.forEach(s => {
                    const content = `
                <div class="p-3 bg-red-50 rounded-lg border border-red-100 mb-2">
                    <p class="font-bold text-red-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                    <p class="text-zinc-800 font-medium mt-1">Ke: ${s.nama_sekolah}</p>
                    <p class="text-zinc-600 mt-1 text-[11px] italic">Alasan: ${s.alasan_pindah || '-'}</p>
                </div>`;
                    htmlSPD += content;
                    htmlSPDTab += content;
                    printHtmlSPD += `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">No: ${s.nomor_surat || '-'}</span> | Pindah ke: ${s.nama_sekolah}<br>Alasan: ${s.alasan_pindah || '-'}</div>`;
                });
                const finalHtmlSPD = htmlSPD || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';
                document.getElementById('list-pindah').innerHTML = finalHtmlSPD;
                document.getElementById('list-pindah-tab').innerHTML = finalHtmlSPD;
                document.getElementById('print-pindah').innerHTML = printHtmlSPD || '<p class="text-sm italic text-zinc-600">Tidak ada data surat pindah.</p>';
                
                // RENDER SURAT PERNYATAAN ORTU
                let htmlSPOrtu = '';
                let printHtmlSPOrtu = '';
                data.pernyataan_ortu.forEach(s => {
                    htmlSPOrtu += `
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 mb-2">
                    <p class="font-bold text-emerald-800 text-[10px] uppercase">Tgl Surat: ${s.tanggal_surat}</p>
                    <p class="text-zinc-700 mt-1 leading-relaxed"><span class="font-medium text-emerald-700">Tujuan:</span> Menyatakan sanggup membina siswa.</p>
                </div>`;
                    printHtmlSPOrtu += `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">Tanggal: ${s.tanggal_surat}</span><br>Menyatakan sanggup membina siswa.</div>`;
                });
                const finalHtmlSPOrtu = htmlSPOrtu || '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>';
                document.getElementById('list-pernyataan-ortu').innerHTML = finalHtmlSPOrtu;
                document.getElementById('list-pernyataan-ortu-tab').innerHTML = finalHtmlSPOrtu;
                document.getElementById('print-pernyataan-ortu').innerHTML = printHtmlSPOrtu || '<p class="text-sm italic text-zinc-600">Tidak ada data surat pernyataan orang tua.</p>';

            } catch (error) {
                console.error("Gagal narik data:", error);
                alert("Terjadi kesalahan saat mengambil data laporan.");
            }
        });
    });
</script>