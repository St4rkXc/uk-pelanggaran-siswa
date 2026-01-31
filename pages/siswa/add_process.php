<?php
session_start();
// Pastikan path config lu bener, bro
require_once __DIR__ . '/../../config/database.php'; 

// Cek Role (Opsional tapi penting buat keamanan)
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: ../dashboard.php?status=error&msg=Akses ditolak!");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_siswa     = $_POST['nama_siswa'] ?? '';
    $nis            = $_POST['nis'] ?? '';
    $nisn           = $_POST['nisn'] ?? '';
    $alamat_rumah   = $_POST['alamat_rumah'] ?? '';
    $kelas          = $_POST['kelas'] ?? '';
    $jurusan        = $_POST['jurusan'] ?? '';
    $jenis_kelamin  = $_POST['jenis_kelamin'] ?? ''; // tinyint (1 atau 0)
    $nama_ortu      = $_POST['nama_ortu'] ?? '';
    $pekerjaan_ortu = $_POST['pekerjaan_ortu'] ?? '';
    $nomor_ortu     = $_POST['nomor_ortu'] ?? '';
    $point          = $_POST['point'] ?? 0;

    try {
        $query = "INSERT INTO siswa (
                    nama_siswa, nis, nisn, alamat_rumah, kelas, 
                    jurusan, jenis_kelamin, nama_ortu, pekerjaan_ortu, 
                    nomor_ortu, point
                  ) VALUES (
                    :nama, :nis, :nisn, :alamat, :kelas, 
                    :jurusan, :jk, :nama_ortu, :kerja_ortu, 
                    :telp_ortu, :point
                  )";

        $stmt = $pdo->prepare($query);
        $params = [
            ':nama'       => $nama_siswa,
            ':nis'        => $nis,
            ':nisn'       => $nisn,
            ':alamat'     => $alamat_rumah,
            ':kelas'      => $kelas,
            ':jurusan'    => $jurusan,
            ':jk'         => $jenis_kelamin,
            ':nama_ortu'  => $nama_ortu,
            ':kerja_ortu' => $pekerjaan_ortu,
            ':telp_ortu'  => $nomor_ortu,
            ':point'      => $point
        ];

        if ($stmt->execute($params)) {
            header("Location: ../siswa/index.php?status=success&msg=Data siswa berhasil ditambah");
        } else {
            header("Location: ../siswa/index.php?status=error&msg=Gagal menyimpan data");
        }
    } catch (PDOException $e) {
        header("Location: ../siswa/index.php?status=error&msg=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../siswa/index.php");
}
exit;