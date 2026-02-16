<?php
session_start();
$requiredRole = ['admin'];

require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="flex w-dvw">
    <div class="flex w-full">
        <?php require_once BASE_PATH . '/includes/ui/sidebar/sidebar.php'; ?>
        <div class="p-8 w-full">
            <h1 class="font-heading-24 font-bold text-zinc-700">Data Pelanggaran</h1>
            <p class="font-paragraph-14 text-zinc-500 mt-1">Kelola data pelanggaran siswa dengan mudah dan efisien.</p>

            <div class="mt-6">
                <a href="<?php echo BASE_URL . '/pages/pelanggaran/add.php'; ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <div class="icon-plus h-4 w-4"></div>
                    Tambah Pelanggaran
                </a>
            </div>

    </div>

</body>

</html>