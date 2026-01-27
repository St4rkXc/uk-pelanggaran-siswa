<?php session_start(); 
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>
<body class="w-dvw h-dvh flex flex-col justify-center items-center bg-zinc-100">
    <div class="p-8 rounded-2xl border border-zinc-300 w-[400px] bg-zinc-50">
        <div class="mb-4 space-y-3">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit" >
                <img src="../src/assets/img/logo_sekolah.png" alt="" class="h-13 w-[50px]  ">
            </div>
            <div class="flex-col">
                <p class="font-paragraph-20 font-semibold text-zinc-800">SMK TI Bali Global Denpasar</p>
                <p class="font-paragraph-14 font-medium text-zinc-500">Silahkan login untuk melanjutkan</p>
            </div>
        </div>
        <form method="POST" action="login_process.php" class="space-y-3">
            <div class="relative flex items-center">
                <input type="text" name="username" placeholder="Username" required class="py-3 px-4 outline outline-zinc-300 rounded-lg w-full placeholder:text-[14px] font-paragraph-14">
                <span class="icon-user absolute right-4 w-5 h-5 text-zinc-400"></span>
            </div>
            <div class="relative flex items-center">
                <input type="password" name="password" placeholder="Password" required class="py-3 px-4 outline outline-zinc-300 rounded-lg w-full placeholder:text-[14px] font-paragraph-14">
                <span class="icon-user absolute right-4 w-5 h-5 text-zinc-400"></span>
            </div>
            <button type="submit" class="button-primary w-full p-2 ">Login</button>
            <p class="font-paragraph-12 text-zinc-500 text-center">Jika lupa <span class="font-bold">username</span>  atau <span class="font-bold">password</span> silahkan kontak dapodik atau bagian TU</p>
        </form>
    </div>
</body>

</html>