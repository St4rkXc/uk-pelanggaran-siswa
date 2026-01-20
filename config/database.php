<?php
// define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/pos-indomaret');
define('DB_HOST', 'localhost');
define('DB_NAME', 'pelanggaran_siswa');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
