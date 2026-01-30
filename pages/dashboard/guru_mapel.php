<?php
session_start();
$requiredRole = 'guru_mapel';

require '../middleware/auth.php';
require '../middleware/role.php';
?>

<h1>Halo Guru Mapel</h1>
<p>Selamat datang, <?= $_SESSION['nama']; ?></p>
<a href="../auth/logout.php">Logout</a>
