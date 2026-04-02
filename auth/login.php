<?php session_start();
require_once __DIR__ . '/../config/database.php';
$imgPath = BASE_URL . '/src/public/assets/img/logo_sekolah.png';

?>
<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <title>Login | Sistem Pelanggaran</title>
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="w-dvw h-dvh flex flex-col justify-center items-center bg-zinc-100">
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
    <div class="p-8 rounded-2xl border border-zinc-300 w-[400px] bg-zinc-50">
        <div class="mb-4 space-y-3">
            <div class="p-3 rounded-2xl border border-zinc-300 w-fit">
                <img src="<?php echo $imgPath; ?>" alt="" class="h-13 w-[50px]  ">
            </div>
            <div class="flex-col">
                <p class="font-paragraph-20 font-semibold text-zinc-800">SMK TI Bali Global Denpasar</p>
                <p class="font-paragraph-14 font-medium text-zinc-500">Silahkan login untuk melanjutkan</p>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 p-4 rounded-xl border flex items-start space-x-3 bg-red-50 border-red-100 text-red-600 animate-in fade-in slide-in-from-top-2 duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm font-medium">
                    <?php 
                    if ($_GET['error'] === 'no_account') {
                        echo "Akun tidak ditemukan. Silahkan hubungi administrator.";
                    } elseif ($_GET['error'] === 'wrong_password') {
                        echo "Username atau password salah. Silahkan coba lagi.";
                    } else {
                        echo "Terjadi kesalahan saat login. Silahkan coba lagi.";
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="login_process.php" class="space-y-3">
            <div class="relative flex items-center">
                <input type="text" name="username" placeholder="Username" required class="py-3 px-4 outline outline-zinc-300 rounded-lg w-full placeholder:text-[14px] font-paragraph-14">
                <span class="icon-user absolute right-4 w-5 h-5 text-zinc-400"></span>
            </div>
            <div class="relative flex items-center">
                <input type="password" name="password" placeholder="Password" required class="py-3 px-4 outline outline-zinc-300 rounded-lg w-full placeholder:text-[14px] font-paragraph-14">
                <span class="icon-lock absolute right-4 w-5 h-5 text-zinc-400"></span>
            </div>
            <button type="submit" class="button-primary w-full p-2 ">Login</button>
            <p class="font-paragraph-12 text-zinc-500 text-center">Jika lupa <span class="font-bold">username</span> atau <span class="font-bold">password</span> silahkan kontak dapodik atau bagian TU</p>
        </form>

    </div>
</body>

</html>