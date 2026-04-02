<?php
require_once __DIR__ . '/../../config/database.php';

$type = $_GET['type'] ?? '';

header('Content-Type: application/json');

try {
    if ($type === 'kelas' && isset($_GET['jurusan'])) {
        $jurusan = $_GET['jurusan'];
        $stmt = $pdo->prepare("SELECT DISTINCT kelas FROM siswa WHERE jurusan = ? ORDER BY kelas ASC");
        $stmt->execute([$jurusan]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($type === 'siswa' && isset($_GET['kelas'])) {
        $kelas = $_GET['kelas'];
        // Hanya menampilkan siswa yang aktif
        $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa FROM siswa WHERE kelas = ? AND status = 'Aktif' ORDER BY nama_siswa ASC");
        $stmt->execute([$kelas]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
