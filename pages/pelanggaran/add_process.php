<?php
session_start();
$requiredRole = ['guru_bk', 'admin', 'guru_mapel'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari Modal Add tadi
    $id_siswa   = $_POST['id_siswa'] ?? null;
    $id_jenis   = $_POST['id_jenis'] ?? null;
    $keterangan = trim($_POST['keterangan'] ?? '');
    $pelapor    = $_SESSION['id_users'] ?? null;
    // Validasi input minimal
    if (!$id_siswa || !$id_jenis || !$pelapor) {
        if ($_SESSION['role'] === 'guru_mapel') {
            header("Location: ../dashboard/guru_mapel.php?status=error&msg=Data seleksi tidak lengkap!");
        } else {
            header("Location: index.php?status=error&msg=Data seleksi tidak lengkap!");
        }
        exit;
    }

    if (!$id_siswa || !$id_jenis || !$pelapor) {
        if ($_SESSION['role'] === 'guru_mapel') {
            header("Location: ../dashboard/guru_mapel.php?status=error&msg=Data seleksi tidak lengkap! Pelapor: " . ($pelapor ? 'Ada' : 'Kosong'));
        } else {
            header("Location: index.php?status=error&msg=Data seleksi tidak lengkap! Pelapor: " . ($pelapor ? 'Ada' : 'Kosong'));
        }
        exit;
    }

    try {
        // MULAI TRANSAKSI
        $pdo->beginTransaction();

        // 1. Ambil bobot poin dari tabel jenis_pelanggaran
        $stmtJenis = $pdo->prepare("SELECT point FROM jenis_pelanggaran WHERE id_jenis = ?");
        $stmtJenis->execute([$id_jenis]);
        $bobot = $stmtJenis->fetchColumn();

        if ($bobot === false) {
            throw new Exception("Jenis pelanggaran tidak valid!");
        }

        // ini but instert ke tabel pelanggaran
        $sqlInsert = "INSERT INTO pelanggaran (id_siswa, id_jenis, keterangan, pelapor, tanggal_pelaporan) 
                      VALUES (:id_siswa, :id_jenis, :keterangan, :pelapor, NOW())";
        $stmtIns = $pdo->prepare($sqlInsert);
        $stmtIns->execute([
            ':id_siswa'   => $id_siswa,
            ':id_jenis'   => $id_jenis,
            ':keterangan' => $keterangan,
            ':pelapor'    => $pelapor
        ]);

        // 3. Update (Potong) Poin di tabel Siswa
        $sqlUpdate = "UPDATE siswa SET point = point - :bobot WHERE id_siswa = :id_siswa";
        $stmtUpd = $pdo->prepare($sqlUpdate);
        $stmtUpd->execute([
            ':bobot'    => $bobot,
            ':id_siswa' => $id_siswa
        ]);

        // Kalau semua OK, simpan permanen
        $pdo->commit();


        if ($_SESSION['role'] === 'guru_mapel') {
            header("Location: ../dashboard/guru_mapel.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } catch (Exception $e) {
        // Kalau ada yang error, batalin semua perubahan (Rollback)
        $pdo->rollBack();
        if ($_SESSION['role'] === 'guru_mapel') {
            header("Location: ../dashboard/guru_mapel.php?status=error&msg=" . urlencode($e->getMessage()));
        } else {
            header("Location: index.php?status=error&msg=" . urlencode($e->getMessage()));
        }   
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
