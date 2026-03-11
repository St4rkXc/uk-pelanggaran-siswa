<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}


$id = $_GET['id'] ?? '';

if (!empty($id)) {
    try {
        $sql = "DELETE FROM surat_perjanjian WHERE id_perjanjian = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            header("Location: index.php?status=success&msg=Surat perjanjian berhasil dihapus");
        } else {
            header("Location: index.php?status=error&msg=Data tidak ditemukan");
        }
        exit;
    } catch (PDOException $e) {
        header("Location: index.php?status=error&msg=Gagal menghapus data: " . $e->getMessage());
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
