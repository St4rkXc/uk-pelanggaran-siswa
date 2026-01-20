<?php
session_start();
$requiredRole = 'siswa';

require '../middleware/auth.php';
require '../middleware/role.php';
?>

<h1>Halo Siswa</h1>
<p>Selamat datang, <?= $_SESSION['nama']; ?></p>