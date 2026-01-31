<?php
session_start();
// Cek Role (Biasanya cuma admin yang boleh hapus)
$requiredRole = ['admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    exit("Akses ditolak!");
}

require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];

        $stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?");
        $stmt->execute([$id]);


        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=success&msg=Data berhasil dihapus");
        exit;
    } catch (PDOException $e) {
        // Jika gagal karena data siswa masih ada di tabel pelanggaran (Foreign Key Constraint)
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=error&msg=Gagal hapus! Siswa masih memiliki data pelanggaran.");
        exit;
    }
}
