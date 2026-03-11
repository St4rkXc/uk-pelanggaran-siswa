<?php // HARUS DI BARIS 1, TANPA SPASI DI DEPAPANNYA
require_once __DIR__ . '/../../config/database.php';

// Tambahin ob_clean buat jaga-jaga ada output sampah dari file config
ob_clean();
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

if ($type === 'kelas') {
    $jurusan = $_GET['jurusan'] ?? '';
    $stmt = $pdo->prepare("SELECT DISTINCT kelas FROM siswa WHERE TRIM(jurusan) = ? ORDER BY kelas ASC");
    $stmt->execute([trim($jurusan)]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($type === 'siswa') {
    $kelas = $_GET['kelas'] ?? '';
    $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa FROM siswa WHERE TRIM(kelas) = ? ORDER BY nama_siswa ASC");
    $stmt->execute([trim($kelas)]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
exit;
