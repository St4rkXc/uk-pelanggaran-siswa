<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSiswa     = $_POST['id_siswa'] ?? null;
    $idSekolah   = $_POST['id_sekolah'] ?? null;
    $alasan       = $_POST['alasan_pindah'] ?? '';
    $nomorSurat  = (int)($_POST['nomor_surat'] ?? 0);
    $tglSurat    = date('Y-m-d');

    if (!$idSiswa || !$idSekolah || !$nomorSurat) {
        die("Data tidak lengkap, bro!");
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert ke detail pindah
        $sqlDetail = "INSERT INTO surat_pindah (id_siswa, id_sekolah, alasan_pindah, tanggal_surat) 
                      VALUES (?, ?, ?, ?)";
        $stmtDetail = $pdo->prepare($sqlDetail);
        $stmtDetail->execute([$idSiswa, $idSekolah, $alasan, $tglSurat]);

        $lastIdPindah = $pdo->lastInsertId();

        // 2. Insert ke induk surat
        $sqlSurat = "INSERT INTO surat (id_jenis_surat, jenis_surat, nomor_surat) 
                     VALUES (?, 'surat_pindah', ?)";
        $stmtSurat = $pdo->prepare($sqlSurat);
        $stmtSurat->execute([$lastIdPindah, $nomorSurat]);

        // 3. Update Status Siswa jadi Non-Aktif
        $sqlUpdateSiswa = "UPDATE siswa SET status = 'pindah' WHERE id_siswa = ?";
        $stmtUpdateSiswa = $pdo->prepare($sqlUpdateSiswa);
        $stmtUpdateSiswa->execute([$idSiswa]);

        $pdo->commit();

        $_SESSION['success'] = "Siswa berhasil dipindahkan dan status dinonaktifkan.";
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal proses pindah: " . $e->getMessage());
    }
}
