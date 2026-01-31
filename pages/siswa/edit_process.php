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
                point = ? 
                WHERE id_siswa = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama, $jurusan, $kelas, $nis, $nisn, $jk, 
            $alamat, $ortu, $kerja, $telp, $point, $id_siswa
        ]);

        header("Location: " . $_SERVER['HTTP_REFERER'] . "");
        exit;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}