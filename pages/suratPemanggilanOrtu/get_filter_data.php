<?php
// get_filter_data.php

// Pastikan tidak ada output apapun sebelum header
ob_start();

require_once __DIR__ . '/../../config/database.php';

// Bersihkan buffer biar gak ada karakter aneh yang ngerusak JSON
ob_clean();
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

try {
    if ($type === 'kelas') {
        $jurusan = $_GET['jurusan'] ?? '';

        // Ambil kelas unik berdasarkan jurusan
        $stmt = $pdo->prepare("SELECT DISTINCT kelas FROM siswa WHERE TRIM(jurusan) = ? ORDER BY kelas ASC");
        $stmt->execute([trim($jurusan)]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);
    } elseif ($type === 'siswa') {
        $kelas = $_GET['kelas'] ?? '';

        // Ambil id dan nama siswa berdasarkan kelas, hanya yang statusnya 'Aktif'
        $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa FROM siswa WHERE TRIM(kelas) = ? AND status = 'Aktif' ORDER BY nama_siswa ASC");
        $stmt->execute([trim($kelas)]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);
    } elseif ($type === 'pelanggaran') {
        $id_siswa = $_GET['id_siswa'] ?? '';

        $stmt = $pdo->prepare("
        SELECT p.id_pelanggaran, jp.nama_jenis, p.tanggal_pelaporan 
        FROM pelanggaran p
        JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
        WHERE p.id_siswa = ? 
        ORDER BY p.tanggal_pelaporan DESC
    ");
        $stmt->execute([$id_siswa]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    // Kalau ada error database, kirim pesan error dalam format JSON
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
