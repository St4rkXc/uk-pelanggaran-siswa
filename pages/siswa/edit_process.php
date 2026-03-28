<?php
session_start(); // Memulai sesi login pengguna

// [OTORISASI AKSES]
// Cek Role: Modul ini hanya membolehkan role 'admin' atau 'guru_bk'
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    // Karena request edit mungkin dikirim dengan AJAX/Form, kita bisa melempar return format JSON atau redirect
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php'; // Mengimpor koneksi database

// [PROSES UPDATE DATA]
// Pastikan skrip ini hanya dijalankan ketika menerima request POST (saat modal edit disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Menangkap semua baris input dari form edit di View, termasuk primary key (ID)
        $id_siswa = $_POST['id_siswa']; // Kunci Utama (Primary Key), tidak boleh diedit user
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
        $status   = $_POST['status']; // Mengambil nilai dropdown pilihan Status ('Aktif', 'Pindah', dll) dari modal form edit

        // 2. Menyiapkan kueri PHP PDO SQL UPDATE (menggunakan placeholder `?` untuk keamanan Anti SQL-Injection)
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

        // 3. Melekatkan input dari form tadi dengan urutan placeholder ? pada Query
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
            $id_siswa // Target Primary Key untuk dieksekusi UPDATE
        ]);

        // 4. Kembali secara mulus (Redirect) menuju halaman darimana user berasal ketika berhasil di perbarui
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=success&msg=Data berhasil diupdate");
        exit;
    } catch (PDOException $e) {
        // 5. Menangani jika ada Error atau konflik (Misalya NISN duplikat)
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}
