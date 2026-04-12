<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

// Proteksi: Cuma admin yang boleh eksekusi update


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form modal edit
    $idPerjanjian = $_POST['id_perjanjian'];
    $idPelanggaran = $_POST['id_pelanggaran'];
    $tglPerjanjian = $_POST['tanggal_perjanjian'];
    $isiPerjanjian = $_POST['isi_perjanjian'];

    // Validasi basic biar gak ada field kosong
    if (empty($idPerjanjian) || empty($idPelanggaran) || empty($tglPerjanjian) || empty($isiPerjanjian)) {
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
            $idPelanggaran,
            $tglPerjanjian,
            $isiPerjanjian,
            $idPerjanjian
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
