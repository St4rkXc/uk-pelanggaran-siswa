<?php
session_start();
$requiredRole = ['admin', 'guru_bk', 'guru_mapel'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

$id_pelanggaran = $_GET['id'] ?? null;

if ($id_pelanggaran) {
    try {
        $pdo->beginTransaction();

        // 1. Ambil info id_siswa dan bobot poin sebelum datanya dihapus
        $sqlInfo = "SELECT p.id_siswa, jp.point 
                    FROM pelanggaran p 
                    JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis 
                    WHERE p.id_pelanggaran = ?";
        $stmtInfo = $pdo->prepare($sqlInfo);
        $stmtInfo->execute([$id_pelanggaran]);
        $data = $stmtInfo->fetch();

        if ($data) {
            $id_siswa = $data['id_siswa'];
            $poin_refund = $data['point'];

            // 2. Hapus data pelanggaran
            $stmtDel = $pdo->prepare("DELETE FROM pelanggaran WHERE id_pelanggaran = ?");
            $stmtDel->execute([$id_pelanggaran]);

            // 3. Refund poin ke tabel siswa (tambahin lagi poinnya)
            $sqlRefund = "UPDATE siswa SET point = point + ? WHERE id_siswa = ?";
            $stmtRefund = $pdo->prepare($sqlRefund);
            $stmtRefund->execute([$poin_refund, $id_siswa]);

            $pdo->commit();
            if ($_SESSION['role'] === 'guru_mapel') {
                header("Location: ../dashboard/guru_mapel.php?status=success&msg=Catatan dihapus dan poin siswa dikembalikan.");
            } else {
                header("Location: index.php?status=success&msg=Catatan dihapus dan poin siswa dikembalikan.");
            }
            exit;
            
        } else {
            throw new Exception("Data pelanggaran tidak ditemukan.");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        if ($_SESSION['role'] === 'guru_mapel') {
            header("Location: ../dashboard/guru_mapel.php?status=error&msg=" . urlencode($e->getMessage()));
        } else {
            header("Location: index.php?status=error&msg=Pelanggaran gagal dihapus karena digunakan pada surat perjanjian atau surat pernyataan orang tua.");
        }
    }
} else {
    if ($_SESSION['role'] === 'guru_mapel') {
        header("Location: ../dashboard/guru_mapel.php?status=error&msg=ID tidak valid.");
    } else {
        header("Location: index.php?status=error&msg=ID tidak valid.");
    }
}
exit;
