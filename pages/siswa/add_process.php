<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';

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
    $tempat_lahir_ortu  = $_POST['tempat_lahir_ortu'] ?? '';
    $tanggal_lahir_ortu = $_POST['tanggal_lahir_ortu'] ?? null;

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
                    nomor_ortu, tempat_lahir_ortu, tanggal_lahir_ortu, point, status
                  ) VALUES (
                    :nama, :nis, :nisn, :alamat, :kelas, 
                    :jurusan, :jk, :nama_ortu, :kerja_ortu, 
                    :telp_ortu, :tempat_lahir, :tanggal_lahir, :point, :status
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
            ':tempat_lahir'  => $tempat_lahir_ortu,
            ':tanggal_lahir' => $tanggal_lahir_ortu,
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
