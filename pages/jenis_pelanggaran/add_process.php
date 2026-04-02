<?php
session_start();
$requiredRole = ['guru_bk', 'admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_jenis']);
    $deskripsi = trim($_POST['deskripsi']);
    $point     = (int)$_POST['point'];

    if (empty($nama) || empty($point)) {
        header("Location: index.php?status=error&msg=Nama dan Poin wajib diisi!");
        exit;
    }

    try {
        $sql = "INSERT INTO jenis_pelanggaran (nama_jenis, deskripsi, point) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$nama, $deskripsi, $point])) {
            header("Location: index.php?status=success&msg=Master data berhasil ditambah");
        } else {
            header("Location: index.php?status=error&msg=Gagal menyimpan data");
        }
    } catch (PDOException $e) {
        header("Location: index.php?status=error&msg=" . urlencode($e->getMessage()));
    }
    exit;
}
