<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

// [PROSES UPDATE DATA]
// Pastikan skrip ini hanya dijalankan ketika menerima request POST (saat modal edit disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Menangkap semua baris input dari form edit di View, termasuk primary key (ID)
        $idSiswa = $_POST['id_siswa']; // Kunci Utama (Primary Key), tidak boleh diedit user
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
        $tempatLahir  = $_POST['tempat_lahir_ortu'] ?? '';
        $tanggalLahir = $_POST['tanggal_lahir_ortu'] ?: null;
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
                tempat_lahir_ortu = ?,
                tanggal_lahir_ortu = ?,
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
            $tempatLahir,
            $tanggalLahir,
            $point,
            $status,
            $idSiswa // Target Primary Key untuk dieksekusi UPDATE
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
