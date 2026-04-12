<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';


$idToDelete = $_GET['id'] ?? null;
$currentUserId = $_SESSION['id_users']; // Pastiin lu simpen ID pas login

if (!$idToDelete) {
    header("Location: index.php?status=error&msg=ID tidak ditemukan");
    exit;
}


if ($idToDelete == $currentUserId) {
    header("Location: index.php?status=error&msg=Lu nggak bisa hapus akun lu sendiri, bro!");
    exit;
}

try {

    $checkSql = "SELECT name FROM Users WHERE id_users = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$idToDelete]);
    $user = $checkStmt->fetch();

    if (!$user) {
        header("Location: index.php?status=error&msg=User tidak ditemukan");
        exit;
    }

    $sql = "DELETE FROM Users WHERE id_users = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$idToDelete])) {
        header("Location: index.php?status=success&msg=User " . $user['name'] . " berhasil didelete!");
    } else {
        header("Location: index.php?status=error&msg=Gagal menghapus user");
    }
    exit;
} catch (PDOException $e) {
    // Biasanya error kalo ada foreign key yang nyangkut
    header("Location: index.php?status=error&msg=" . urlencode("Database Error: " . $e->getMessage()));
    exit;
}
