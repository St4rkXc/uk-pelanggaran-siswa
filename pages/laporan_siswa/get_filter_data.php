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

        // [REFACTOR DOCS]: Query ini diperbarui untuk juga mengambil data 'nis', 'nisn', 'jurusan', dan 'kelas'.
        // Data ekstra ini diambil di awal agar frontend bisa langsung menampilkannya di halaman cetak (print)
        // tanpa harus melakukan request AJAX/fetch terpisah saat mencetak laporan.
        $stmt = $pdo->prepare("SELECT id_siswa, nama_siswa, nis, nisn, jurusan, kelas FROM siswa WHERE TRIM(kelas) = ? AND status = 'Aktif' ORDER BY nama_siswa ASC");
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
    } elseif ($type === 'full_report') {
        $id_siswa = $_GET['id_siswa'] ?? '';

        // 1. Ambil Data Pelanggaran
        $stmtP = $pdo->prepare("
            SELECT p.id_pelanggaran, jp.nama_jenis, p.tanggal_pelaporan, p.keterangan 
            FROM pelanggaran p
            JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
            WHERE p.id_siswa = ? ORDER BY p.tanggal_pelaporan DESC
        ");
        $stmtP->execute([$id_siswa]);
        $pelanggaran = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil Data Surat Panggilan (Include No Surat dari tabel surat)
        $stmtSPO = $pdo->prepare("
            SELECT spo.*, s.nomor_surat 
            FROM surat_panggilan_ortu spo
            LEFT JOIN surat s ON spo.id_surat_panggilan_ortu = s.id_jenis_surat AND s.jenis_surat = 'surat_panggilan_ortu'
            WHERE spo.id_siswa = ? ORDER BY spo.tanggal_surat DESC
        ");
        $stmtSPO->execute([$id_siswa]);
        $panggilan = $stmtSPO->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ambil Data Surat Perjanjian
        $stmtSPJ = $pdo->prepare("
            SELECT * FROM surat_perjanjian WHERE id_siswa = ? ORDER BY tanggal_surat DESC
        ");
        $stmtSPJ->execute([$id_siswa]);
        $perjanjian = $stmtSPJ->fetchAll(PDO::FETCH_ASSOC);

        // ... di dalam elseif ($type === 'full_report')
        // 4. Ambil Data Surat Pindah (Join dengan nama sekolah & tabel surat)
        $stmtSPD = $pdo->prepare("
    SELECT sp.*, sk.nama_sekolah, s.nomor_surat 
    FROM surat_pindah sp
    JOIN sekolah sk ON sp.id_sekolah = sk.id_sekolah
    LEFT JOIN surat s ON sp.id_surat_pindah = s.id_jenis_surat AND s.jenis_surat = 'surat_pindah'
    WHERE sp.id_siswa = ?
");
        $stmtSPD->execute([$id_siswa]);
        $pindah = $stmtSPD->fetchAll(PDO::FETCH_ASSOC);

        $stmtSPD->execute([$id_siswa]);
        $pindah = $stmtSPD->fetchAll(PDO::FETCH_ASSOC);

        // 5. Ambil Data Surat Pernyataan Orang Tua
        $stmtSPOrtu = $pdo->prepare("
            SELECT * FROM surat_pernyataan_ortu WHERE id_siswa = ? ORDER BY tanggal_surat DESC
        ");
        $stmtSPOrtu->execute([$id_siswa]);
        $pernyataan_ortu = $stmtSPOrtu->fetchAll(PDO::FETCH_ASSOC);

        // Gabungin semua jadi satu response JSON
        echo json_encode([
            'pelanggaran'      => $pelanggaran,
            'panggilan'        => $panggilan,
            'perjanjian'       => $perjanjian,
            'pindah'           => $pindah,
            'pernyataan_ortu'  => $pernyataan_ortu
        ]);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    // Kalau ada error database, kirim pesan error dalam format JSON
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
