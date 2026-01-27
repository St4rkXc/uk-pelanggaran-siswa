<?php
session_start();
$requiredRole = 'siswa';

require '../middleware/auth.php';
require '../middleware/role.php';
require '../config/database.php';

// ambil id_siswa dari session
$idSiswa = $_SESSION['id_siswa'];

// query ke tabel Siswa
$stmt = $pdo->prepare("SELECT nama_siswa, point FROM Siswa WHERE id_siswa = ?");
$stmt->execute([$idSiswa]);
$siswa = $stmt->fetch();
?>

<h1>Halo Siswa</h1>

<p>Nama: <?= htmlspecialchars($siswa['nama_siswa']); ?></p>
<p>Total Poin Pelanggaran: <strong><?= $siswa['point']; ?></strong></p>