<?php
session_start();
// Security: Cuma admin/master yang boleh akses
$requiredRole = ['admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: index.php?status=error&msg=Unauthorized");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';
    $id_siswa = !empty($_POST['id_siswa']) ? $_POST['id_siswa'] : null;

    if (empty($name) || empty($password) || empty($role)) {
        header("Location: index.php?status=error&msg=Field tidak boleh kosong!");
        exit;
    }

    try {

        $checkSql = "SELECT COUNT(*) FROM Users WHERE name = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$name]);

        if ($checkStmt->fetchColumn() > 0) {
            header("Location: index.php?status=error&msg=Username sudah terdaftar!");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO Users (name, password, role, id_siswa) 
                VALUES (:name, :password, :role, :id_siswa)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'     => $name,
            ':password' => $hashedPassword,
            ':role'     => $role,
            ':id_siswa' => $id_siswa
        ]);

        header("Location: index.php?status=success&msg=User baru berhasil ditambahkan!");
        exit;
    } catch (PDOException $e) {
        // Log error atau tampilin buat debugging
        header("Location: index.php?status=error&msg=" . urlencode("Database Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
