<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'];
    $tgl_surat = $_POST['tanggal_surat'];

    try {
        $sql = "INSERT INTO surat_pernyataan_ortu (id_siswa, tanggal_surat) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa, $tgl_surat]);

        header("Location: index.php?status=success&msg=Surat Pernyataan Orang Tua Berhasil Dibuat");
        exit;
    } catch (Exception $e) {
        die("Gagal simpan data: " . $e->getMessage());
    }
}
