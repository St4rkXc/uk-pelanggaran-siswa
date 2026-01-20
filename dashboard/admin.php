<?php
session_start();
$requiredRole = 'admin';

require '../middleware/auth.php';
require '../middleware/role.php';
?>

<h1>Halo Admin</h1>
<p>Selamat datang, <?= $_SESSION['nama']; ?></p>
<a href="../auth/logout.php">Logout</a>
