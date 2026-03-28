<?php
session_start(); // Memulai sesi pengguna yang sedang login
require_once __DIR__ . '/../../config/database.php'; // Mengimpor koneksi database

// [OTORISASI AKSES]
// Cek Role: Hanya user dengan role 'admin' atau 'guru_bk' yang diizinkan untuk menambah data siswa
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    // Jika tidak diizinkan, kembalikan ke dashboard dengan notifikasi gagal
    header("Location: ../dashboard.php?status=error&msg=Akses ditolak!");
    exit;
}

// [PROSES SIMPAN DATA]
// Pastikan skrip ini hanya dijalankan ketika menerima request POST (saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Menangkap semua inputan dari form HTML
    // Disertai pencegahan null (menggunakan ??) untuk memberikan nilai kosong default jika input tidak diisi
    $nama_siswa     = $_POST['nama_siswa'] ?? '';
    $nis            = $_POST['nis'] ?? '';
    $nisn           = $_POST['nisn'] ?? '';
    $alamat_rumah   = $_POST['alamat_rumah'] ?? '';
    $kelas          = $_POST['kelas'] ?? '';
    $jurusan        = $_POST['jurusan'] ?? '';
    $jenis_kelamin  = $_POST['jenis_kelamin'] ?? '';
    $nama_ortu      = $_POST['nama_ortu'] ?? '';
    $pekerjaan_ortu = $_POST['pekerjaan_ortu'] ?? '';
    $nomor_ortu     = $_POST['nomor_ortu'] ?? '';

    // 2. Nilai Default Poin & Status
    // Saat pendaftaran pertama kali, otomatis siswa mendapat 100 Poin prilaku
    $point          = $_POST['point'] ?? 100;
    // Otomatis disetting Aktif
    $status         = $_POST['status'] ?? 'Aktif';

    try {
        // 3. Menyiapkan perintah SQL untuk insert menggunakan teknik Prepared Statement
        // Parameter diikat dengan tanda titik dua (:nama_field) demi mencegah serangan SQL Injection
        $query = "INSERT INTO siswa (
                    nama_siswa, nis, nisn, alamat_rumah, kelas, 
                    jurusan, jenis_kelamin, nama_ortu, pekerjaan_ortu, 
                    nomor_ortu, point, status
                  ) VALUES (
                    :nama, :nis, :nisn, :alamat, :kelas, 
                    :jurusan, :jk, :nama_ortu, :kerja_ortu, 
                    :telp_ortu, :point, :status
                  )";

        // 4. Mendaftarkan mapping Data variabel menuju Parameter Query Bind (#Bind Param)
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
            ':point'      => $point,
            ':status'     => $status
        ];

        // 5. Eksekusi penyimpanan data ke Database
        if ($stmt->execute($params)) {
            // Jika berhasil eksekusi, redirect kembali ke halaman list dengan notifikasi "Data siswa berhasil ditambah"
            header("Location: ../siswa/index.php?status=success&msg=Data siswa berhasil ditambah");
        } else {
            // Jika eksekusi gagal dari sisi basis data
            header("Location: ../siswa/index.php?status=error&msg=Gagal menyimpan data");
        }
    } catch (PDOException $e) {
        // Jika ada kesalahan teknis saat insert database (contoh: Duplikat NISN, Kolom terlalu panjang)
        header("Location: ../siswa/index.php?status=error&msg=" . urlencode($e->getMessage()));
    }
} else {
    // Jika ada yang iseng langsung membuka file ini, kembalikan ke /siswa/index.php
    header("Location: ../siswa/index.php");
}
exit;
