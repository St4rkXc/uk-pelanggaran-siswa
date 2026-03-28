<?php
session_start();

// [OTORISASI AKSES]
// Cek Role (Biasanya cuma admin atau guru_bk yang boleh hapus data)
$requiredRole = ['admin', 'guru_bk'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    exit("Akses ditolak!");
}

require_once __DIR__ . '/../../config/database.php';

// [PROSES HAPUS DATA]
// Memastikan script menangkap parameter ID dari query url (?id=...)
if (isset($_GET['id'])) {
    try {
        // 1. Menampung Primary Key target penghapusan
        $id = $_GET['id'];

        // 2. Menyiapkan query DELETE menggunakan objek PDO agar keamanannya terjaga dari SQL Injection
        $stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?");

        // 3. Eksekusi menghapus record dari row database
        $stmt->execute([$id]);

        // 4. Jika sukses terhapus, kembalikan posisi pengguna ke halaman sebelumnya disertai notifikasi toast green success
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=success&msg=Data berhasil dihapus");
        exit;
    } catch (PDOException $e) {
        // 5. KEAMANAN INTEGRITAS (Constraint). 
        // Jika data siswa gagal dihapus (karena datanya masih bergantung/memiliki track record sebagai Foreign Key pada table `Pelanggaran`), maka tolak!
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?status=error&msg=Gagal hapus! Siswa masih memiliki data pelanggaran.");
        exit;
    }
}
