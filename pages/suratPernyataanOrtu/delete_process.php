<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM surat_pernyataan_ortu WHERE id_surat_pernyataan_ortu = ?");
        $stmt->execute([$id]);

        header("Location: index.php?status=success&msg=Surat Pernyataan Orang Tua berhasil dihapus");
        exit;
    } catch (PDOException $e) {
         header("Location: index.php?status=error&msg=Gagal menghapus surat: " . urlencode($e->getMessage()));
         exit;
    }
} else {
    header("Location: index.php?status=error&msg=ID tidak ditemukan");
    exit;
}
