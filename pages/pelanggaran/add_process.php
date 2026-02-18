<?php
// session_start();
// var_dump($_POST);
// var_dump($_SESSION);
// die();
session_start();
// Security Check: Hanya admin yang bisa eksekusi
$requiredRole = ['admin'];

$pelapor = $_SESSION['id_users'] ?? null;

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari Modal Add tadi
    $id_siswa   = $_POST['id_siswa'] ?? null;
    $id_jenis   = $_POST['id_jenis'] ?? null;
    $keterangan = trim($_POST['keterangan'] ?? '');
    $pelapor    = $_SESSION['id_users'] ?? null; // ID Admin/Guru yang lagi login

    // Validasi input minimal
    if (!$id_siswa || !$id_jenis || !$pelapor) {
        header("Location: index.php?status=error&msg=Data seleksi tidak lengkap!");
        exit;
    }

    if (!$id_siswa || !$id_jenis || !$pelapor) {
        header("Location: index.php?status=error&msg=Data seleksi tidak lengkap! Pelapor: " . ($pelapor ? 'Ada' : 'Kosong'));
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

        header("Location: index.php?status=success&msg=Pelanggaran berhasil dicatat dan poin siswa telah dipotong!");
        exit;
    } catch (Exception $e) {
        // Kalau ada yang error, batalin semua perubahan (Rollback)
        $pdo->rollBack();
        header("Location: index.php?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
