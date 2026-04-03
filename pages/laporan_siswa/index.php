<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

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
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
    <div class="flex w-full flex-row">
        <aside class="sidebar print:hidden shrink-0 border-r border-zinc-200">
            <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        </aside>
        <div class="flex-1 overflow-x-hidden w-full bg-zinc-50">
            <div class="print:hidden">
                <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
            </div>
            <main class="p-8 print:hidden flex-1 overflow-y-auto w-full">
                <div class="mb-8 no-print">
                    <h1 class="text-2xl font-black text-zinc-950 uppercase tracking-tight">Sistem Laporan & Rekapitulasi</h1>
                    <p class="text-zinc-500 text-sm">Pilih siswa untuk melihat data.</p>
                </div>
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
                    <p class="text-md mt-2 font-bold" id="print-siswa-nis/nisn">-</p>
                    <p class="text-md mt-2 font-bold" id="print-siswa-kelas">-</p>
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
    document.addEventListener('DOMContentLoaded', () => {
        const selJurusan = document.getElementById('filter-jurusan');
        const selKelas = document.getElementById('filter-kelas');
        const selSiswa = document.getElementById('filter-siswa');
        const reportContent = document.getElementById('report-content');
        const emptyState = document.getElementById('empty-state');

        // [REFACTOR DOCS]: Logika Tab yang Disederhanakan
        // Menggunakan toggle() dengan kondisi boolean (isActive) untuk mempersingkat sintaks 
        // penambahan/penghapusan class. Menghapus kebutuhan loop bersarang yang repetitif.
        const tabsBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabsBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');

                tabsBtns.forEach(b => {
                    const isActive = b === btn;
                    b.classList.toggle('border-orange-500', isActive);
                    b.classList.toggle('text-orange-600', isActive);
                    b.classList.toggle('border-transparent', !isActive);
                    b.classList.toggle('text-zinc-500', !isActive);
                });

                tabPanes.forEach(pane => {
                    const isActive = pane.id === targetId;
                    pane.classList.toggle('block', isActive);
                    pane.classList.toggle('hidden', !isActive);
                });
            });
        });

        // [REFACTOR DOCS]: Helper Fungsi 'fetchData'
        // Fungsi pembungkus (wrapper) untuk fetch API agar kode tidak dipenuhi
        // oleh blok try...catch di setiap request. Mengembalikan data JSON secara langsung.
        const fetchData = async (url) => {
            try {
                const res = await fetch(url);
                return await res.json();
            } catch (error) {
                console.error(`Error fetching from ${url}:`, error);
                return null;
            }
        };

        // [REFACTOR DOCS]: Helper Fungsi 'renderList'
        // Menggantikan proses manual forEach() dan manipulasi string pada DOM. 
        // Fungsi ini menggunakan pola map().join() untuk merender array data ke HTML string
        // dengan performa lebih baik dan sangat mengurangi baris kode.
        // Mendukung multi-container (contoh: tab dan print sekaligus).
        const renderList = (data, containerIds, renderer, emptyMsg, printContainerId = null, printRenderer = null, printEmptyMsg = null) => {
            const hasData = data && data.length > 0;
            const html = hasData ? data.map(renderer).join('') : emptyMsg;
            containerIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = html;
            });

            if (printContainerId) {
                const printHtml = hasData && printRenderer ? data.map(printRenderer).join('') : (printEmptyMsg || emptyMsg);
                const printEl = document.getElementById(printContainerId);
                if (printEl) printEl.innerHTML = printHtml;
            }
        };

        // 1. Jurusan -> Kelas
        selJurusan.addEventListener('change', async () => {
            const jurusan = selJurusan.value;
            selKelas.innerHTML = '<option value="">Pilih Jurusan Terlebih Dahulu</option>';
            selSiswa.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
            selKelas.disabled = true;
            selSiswa.disabled = true;

            if (!jurusan) return;

            const data = await fetchData(`get_filter_data.php?type=kelas&jurusan=${encodeURIComponent(jurusan)}`);
            if (data) {
                selKelas.innerHTML = '<option value="">Pilih Kelas</option>' +
                    data.map(item => `<option value="${item.kelas}">${item.kelas}</option>`).join('');
                selKelas.disabled = false;
            }
        });

        // 2. Kelas -> Siswa
        selKelas.addEventListener('change', async () => {
            const kelas = selKelas.value;
            selSiswa.innerHTML = '<option value="">Pilih Kelas Terlebih Dahulu</option>';
            selSiswa.disabled = true;

            if (!kelas) return;

            const data = await fetchData(`get_filter_data.php?type=siswa&kelas=${encodeURIComponent(kelas)}`);
            if (data) {
                // [REFACTOR DOCS]: Injeksi Data ke DOM attributes
                // Menyimpan data tambahan (nis, nisn, jurusan, kelas) dari backend ke dalam fungsi data-*
                // agar ketika siswa dipilih, kita bisa langsung mengambil datanya untuk laporan cetak.
                selSiswa.innerHTML = '<option value="">Pilih Siswa</option>' +
                    data.map(item => `<option value="${item.id_siswa}" data-name="${item.nama_siswa}" data-nis="${item.nis || '-'}" data-nisn="${item.nisn || '-'}" data-jurusan="${item.jurusan || '-'}" data-kelas="${item.kelas || '-'}">${item.nama_siswa}</option>`).join('');
                selSiswa.disabled = false;
            }
        });

        // 3. Siswa -> Load All Data
        selSiswa.addEventListener('change', async () => {
            const idSiswa = selSiswa.value;
            if (!idSiswa) return;

            // [REFACTOR DOCS]: Penggunaan Dataset
            // Mengambil metadata (dataset) secara instan dari attribute option yang sedang dipilih
            // lalu disematkan ke elemen header untuk keperluan Cetak (Print).
            const selectedOption = selSiswa.options[selSiswa.selectedIndex];
            const dataset = selectedOption.dataset;
            const siswaName = dataset.name || idSiswa;

            document.getElementById('print-siswa-name').innerText = `Nama Siswa: ${siswaName}`;
            document.getElementById('print-siswa-nis/nisn').innerText = `NIS/NISN: ${dataset.nis} / ${dataset.nisn}`;
            document.getElementById('print-siswa-kelas').innerText = `Jurusan/Kelas: ${dataset.jurusan} / ${dataset.kelas}`;

            emptyState.classList.add('hidden');
            reportContent.classList.remove('hidden');

            const data = await fetchData(`get_filter_data.php?type=full_report&id_siswa=${idSiswa}`);
            if (!data) return alert("Terjadi kesalahan saat mengambil data laporan.");

            // [REFACTOR DOCS]: Penggunaan `renderList` untuk membersihkan kode
            // Sekarang fungsi rendering untuk view utama dan view print dilakukan dalam 1 pemanggilan
            // Tidak perlu lagi menulis iterasi looping dan string matching berulang-ulang untuk tiap jenis data.

            // RENDER PELANGGARAN
            renderList(data.pelanggaran, ['table-pelanggaran', 'table-pelanggaran-tab'], p => `
                <tr class="border-t border-zinc-100">
                    <td class="p-4 text-zinc-500">${p.tanggal_pelaporan}</td>
                    <td class="p-4 font-bold text-zinc-800">${p.nama_jenis}</td>
                    <td class="p-4 text-zinc-500">${p.keterangan || '-'}</td>
                </tr>`,
                '<tr><td colspan="3" class="p-4 text-center text-zinc-400 italic">Tidak ada riwayat pelanggaran</td></tr>',
                'print-pelanggaran', p => `
                <tr>
                    <td class="px-4 py-2 border border-zinc-900 text-sm text-zinc-900">${p.tanggal_pelaporan}</td>
                    <td class="px-4 py-2 border border-zinc-900 font-bold text-sm text-zinc-900">${p.nama_jenis}</td>
                    <td class="px-4 py-2 border border-zinc-900 text-sm text-zinc-900">${p.keterangan || '-'}</td>
                </tr>`,
                '<tr><td colspan="3" class="px-4 py-2 border border-zinc-900 text-center italic text-sm text-zinc-600">Tidak ada riwayat pelanggaran</td></tr>'
            );

            // RENDER SURAT PERJANJIAN
            renderList(data.perjanjian, ['list-perjanjian', 'list-perjanjian-tab'], s => `
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 mb-2">
                    <p class="font-bold text-blue-800 text-[10px] uppercase">Tgl Surat: ${s.tanggal_surat}</p>
                    <p class="text-zinc-700 mt-1 leading-relaxed">${s.isi_perjanjian}</p>
                </div>`,
                '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>',
                'print-perjanjian', s => `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">Tanggal: ${s.tanggal_surat}</span><br>${s.isi_perjanjian}</div>`,
                '<p class="text-sm italic text-zinc-600">Tidak ada data surat perjanjian.</p>'
            );

            // RENDER PANGGILAN ORTU
            renderList(data.panggilan, ['list-panggilan', 'list-panggilan-tab'], s => `
                <div class="p-3 bg-orange-50 rounded-lg border border-orange-100 mb-2">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-orange-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                        <p class="font-bold text-orange-800 text-[10px] uppercase">${s.tanggal_temu}</p>
                    </div>
                    <p class="text-zinc-700 mt-1 leading-relaxed"><span class="font-medium text-orange-700">Keperluan:</span> ${s.keperluan}</p>
                </div>`,
                '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>',
                'print-panggilan', s => `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">No: ${s.nomor_surat || '-'} | Tanggal Temu: ${s.tanggal_temu}</span><br>Keperluan: ${s.keperluan}</div>`,
                '<p class="text-sm italic text-zinc-600">Tidak ada data surat panggilan.</p>'
            );

            // RENDER SURAT PINDAH
            renderList(data.pindah, ['list-pindah', 'list-pindah-tab'], s => `
                <div class="p-3 bg-red-50 rounded-lg border border-red-100 mb-2">
                    <p class="font-bold text-red-800 text-[10px] uppercase">No: ${s.nomor_surat || '-'}</p>
                    <p class="text-zinc-800 font-medium mt-1">Ke: ${s.nama_sekolah}</p>
                    <p class="text-zinc-600 mt-1 text-[11px] italic">Alasan: ${s.alasan_pindah || '-'}</p>
                </div>`,
                '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>',
                'print-pindah', s => `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">No: ${s.nomor_surat || '-'}</span> | Pindah ke: ${s.nama_sekolah}<br>Alasan: ${s.alasan_pindah || '-'}</div>`,
                '<p class="text-sm italic text-zinc-600">Tidak ada data surat pindah.</p>'
            );

            // RENDER SURAT PERNYATAAN ORTU
            renderList(data.pernyataan_ortu, ['list-pernyataan-ortu', 'list-pernyataan-ortu-tab'], s => `
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 mb-2">
                    <p class="font-bold text-emerald-800 text-[10px] uppercase">Tgl Surat: ${s.tanggal_surat}</p>
                    <p class="text-zinc-700 mt-1 leading-relaxed"><span class="font-medium text-emerald-700">Tujuan:</span> Menyatakan sanggup membina siswa.</p>
                </div>`,
                '<p class="text-zinc-400 italic text-center py-2">Tidak ada data.</p>',
                'print-pernyataan-ortu', s => `<div class="mb-3 text-sm border-l-4 pl-3 border-zinc-900 text-zinc-900"><span class="font-bold">Tanggal: ${s.tanggal_surat}</span><br>Menyatakan sanggup membina siswa.</div>`,
                '<p class="text-sm italic text-zinc-600">Tidak ada data surat pernyataan orang tua.</p>'
            );
        });
    });
</script>