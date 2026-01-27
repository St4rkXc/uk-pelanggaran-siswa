<?php
session_start();
$requiredRole = 'admin';

require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>

</head>
<body>
    <?php require_once BASE_PATH . '/includes/ui/sidebar/admin_sidebar.php'; ?>
    <a href="../auth/logout.php">Logout</a>
</body>
</html>