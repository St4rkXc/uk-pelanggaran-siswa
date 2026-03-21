<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

// Proteksi: Cuma admin yang boleh eksekusi update


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form modal edit
    $id_perjanjian = $_POST['id_perjanjian'];
    $id_pelanggaran = $_POST['id_pelanggaran'];
    $tgl_perjanjian = $_POST['tanggal_perjanjian'];
    $isi_perjanjian = $_POST['isi_perjanjian'];

    // Validasi basic biar gak ada field kosong
    if (empty($id_perjanjian) || empty($id_pelanggaran) || empty($tgl_perjanjian) || empty($isi_perjanjian)) {
        header("Location: index.php?status=error&msg=Semua field wajib diisi!");
        exit;
    }

    try {
        // Query Update
        $sql = "UPDATE surat_perjanjian SET 
                id_pelanggaran = ?, 
                tanggal_perjanjian = ?, 
                isi_perjanjian = ? 
                WHERE id_perjanjian = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_pelanggaran,
            $tgl_perjanjian,
            $isi_perjanjian,
            $id_perjanjian
        ]);

        // Redirect balik ke dashboard dengan pesan sukses
        header("Location: index.php?status=success&msg=Surat Perjanjian berhasil diperbarui");
        exit;
    } catch (PDOException $e) {
        // Jika ada error database
        header("Location: index.php?status=error&msg=Gagal update data: " . $e->getMessage());
        exit;
    }
} else {
    // Kalau ada yang coba akses file ini tanpa POST
    header('Location: index.php');
    exit;
}
