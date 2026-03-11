<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$requiredRole = ['admin', 'guru_bk'];


// Ambil ID Detail dari URL
$id_panggilan = $_GET['id'] ?? '';

if (empty($id_panggilan)) {
    die("ID Surat tidak valid!");
}

try {
    // Query JOIN ke tabel induk 'surat' buat dapet Nomor Surat
    $query = "SELECT 
        spo.*, 
        s.nomor_surat,
        sw.nama_siswa, sw.nis, sw.kelas, sw.jurusan, sw.nama_ortu
    FROM surat_panggilan_ortu spo
    JOIN surat s ON spo.id_surat_panggilan_ortu = s.id_jenis_surat 
                 AND s.jenis_surat = 'surat_panggilan_ortu'
    JOIN siswa sw ON spo.id_siswa = sw.id_siswa
    WHERE spo.id_surat_panggilan_ortu = ?
    LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_panggilan]);
    $s = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$s) {
        die("Data surat panggilan tidak ditemukan!");
    }
} catch (Exception $e) {
    die("Gagal load data: " . $e->getMessage());
}

$kopPath = BASE_URL . '/src/public/assets/img/kop_surat.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Panggilan Ortu - <?= htmlspecialchars($s['nama_siswa']) ?></title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <style>
        body {
            background: white;

        }

        #print-section {
            font-family: 'Times New Roman', serif;
        }

        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                font-family: 'Times New Roman', serif;
            }

            #print-button {
                display: none;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }

        .content-area {
            line-height: 1.8;
            color: #18181b;
        }
    </style>
</head>

<body class="bg-zinc-100 p-6 md:p-12">

    <div id="print-button" class="fixed top-6 right-6 z-50 w-md p-6 bg-white border border-zinc-200 rounded-lg no-print">
        <label class="block text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Pilih Guru BK Penandatangan</label>
        <div class="space-y-4">
            <select id="select-guru-bk" class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:ring-2 focus:ring-zinc-900 outline-none transition-all">
                <option value="I Gusti Ayu Rinjani, M.Pd">I Gusti Ayu Rinjani, M.Pd</option>
                <option value="Ni Putu Chintya Pradnya Suari, S.Pd">Ni Putu Chintya Pradnya Suari, S.Pd</option>
                <option value="Bagus Putu Eka Wijaya, S.Kom">Bagus Putu Eka Wijaya, S.Kom</option>
                <option value="Custom">-- Input Manual --</option>
            </select>

            <div id="custom-name-wrapper" class="hidden">
                <label class="block text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Nama Guru BK Manual</label>
                <input type="text" id="custom-guru-name" class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl" placeholder="Ketik Nama & Gelar...">
            </div>

            <div class="flex gap-2">
                <button onclick="terapkanGuruBK()" class="flex-1 py-3 bg-zinc-900 text-white rounded-xl font-bold hover:bg-black transition-all">
                    Terapkan
                </button>
                <button onclick="window.print()" class="px-4 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">
                    Cetak
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white max-w-[21cm] min-h-[29.7cm] mx-auto p-12 border border-zinc-200 shadow-xl rounded-2xl print:shadow-none print:border-none print:p-0" id="print-section">

        <img src="<?= $kopPath ?>" alt="Kop Surat Sekolah" class="w-full h-auto mb-6">

        <div class="grid grid-cols-2 mb-8 text-sm">
            <table class="w-full">
                <tr>
                    <td class="w-16 py-0.5">No.</td>
                    <td>: <?= $s['nomor_surat'] ?>/SMKTI/B/I/<?= date('Y') ?></td>
                </tr>
                <tr>
                    <td class="py-0.5">Lamp.</td>
                    <td>: -</td>
                </tr>
                <tr>
                    <td class="py-0.5 font-bold">Perihal</td>
                    <td class="font-bold">: Pemanggilan Orang Tua / Wali Siswa</td>
                </tr>
            </table>
        </div>

        <div class="content-area space-y-4">
            <div class="mb-4">
                <p>Kepada</p>
                <p>Yth. Bapak/ Ibu</p>
                <div class="ml-8 flex">
                    <span class="w-32">Orang Tua / Wali</span>
                    <span>: <span class=""><?= htmlspecialchars($s['nama_siswa']) ?></span></span>
                </div>
                <div class="ml-8 flex">
                    <span class="w-32">Kelas / Nis</span>
                    <span>: <?= $s['kelas'] ?> / <?= $s['nis'] ?></span>
                </div>
            </div>

            <p class="text-justify">Dengan hormat,</p>
            <p class="text-justify ">Bersama surat ini, kami mengharapkan kehadiran Bapak / Ibu pada :</p>

            <table class="w-full ml-12">
                <tr>
                    <td class="w-40 py-1">Hari / Tanggal</td>
                    <td class="w-4">:</td>
                    <td class="f"><?= date('l, d F Y', strtotime($s['tanggal_temu'])) ?></td>
                </tr>
                <tr>
                    <td class="py-1">Pukul</td>
                    <td>:</td>
                    <td><?= date('H:i', strtotime($s['tanggal_temu'])) ?> Wita</td>
                </tr>
                <tr>
                    <td class="py-1">Tempat</td>
                    <td>:</td>
                    <td>SMK TI Bali Global Denpasar</td>
                </tr>
                <tr>
                    <td class="py-1">Keperluan</td>
                    <td>:</td>
                    <td class=""><?= htmlspecialchars($s['keperluan']) ?></td>
                </tr>
            </table>

            <p class="text-justify">Demikian surat ini kami sampaikan, besar harapan kami pertemuan ini agar tidak diwakilkan. Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p>
        </div>

        <div class="mt-20 grid grid-cols-2  text-sm">
            <div>
                <p class="mb-24 ">Mengetahui,<br>Waka Kesiswaan</p>
                <p class="font-bold underline ">Bagus Putu Eka Wijaya, S.Kom</p>
            </div>
            <div>
                <p class="mb-24 ">Denpasar, <?= date('d F Y', strtotime($s['tanggal_surat'])) ?><br>Guru BK</p>
                <p id="nama-guru-bk-display" class="font-bold underline ">I Gusti Ayu Rinjani, M.Pd</p>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function terapkanGuruBK() {
        const select = document.getElementById('select-guru-bk');
        const customWrapper = document.getElementById('custom-name-wrapper');
        const customInput = document.getElementById('custom-guru-name');
        const display = document.getElementById('nama-guru-bk-display');

        if (select.value === 'Custom') {
            customWrapper.classList.remove('hidden');
            if (customInput.value.trim() !== "") {
                display.innerText = customInput.value;
            }
        } else {
            customWrapper.classList.add('hidden');
            display.innerText = select.value;
        }
    }

    // Logic buat munculin input manual otomatis pas select berubah
    document.getElementById('select-guru-bk').addEventListener('change', function() {
        const customWrapper = document.getElementById('custom-name-wrapper');
        if (this.value === 'Custom') {
            customWrapper.classList.remove('hidden');
        } else {
            customWrapper.classList.add('hidden');
        }
    });
</script>