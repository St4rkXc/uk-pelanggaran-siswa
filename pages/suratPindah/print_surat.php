<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("ID Surat tidak ditemukan!");
}

try {
    $query = "SELECT sp.*, s.nomor_surat, sw.*, sk.nama_sekolah AS sekolah_tujuan, sk.alamat_sekolah AS alamat_tujuan
              FROM surat_pindah sp
              JOIN surat s ON sp.id_surat_pindah = s.id_jenis_surat AND s.jenis_surat = 'surat_pindah'
              JOIN siswa sw ON sp.id_siswa = sw.id_siswa
              JOIN sekolah sk ON sp.id_sekolah = sk.id_sekolah
              WHERE sp.id_surat_pindah = ?
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $s = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$s) {
        die("Data surat tidak ditemukan!");
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
    <title>Cetak Surat Pindah - <?= htmlspecialchars($s['nama_siswa']) ?></title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
    <style>
        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 0;
            }

            #print-section {
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                width: 21cm;
            }
        }
    </style>
</head>

<body class="bg-zinc-100 p-6 md:p-12 min-h-screen">

    <div id="print-button" class="fixed top-6 right-6 z-50 p-4 bg-white border border-zinc-200 rounded-2xl shadow-2xl no-print">
        <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all shadow-lg shadow-orange-100">
            <span class="icon-print"></span> Cetak Surat
        </button>
    </div>

    <div class="bg-white w-[21cm] min-h-[29.7cm] mx-auto p-16 border border-zinc-200 shadow-xl print:m-0 print:shadow-none print:border-none" id="print-section" style="font-family: 'Times New Roman', serif;">

        <img src="<?= $kopPath ?>" alt="Kop Surat" class="w-full mb-6">

        <div class="text-center mb-4">
            <h1 class="text-xl font-bold uppercase underline tracking-tight text-zinc-950">Surat Keterangan Pindah Sekolah</h1>
            <p class="text-md font-bold mt-1">No : <?= $s['nomor_surat'] ?>/SMK TI/BG/<?= date('m', strtotime($s['tanggal_surat'])) ?>/<?= date('Y', strtotime($s['tanggal_surat'])) ?></p>
        </div>

        <div class="space-y-6 text-[12pt] text-zinc-900 leading-relaxed">
            <p>Yang bertanda tangan di bawah ini Kepala SMK TI BALI GLOBAL Denpasar, kecamatan Denpasar Selatan, Kota Denpasar, Provinsi Bali, Menerangkan bahwa :</p>

            <table class="w-full ml-10">
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Nama Siswa</td>
                    <td class="w-4 ">:</td>
                    <td class="font-bold  uppercase"><?= htmlspecialchars($s['nama_siswa']) ?></td>
                </tr>
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Kelas / Program</td>
                    <td class="w-4 ">:</td>
                    <td class=""><?= $s['kelas'] ?> / <?= $s['jurusan'] ?></td>
                </tr>
                <tr class="align-top">
                    <td class="w-48 font-semibold ">NIS / NISN</td>
                    <td class="w-4 ">:</td>
                    <td class=""><?= $s['nis'] ?> </td>
                </tr>
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Jenis Kelamin</td>
                    <td class="w-4 ">:</td>
                    <td class=""><?= ($s['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan' ?></td>
                </tr>
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Alamat</td>
                    <td class="w-4 ">:</td>
                    <td class=""><?= htmlspecialchars($s['alamat_rumah'] ?? $s['alamat'] ?? '-') ?></td>
                </tr>
            </table>

            <p>Sesuai dengan surat permohonan pindah sekolah dari Orang tua / Wali siswa :</p>

            <table class="w-full ml-10">
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Nama Orang Tua</td>
                    <td class="w-4 ">:</td>
                    <td class=""><?= htmlspecialchars($s['nama_ortu']) ?></td>
                </tr>
                <tr class="align-top">
                    <td class="w-48 font-semibold ">Alamat</td>
                    <td class="w-4 ">:</td>
                    <td class=" text-sm"><?= htmlspecialchars($s['alamat_rumah'] ?? $s['alamat'] ?? '-') ?></td>
                </tr>
            </table>

            <p class="text-justify">
                Telah mengajukan surat permohonan pindah ke <strong class="uppercase"><?= htmlspecialchars($s['sekolah_tujuan']) ?></strong>,
                dengan alasan <strong class="italic"><?= htmlspecialchars($s['alasan_pindah']) ?></strong>.
                Segala kelengkapan administrasi yang bersangkutan sudah diselesaikan.
            </p>
            <p>Demikian surat keterangan pindah ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="mt-20 flex justify-end">
            <div class="w-80 ">
                <p>Denpasar, <?= date('d M Y', strtotime($s['tanggal_surat'])) ?></p>
                <p class="mb-24">Kepala SMK TI Bali Global Denpasar</p>
                <p class="font-bold underline text-lg">Drs. I Gusti Made Murjana, M.Pd</p>
            </div>
        </div>
    </div>

</body>

</html>