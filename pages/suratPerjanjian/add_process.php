<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Pastikan cuma admin/guru yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa = $_POST['id_siswa'];
    $id_pelanggaran = $_POST['id_pelanggaran'];
    $tgl_perjanjian = $_POST['tanggal_perjanjian'];
    $tgl_surat = $_POST['tanggal_surat'];
    $isi_perjanjian = $_POST['isi_perjanjian'];

    try {
        // Karena cuma ke satu tabel, lo gak wajib pake transaction, tapi buat jaga-jaga tetep oke
        $pdo->beginTransaction();

        // 1. INSERT langsung ke tabel surat_perjanjian
        // Nomor surat kita abaikan sesuai request lo
        $sqlPerjanjian = "INSERT INTO surat_perjanjian (id_siswa, id_pelanggaran, tanggal_perjanjian, isi_perjanjian, tanggal_surat) 
                          VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlPerjanjian);
        $stmt->execute([$id_siswa, $id_pelanggaran, $tgl_perjanjian, $isi_perjanjian, $tgl_surat]);

        $pdo->commit();

        // Redirect ke dashboard
        header("Location: index.php?status=success&msg=Surat Perjanjian Berhasil Dibuat");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal simpan data: " . $e->getMessage());
    }
}
