<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$requiredRole = ['admin', 'guru_bk'];

// Ambil ID dari URL (print_surat.php?id=123)
$id_perjanjian = $_GET['id'] ?? '';

if (empty($id_perjanjian)) {
    die("ID Surat tidak valid!");
}

try {
    // Query Brutal buat ambil semua detail
    $query = "SELECT 
        sp.id_perjanjian, sp.tanggal_surat, sp.isi_perjanjian, 
        sw.nama_siswa, sw.nis, sw.nisn, sw.kelas, sw.jurusan, sw.jenis_kelamin, sw.alamat_rumah,
        sw.nama_ortu, sw.pekerjaan_ortu, sw.nomor_ortu, sw.point,
        p.keterangan AS detail_kejadian,
        jp.nama_jenis AS nama_pelanggaran
    FROM surat_perjanjian sp
    JOIN siswa sw ON sp.id_siswa = sw.id_siswa
    JOIN pelanggaran p ON sp.id_pelanggaran = p.id_pelanggaran
    JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
    WHERE sp.id_perjanjian = ?
    LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_perjanjian]);
    $s = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$s) {
        die("Data surat tidak ditemukan!");
    }
} catch (Exception $e) {
    die("Gagal load data: " . $e->getMessage());
}

$kopPath = BASE_URL . '/src/public/assets/img/kop_surat.jpg'; // Ganti dengan path kop surat lo
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Perjanjian - <?= htmlspecialchars($s['nama_siswa']) ?></title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

    <style>
        /* CSS Khusus buat Print A4 */
        body {
            background: white;
        }

        #print-section {
            font-family: 'Times New Roman', serif;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white;
                font-family: 'Times New Roman', serif;
            }

            /* Sembunyikan tombol cetak pas di-print */
            #print-button {
                display: none;
            }

            /* Atur margin kertas A4 */
            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body class="bg-zinc-100 p-6 md:p-12">

    <div id="print-button" class="fixed top-6 right-6 z-50 w-md p-6 bg-white border border-zinc-200 rounded-lg no-print">
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Penandatangan BK</label>
                <select id="select-guru-bk" class="w-full p-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-zinc-900 transition-all">
                    <option value="Ni Putu Chintya Pradnya Suari, S.Pd">Ni Putu Chintya Pradnya Suari, S.Pd</option>
                    <option value="I Gusti Ayu Rinjani, M.Pd">I Gusti Ayu Rinjani, M.Pd</option>
                    <option value="Bagus Putu Eka Wijaya, S.Pd">Bagus Putu Eka Wijaya, S.Pd</option>
                    <option value="Custom">-- Input Manual --</option>
                </select>
            </div>

            <div id="custom-name-wrapper" class="hidden">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2">Nama Guru Manual</label>
                <input type="text" id="custom-guru-name" class="w-full p-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm" placeholder="Nama & Gelar...">
            </div>

            <div class="flex gap-2">
                <button onclick="terapkanGuruBK()" class="flex-1 py-2.5 bg-zinc-900 text-white rounded-xl font-bold text-sm hover:bg-black transition-all">
                    Terapkan
                </button>
                <button onclick="window.print()" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">
                    <span class="icon-printer"></span> Cetak
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white max-w-[21cm] min-h-[29.7cm] mx-auto p-12 border border-zinc-200 shadow-xl rounded-2xl print:shadow-none print:border-none print:p-0" id="print-section">

        <img src="<?= $kopPath ?>" alt="Kop Surat Sekolah" class="w-full h-30 image-full mb-1">

        <div class="text-center mb-2">
            <h1 class="text-xl font-bold uppercase underline tracking-tight text-zinc-950">Surat Pernyataan / Perjanjian Siswa</h1>
        </div>

        <p class="  text-justify text-zinc-800">Yang bertanda tangan di bawah ini.</p>

        <table class="w-full ms-10 text-zinc-900 space-y-1">
            <tr>
                <td class="w-48  font-semibold">Nama Siswa</td>
                <td class="w-4 ">:</td>
                <td><?= htmlspecialchars($s['nama_siswa']) ?></td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">NIS / NISN</td>
                <td class="w-4 ">:</td>
                <td class=""><?= $s['nis'] ?> </td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">Kelas</td>
                <td class="w-4 ">:</td>
                <td class=""><?= $s['kelas'] ?> </td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">Jurusan</td>
                <td class="w-4 ">:</td>
                <td class=""><?= $s['jurusan'] ?></td>
            </tr>


        </table>
        <div class="ms-10">
            <div class="font-semibold">Masalah</div>
            <p><?= htmlspecialchars($s['isi_perjanjian']) ?></p>
        </div>
        <table class="w-full ms-10 text-zinc-900 space-y-1">
            <tr>
                <td class="w-48  font-semibold">Nama Orang tua</td>
                <td class="w-4 ">:</td>
                <td><?= htmlspecialchars($s['nama_ortu']) ?></td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">Pekerjaan</td>
                <td class="w-4 ">:</td>
                <td class=" leading-relaxed text-sm"><?= htmlspecialchars($s['pekerjaan_ortu']) ?></td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">Alamat Rumah</td>
                <td class="w-4 ">:</td>
                <td class=" leading-relaxed text-sm"><?= htmlspecialchars($s['alamat_rumah']) ?></td>
            </tr>
            <tr>
                <td class="w-48  font-semibold">No</td>
                <td class="w-4 ">:</td>
                <td class=""><?= $s['nomor_ortu'] ?> </td>
            </tr>



        </table>

        <p class="mb-6 leading-relaxed text-justify text-zinc-800">
            Menyatakan dan berjanji akan bersungguh-sungguh berubah dan bersedia menaati aturan dan tata tertib sekolah. Apabila selama masa pembinaan tidak mengalami perubahan, maka siswa yang bersangkutan
            dikembalikan kepada orang tua/wali.
            <br>
            Demikian surat pernyataan ini saya buat dengan sesungguhnya tanpa ada tekanan dari siapapun.
        </p>
        <div class="grid grid-cols-2 gap-8  text-zinc-950">
            <div>
                <p>Mengetahui,</p>
                <p class="mb-16">Orang Tua / Wali Siswa</p>
                <p class="font-bold underline"><?= htmlspecialchars($s['nama_ortu']) ?></p>

            </div>
            <div>
                <p>Denpasar, <?= date('d M Y', strtotime($s['tanggal_surat'])) ?></p>
                <p class="mb-16">Siswa Yang Menyatakan,</p>
                <p class="font-bold underline"><?= htmlspecialchars($s['nama_siswa']) ?></p>

            </div>
            <div>
                <p class="mb-16">Guru Bimbingan Konseling</p>
                <p id="nama-guru-bk-display" class="font-bold underline">Ni Putu Chintya Pradnya Suari, S.Pd</p>
            </div>
            <div>
                <p class="mb-16">Guru Wali Kelas</p>
                <p class="font-bold underline">.................................................................</p>

            </div>
            <div class="col-span-2 text-center">
                <p>Mengetahui,</p>
                <p class="mb-16">Wakasek Kesiswaan</p>
                <p class="font-bold underline">Bagus Putu Eka Wijaya, S.Pd</p>
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
            if (customInput.value.trim() !== "") {
                display.innerText = customInput.value;
            } else {
                alert("Isi dulu nama manualnya, bro!");
            }
        } else {
            display.innerText = select.value;
        }
    }

    // Toggle input manual pas select berubah
    document.getElementById('select-guru-bk').addEventListener('change', function() {
        const customWrapper = document.getElementById('custom-name-wrapper');
        if (this.value === 'Custom') {
            customWrapper.classList.remove('hidden');
        } else {
            customWrapper.classList.add('hidden');
        }
    });
</script>