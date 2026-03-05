<?php
session_start();
// Pastiin path database.php bener sesuai struktur folder lo
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form modal edit
    $id_pindah  = $_POST['id_surat_pindah'] ?? null;
    $id_sekolah = $_POST['id_sekolah'] ?? null;
    $alasan     = $_POST['alasan_pindah'] ?? '';

    // Validasi brutal: jangan kasih lolos kalau ID kosong
    if (!$id_pindah || !$id_sekolah) {
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
        $stmt->execute([$id_sekolah, $alasan, $id_pindah]);

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
