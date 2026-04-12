<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsers = $_POST['id_users'] ?? null;
    $name     = trim($_POST['name'] ?? '');
    $role     = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$idUsers || empty($name) || empty($role)) {
        header("Location: index.php?status=error&msg=Data tidak lengkap!");
        exit;
    }

    try {
        // Cek apakah password diisi atau dikosongkan
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE Users SET name = :name, role = :role, password = :password WHERE id_users = :id";
            $params = [
                ':name'     => $name,
                ':role'     => $role,
                ':password' => $hashedPassword,
                ':id'       => $idUsers
            ];
        } else {
            // Jika password gak diganti (abaikan kolom password)
            $sql = "UPDATE Users SET name = :name, role = :role WHERE id_users = :id";
            $params = [
                ':name' => $name,
                ':role' => $role,
                ':id'   => $idUsers
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: index.php?status=success&msg=Data user berhasil diperbarui!");
        exit;
    } catch (PDOException $e) {
        header("Location: index.php?status=error&msg=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
