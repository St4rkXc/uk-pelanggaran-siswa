<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form modal edit
    $idPindah  = $_POST['id_surat_pindah'] ?? null;
    $idSekolah = $_POST['id_sekolah'] ?? null;
    $alasan     = $_POST['alasan_pindah'] ?? '';

    // Validasi brutal: jangan kasih lolos kalau ID kosong
    if (!$idPindah || !$idSekolah) {
        $_SESSION['error'] = "Data tidak valid atau ID hilang, bro!";
        header("Location: index.php");
        exit;
    }

    try {
        // Query update detail pindah
        $sql = "UPDATE surat_pindah 
                SET id_sekolah = ?, 
                    alasan_pindah = ? 
                WHERE id_surat_pindah = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idSekolah, $alasan, $idPindah]);

        // Set pesan sukses buat ditampilin di dashboard
        $_SESSION['success'] = "Data kepindahan siswa berhasil diperbarui!";
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        // Kalau error database, rollback dan kasih tau
        die("Gagal update database: " . $e->getMessage());
    }
} else {
    // Kalau ada yang coba akses file ini langsung via URL
    header("Location: index.php");
    exit;
}
