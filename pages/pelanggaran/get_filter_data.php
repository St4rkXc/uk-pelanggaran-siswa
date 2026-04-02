<?php // HARUS DI BARIS 1, TANPA SPASI DI DEPAPANNYA
require_once __DIR__ . '/../../config/database.php';

// [API ENDPOINT / WEBSERVICE UNTUK CHAINED DROPDOWN]
// File ini bertugas sebagai backend mini (API) yang merespons permintaan AJAX/Fetch dari JavaScript.
// Digunakan untuk "Filter Bertingkat" (Chained Dropdown) pada form Tambah Pelanggaran (Pilih Jurusan -> Pilih Kelas -> Pilih Siswa).

// Tambahin ob_clean buat jaga-jaga ada output sampah dari file config (misalnya spasi berlebih) 
// yang bisa merusak format JSON
ob_clean();

// Memastikan respons HTTP dikenali browser sebagai JSON (bukan HTML)
header('Content-Type: application/json');

// Menangkap parameter 'type' dari URL, contoh: get_filter_data.php?type=kelas
$type = $_GET['type'] ?? '';

// [SKENARIO 1]: User memilih Jurusan, sistem harus mengembalikan daftar Kelas yang ada di jurusan tersebut
if ($type === 'kelas') {
    $jurusan = $_GET['jurusan'] ?? '';
    // Mencari daftar kelas spesifik berdasarkan jurusan, tanpa ada kelas yang dobel (DISTINCT)
    $stmt = $pdo->prepare("SELECT DISTINCT kelas FROM siswa WHERE TRIM(jurusan) = ? ORDER BY kelas ASC");
    $stmt->execute([trim($jurusan)]);

    // Mengubah hasil Query DB (Array PHP) menjadi string JSON agar bisa dibaca oleh JavaScript UI
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
// [SKENARIO 2]: User memilih Kelas, sistem harus mengembalikan daftar Nama Siswa di kelas tersebut
elseif ($type === 'siswa') {
    $kelas = $_GET['kelas'] ?? '';
    // Mengambil id_siswa dan nama_siswa saja (tidak butuh data lengkap) untuk menghemat bandwidth
    // Hanya menampilkan siswa dengan status 'Aktif'
    $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa FROM siswa WHERE TRIM(kelas) = ? AND status = 'Aktif' ORDER BY nama_siswa ASC");
    $stmt->execute([trim($kelas)]);

    // Mengembalikan data murid berbentuk JSON
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Mengakhiri eksekusi skrip agar tidak ada proses/teks lain yang ter-load secara tidak sengaja
exit;
