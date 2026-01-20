<?php
session_start();
$requiredRole = 'guru_bk';

require '../middleware/auth.php';
require '../middleware/role.php';
?>

<h1>Halo Guru BK</h1>
<p>Selamat datang, <?= $_SESSION['nama']; ?></p>
<a href="../auth/logout.php">Logout</a>