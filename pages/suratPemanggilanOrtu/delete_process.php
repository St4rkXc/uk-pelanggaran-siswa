<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
// Ambil ID detail dari URL (GET)
$id_detail = $_GET['id'] ?? null;

if (!$id_detail) {
    header("Location: index.php?status=error&msg=ID tidak ditemukan!");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Ambil info id_surat di tabel induk sebelum detailnya dihapus
    // Kita cari yang jenis_surat-nya sesuai biar gak salah hapus
    $stmtFind = $pdo->prepare("SELECT id_surat FROM surat WHERE id_jenis_surat = ? AND jenis_surat = 'surat_panggilan_ortu'");
    $stmtFind->execute([$id_detail]);
    $surat = $stmtFind->fetch();

    if ($surat) {
        // 2. Hapus di tabel detail dulu
        $stmtDelDetail = $pdo->prepare("DELETE FROM surat_panggilan_ortu WHERE id_surat_panggilan_ortu = ?");
        $stmtDelDetail->execute([$id_detail]);

        // 3. Hapus di tabel induk 'surat'
        $stmtDelInduk = $pdo->prepare("DELETE FROM surat WHERE id_surat = ?");
        $stmtDelInduk->execute([$surat['id_surat']]);

        $pdo->commit();
        header("Location: index.php?status=success&msg=Surat panggilan berhasil dihapus!");
    } else {
        // Kalau record di tabel induk gak ketemu, rollback aja biar aman
        $pdo->rollBack();
        header("Location: index.php?status=error&msg=Data induk tidak ditemukan!");
    }
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header("Location: index.php?status=error&msg=Gagal hapus data: " . $e->getMessage());
    exit;
}
