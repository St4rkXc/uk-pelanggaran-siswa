<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];
require_once __DIR__ . '/../../config/database.php';

// Pastikan cuma admin/guru yang bisa akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header('Location: index.php?status=error&msg=Unauthorized');
    exit;
}

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
