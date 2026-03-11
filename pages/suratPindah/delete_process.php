<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$requiredRole = ['admin', 'guru_bk'];


$id_pindah = $_GET['id'] ?? null;

if (!$id_pindah) {
    $_SESSION['error'] = "ID tidak ditemukan";
    header("Location: index.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Cari dulu ID Siswa sebelum datanya dihapus (buat update status nanti)
    $stmtCari = $pdo->prepare("SELECT id_siswa FROM surat_pindah WHERE id_surat_pindah = ?");
    $stmtCari->execute([$id_pindah]);
    $dataPindah = $stmtCari->fetch();

    if ($dataPindah) {
        $id_siswa = $dataPindah['id_siswa'];

        // 2. Hapus dari tabel induk 'surat' dulu (Constraint check)
        // Karena kita pake jenis_surat = 'surat_pindah'
        $sqlSurat = "DELETE FROM surat WHERE id_jenis_surat = ? AND jenis_surat = 'surat_pindah'";
        $pdo->prepare($sqlSurat)->execute([$id_pindah]);

        // 3. Hapus dari tabel detail 'surat_pindah'
        $sqlDetail = "DELETE FROM surat_pindah WHERE id_surat_pindah = ?";
        $pdo->prepare($sqlDetail)->execute([$id_pindah]);

        // 4. Update Status Siswa jadi Aktif lagi
        $sqlUpdateSiswa = "UPDATE siswa SET status = 'aktif' WHERE id_siswa = ?";
        $pdo->prepare($sqlUpdateSiswa)->execute([$id_siswa]);

        $pdo->commit();
        $_SESSION['success'] = "Data dihapus dan siswa kembali Aktif!";
    } else {
        $pdo->rollBack();
        $_SESSION['error'] = "Data tidak ditemukan di database.";
    }

    header("Location: index.php");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal hapus data: " . $e->getMessage());
}
