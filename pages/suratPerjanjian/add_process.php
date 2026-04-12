<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSiswa = $_POST['id_siswa'];
    $idPelanggaran = $_POST['id_pelanggaran'];
    $tglPerjanjian = $_POST['tanggal_perjanjian'];
    $tglSurat = $_POST['tanggal_surat'];
    $isiPerjanjian = $_POST['isi_perjanjian'];

    try {
        // Karena cuma ke satu tabel, lo gak wajib pake transaction, tapi buat jaga-jaga tetep oke
        $pdo->beginTransaction();

        // 1. INSERT langsung ke tabel surat_perjanjian
        // Nomor surat kita abaikan sesuai request lo
        $sqlPerjanjian = "INSERT INTO surat_perjanjian (id_siswa, id_pelanggaran, tanggal_perjanjian, isi_perjanjian, tanggal_surat) 
                          VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlPerjanjian);
        $stmt->execute([$idSiswa, $idPelanggaran, $tglPerjanjian, $isiPerjanjian, $tglSurat]);

        $pdo->commit();

        // Redirect ke dashboard
        header("Location: index.php?status=success&msg=Surat Perjanjian Berhasil Dibuat");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal simpan data: " . $e->getMessage());
    }
}
