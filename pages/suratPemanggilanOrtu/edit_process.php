<?php
session_start();
$requiredRole = ['admin', 'guru_bk'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo "<pre>"; print_r($_POST); echo "</pre>"; die();
    // Ambil data dari form modal edit
    $id_panggilan = $_POST['id_surat_panggilan_ortu'] ?? null;
    $tanggal_temu = $_POST['tanggal_temu'] ?? null;
    $keperluan   = $_POST['keperluan'] ?? ''; 

    // Validasi basic
    if (!$id_panggilan || !$tanggal_temu || !$keperluan) {
        header("Location: index.php?status=error&msg=Data tidak lengkap!");
        exit;
    }

    try {
        // Query Update menggunakan PDO Prepared Statements
        $sql = "UPDATE surat_panggilan_ortu SET 
                tanggal_temu = :tgl, 
                keperluan = :kep 
                WHERE id_surat_panggilan_ortu = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tgl' => $tanggal_temu,
            ':kep' => $keperluan,
            ':id'  => $id_panggilan
        ]);

        // Redirect dengan feedback
        header("Location: index.php?status=success&msg=Data panggilan berhasil diperbarui");
        exit;
    } catch (PDOException $e) {
        // Log error atau tampilkan pesan jika gagal
        header("Location: index.php?status=error&msg=Gagal update database: " . $e->getMessage());
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
