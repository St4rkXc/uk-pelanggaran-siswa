<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

$id_surat = $_GET['id'] ?? '';

if (empty($id_surat)) {
    die("ID Surat tidak valid!");
}

try {
    $query = "SELECT 
        spo.id_surat_pernyataan_ortu, spo.tanggal_surat,
        sw.nama_siswa, sw.kelas, sw.alamat_rumah,
        sw.nama_ortu, sw.pekerjaan_ortu, sw.nomor_ortu,
        sw.tempat_lahir_ortu, sw.tanggal_lahir_ortu
    FROM surat_pernyataan_ortu spo
    JOIN siswa sw ON spo.id_siswa = sw.id_siswa
    WHERE spo.id_surat_pernyataan_ortu = ?
    LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_surat]);
    $s = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$s) {
        die("Data surat tidak ditemukan!");
    }
} catch (Exception $e) {
    die("Gagal load data: " . $e->getMessage());
}

$kopPath = BASE_URL . '/src/public/assets/img/kop_surat.jpg';

// Format tempat tanggal lahir ortu
$ttl_ortu = "-";
if (!empty($s['tempat_lahir_ortu']) && !empty($s['tanggal_lahir_ortu'])) {
    $ttl_ortu = htmlspecialchars($s['tempat_lahir_ortu']) . ', ' . date('d F Y', strtotime($s['tanggal_lahir_ortu']));
} elseif (!empty($s['tempat_lahir_ortu'])) {
    $ttl_ortu = htmlspecialchars($s['tempat_lahir_ortu']);
} elseif (!empty($s['tanggal_lahir_ortu'])) {
    $ttl_ortu = date('d F Y', strtotime($s['tanggal_lahir_ortu']));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Pernyataan Orang Tua - <?= htmlspecialchars($s['nama_siswa']) ?></title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

    <style>
        body {
            background: white;
            color: black;
            font-size: 16px;
        }

        #print-section {
            font-family: 'Times New Roman', serif;
            line-height: 1.5;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white;
                font-family: 'Times New Roman', serif;
            }

            #print-button {
                display: none;
            }

            @page {
                size: A4;
                margin: 1cm;
            }
        }
        
        .indent-text {
            text-indent: 40px;
        }
    </style>
</head>

<body class="bg-zinc-100 p-6 md:p-12">

    <div id="print-button" class="fixed top-6 right-6 z-50 p-6 bg-white border border-zinc-200 rounded-lg no-print">
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-md">
            <span class="icon-printer mr-2"></span> Cetak Surat
        </button>
    </div>

    <div class="bg-white max-w-[21cm] min-h-[29.7cm] mx-auto p-12 border border-zinc-200 shadow-xl rounded-2xl print:shadow-none print:border-none print:p-0" id="print-section">

        <img src="<?= $kopPath ?>" alt="Kop Surat Sekolah" class="w-full h-auto mb-6">

        <div class="text-center mb-10 mt-6">
            <h1 class="text-xl font-bold uppercase tracking-tight text-black">SURAT PERNYATAAN ORANG TUA</h1>
        </div>

        <p class="text-justify text-black mb-4 indent-text">Yang bertandatangan di bawah ini orang tua/ wali siswa SMK TI Bali Global Denpasar :</p>

        <div class="pl-10 mb-8">
            <table class="w-full text-black">
                <tr>
                    <td class="w-48 py-2">Nama</td>
                    <td class="w-4 py-2">:</td>
                    <td class="py-2"><?= htmlspecialchars($s['nama_ortu']) ?></td>
                </tr>
                <tr>
                    <td class="w-48 py-2">Tempat/ tanggal Lahir</td>
                    <td class="w-4 py-2">:</td>
                    <td class="py-2"><?= $ttl_ortu ?></td>
                </tr>
                <tr>
                    <td class="w-48 py-2">Pekerjaan</td>
                    <td class="w-4 py-2">:</td>
                    <td class="py-2"><?= htmlspecialchars($s['pekerjaan_ortu'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="w-48 py-2">Alamat Rumah</td>
                    <td class="w-4 py-2">:</td>
                    <td class="py-2"><?= htmlspecialchars($s['alamat_rumah'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="w-48 py-2">No. Hp./Telp.</td>
                    <td class="w-4 py-2">:</td>
                    <td class="py-2"><?= htmlspecialchars($s['nomor_ortu'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <p class="text-justify text-black mb-6 leading-relaxed">
            Menyatakan memang benar sanggup membina anak kami yang bernama <span class="font-bold border-b border-black print:border-none print:underline"><?= htmlspecialchars($s['nama_siswa']) ?></span>, 
            Kelas : <span class="font-bold border-b border-black print:border-none print:underline"><?= htmlspecialchars($s['kelas']) ?></span> 
            untuk lebih disiplin mengikuti proses pembelajaran dan mengikuti Tata Tertib Sekolah.
        </p>

        <p class="text-justify text-black mb-16 leading-relaxed">
            Demikianlah pernyataan kami dan jika tidak sesuai dengan pernyataan diatas, anak kami dapat dikeluarkan dari sekolah ini dengan rekomendasi pindah ke SMK lain yang serumpun.
        </p>

        <div class="flex justify-end text-black">
            <div class="text-center">
                <p>Denpasar, <?= date('d F Y', strtotime($s['tanggal_surat'])) ?></p>
                <p>Yang membuat pernyataan</p>
                <p class="mb-24">Orang Tua/Wali siswa</p>
                <p class="font-bold underline text-lg whitespace-nowrap px-4 border-b border-dashed border-black inline-block mt-4 min-w-50 text-center" style="text-decoration:none !important;">
                    <?= empty($s['nama_ortu']) ? '...................................................' : htmlspecialchars($s['nama_ortu']) ?>
                </p>
            </div>
        </div>

        <div class="mt-20 text-black">
            <p class="font-bold underline mb-4">NB:</p>
            <p class="underline leading-relaxed">
                Jika siswa tidak bisa mengikuti proses pembelajaran sampai bulan mei 2025 maka
            </p>
            <p class="underline leading-relaxed mt-1">
                Siswa dinyatakan mengundurkan diri.
            </p>
        </div>

    </div>

</body>

</html>
