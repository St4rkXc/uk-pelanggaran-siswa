<?php
// Untuk internal PHP (include/require) -> Hasilnya: C:\xampp\htdocs\project
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Untuk Browser (Link/Gambar) -> Hasilnya: http://localhost/project
// Sesuaikan 'nama_project_kamu' dengan nama folder project di htdocs
define('BASE_URL', 'http://localhost/pelanggaran_uk');

define('DB_HOST', 'localhost');
define('DB_NAME', 'pelanggaran_siswa_new');
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
