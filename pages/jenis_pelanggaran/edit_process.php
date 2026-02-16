<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = $_POST['id_jenis'];
    $nama  = trim($_POST['nama_jenis']);
    $desc  = trim($_POST['deskripsi']);
    $point = (int)$_POST['point'];

    $sql = "UPDATE jenis_pelanggaran SET nama_jenis = ?, deskripsi = ?, point = ? WHERE id_jenis = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $desc, $point, $id]);

    header("Location: index.php?status=success&msg=Data berhasil diupdate");
    exit;
}
