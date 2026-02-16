<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_siswa = $_POST['id_siswa'];
        $nama     = $_POST['nama_siswa'];
        $jurusan  = $_POST['jurusan'];
        $kelas    = $_POST['kelas'];
        $nis      = $_POST['nis'];
        $nisn     = $_POST['nisn'];
        $jk       = $_POST['jenis_kelamin'];
        $alamat   = $_POST['alamat_rumah'];
        $ortu     = $_POST['nama_ortu'];
        $kerja    = $_POST['pekerjaan_ortu'];
        $telp     = $_POST['nomor_ortu'];
        $point    = $_POST['point'];
        $status   = $_POST['status']; // Ambil data status dari modal edit

        // Query SQL yang sudah ditambah field status
        $sql = "UPDATE siswa SET 
                nama_siswa = ?, 
                jurusan = ?, 
                kelas = ?, 
                nis = ?, 
                nisn = ?, 
                jenis_kelamin = ?, 
                alamat_rumah = ?, 
                nama_ortu = ?, 
                pekerjaan_ortu = ?, 
                nomor_ortu = ?, 
                point = ?,
                status = ? 
                WHERE id_siswa = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama,
            $jurusan,
            $kelas,
            $nis,
            $nisn,
            $jk,
            $alamat,
            $ortu,
            $kerja,
            $telp,
            $point,
            $status,
            $id_siswa
        ]);

        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=success&msg=Data berhasil diupdate");
        exit;
    } catch (PDOException $e) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}
