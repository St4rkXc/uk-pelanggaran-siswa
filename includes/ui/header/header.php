<?php
require_once __DIR__ . '/../../../config/database.php';

?>

<header class="flex justify-between items-center px-6 py-4 border-b border-zinc-300">
    <p class="font-paragraph-20 font-semibold text-zinc-800">Selamat Datang, <?= htmlspecialchars($currentUser['role']); ?></p>
    <div class="flex justify-end items-center gap-4">
        <span class="relative flex size-3">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
            <span class="relative inline-flex size-3 rounded-full bg-sky-500"></span>
        </span>
        <p class="font-paragraph-16 font-semibold text-zinc-800">
            <?= htmlspecialchars($currentUser['nama']); ?>
        </p>
        <div class="p-3 rounded-full border border-zinc-300 flex justify-center items-center">
            <span class="icon-user h-6 w-6 "></span>
        </div>
    </div>
</header>