<?php
session_start();

$requiredRole = ['admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

require_once __DIR__ . '/../../config/database.php';


$id_to_delete = $_GET['id'] ?? null;
$current_user_id = $_SESSION['id_users']; // Pastiin lu simpen ID pas login

if (!$id_to_delete) {
    header("Location: index.php?status=error&msg=ID tidak ditemukan");
    exit;
}


if ($id_to_delete == $current_user_id) {
    header("Location: index.php?status=error&msg=Lu nggak bisa hapus akun lu sendiri, bro!");
    exit;
}

try {

    $checkSql = "SELECT name FROM Users WHERE id_users = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$id_to_delete]);
    $user = $checkStmt->fetch();

    if (!$user) {
        header("Location: index.php?status=error&msg=User tidak ditemukan");
        exit;
    }

    $sql = "DELETE FROM Users WHERE id_users = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$id_to_delete])) {
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
