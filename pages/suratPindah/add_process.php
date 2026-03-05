<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_siswa     = $_POST['id_siswa'] ?? null;
    $id_sekolah   = $_POST['id_sekolah'] ?? null;
    $alasan       = $_POST['alasan_pindah'] ?? '';
    $nomor_surat  = (int)($_POST['nomor_surat'] ?? 0);
    $tgl_surat    = date('Y-m-d');

    if (!$id_siswa || !$id_sekolah || !$nomor_surat) {
        die("Data tidak lengkap, bro!");
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert ke detail pindah
        $sqlDetail = "INSERT INTO surat_pindah (id_siswa, id_sekolah, alasan_pindah, tanggal_surat) 
                      VALUES (?, ?, ?, ?)";
        $stmtDetail = $pdo->prepare($sqlDetail);
        $stmtDetail->execute([$id_siswa, $id_sekolah, $alasan, $tgl_surat]);

        $lastIdPindah = $pdo->lastInsertId();

        // 2. Insert ke induk surat
        $sqlSurat = "INSERT INTO surat (id_jenis_surat, jenis_surat, nomor_surat) 
                     VALUES (?, 'surat_pindah', ?)";
        $stmtSurat = $pdo->prepare($sqlSurat);
        $stmtSurat->execute([$lastIdPindah, $nomor_surat]);

        // 3. Update Status Siswa jadi Non-Aktif
        $sqlUpdateSiswa = "UPDATE siswa SET status = 'pindah' WHERE id_siswa = ?";
        $stmtUpdateSiswa = $pdo->prepare($sqlUpdateSiswa);
        $stmtUpdateSiswa->execute([$id_siswa]);

        $pdo->commit();

        $_SESSION['success'] = "Siswa berhasil dipindahkan dan status dinonaktifkan.";
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal proses pindah: " . $e->getMessage());
    }
}
