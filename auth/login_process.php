<?php
session_start();
require '../config/database.php';

$usernameInput = $_POST['username'];
$passwordInput = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
$stmt->execute([$usernameInput]);
$user = $stmt->fetch();

if ($user && password_verify($passwordInput, $user['password'])) {

    $_SESSION['login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['nama']  = $user['name'];
    $_SESSION['role']  = $user['role'];

    header("Location: ../dashboard/{$user['role']}.php");
    exit;
}

echo "Login gagal";

if ($user) {
    echo "User ditemukan!<br>";
    if (password_verify($passwordInput, $user['password'])) {
        echo "Password cocok!";
        // ... sisa kode login ...
    } else {
        echo "Password salah. Hash di DB: " . $user['password'];
        echo "Password yang dimasukkan: " . $passwordInput;
    }
} else {
    echo "Username tidak ditemukan di database.";
}
