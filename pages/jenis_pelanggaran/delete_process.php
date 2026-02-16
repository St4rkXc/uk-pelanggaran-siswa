<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM jenis_pelanggaran WHERE id_jenis = ?");
        $stmt->execute([$id]);
        header("Location: index.php?status=success&msg=Data berhasil dihapus");
    } catch (PDOException $e) {
        header("Location: index.php?status=error&msg=Data tidak bisa dihapus karena masih digunakan!");
    }
}
exit;
