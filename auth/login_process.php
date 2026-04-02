<?php
session_start();
require_once __DIR__ . '/../config/database.php';


$usernameInput = $_POST['username'];
$passwordInput = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
$stmt->execute([$usernameInput]);
$user = $stmt->fetch();

if ($user) {
    if (password_verify($passwordInput, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_users'] = $user['id_users'];
        $_SESSION['nama']  = $user['name'];
        $_SESSION['role']  = $user['role'];
        $_SESSION['id_siswa'] = $user['id_siswa'];

        header("Location: ../pages/dashboard/{$user['role']}.php");
        exit;
    } else {
        // Password salah
        header("Location: login.php?error=wrong_password");
        exit;
    }
} else {
    // Akun tidak ditemukan
    header("Location: login.php?error=no_account");
    exit;
}
