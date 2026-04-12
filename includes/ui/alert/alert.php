<?php
// Cek apakah ada parameter URL 'status' dan 'msg', ATAU 'error' dari request (biasanya redirect dari *_process.php)
if ((isset($_GET['status']) && isset($_GET['msg'])) || isset($_GET['error'])) {
    // Ambil nilai status, jika tidak ada default ke 'error'
    $status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'error';

    // Tentukan pesan (msg) berdasarkan parameter yang tersedia
    if (isset($_GET['msg'])) {
        $msg = htmlspecialchars($_GET['msg']);
    } elseif (isset($_GET['error'])) {
        // Tangani pesan error umum (contoh: dari login.php)
        // [REFACOTOR DOCS]
        // Php hanya menggunakan ekflusif else if jadi hanya mengambil isset error hanya sekali.
        $errorCode = $_GET['error'];
        if ($errorCode === 'no_account') {
            $msg = 'Akun tidak ditemukan.';
        } elseif ($errorCode === 'wrong_password') {
            $msg = 'Password Salah';
        } else {
            $msg = htmlspecialchars($errorCode);
        }
    } else {
        $msg = 'Terjadi kesalahan.';
    }

    // Default class dan icon untuk alert (Info)
    $alertClass = 'alert-info text-white';
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

    // Ubah class dan icon sesuai status success atau error
    if ($status === 'success') {
        $alertClass = 'alert-success text-white';
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
    } elseif ($status === 'error' || $status === 'failed') {
        $alertClass = 'alert-error text-white';
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
    }
?>
    <!-- Wrapper DaisyUI toast agar popup berada di ujung atas kanan (top-end) dan z-index paling atas -->
    <div class="toast toast-top toast-end z-50 p-4" id="global-alert">
        <!-- Komponen alert DaisyUI sesuai struktur dengan role="alert" -->
        <div role="alert" class="alert <?= $alertClass ?> shadow-lg flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <?= $icon ?>
                <span><?= $msg ?></span>
            </div>
            <!-- Tombol silang untuk menutup alert secara manual -->
            <button onclick="dismissAlert('global-alert')" class="btn btn-ghost btn-sm btn-circle opacity-70 hover:opacity-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        // Fungsi JS untuk menghilangkan popup dengan efek fade out
        function dismissAlert(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity 0.4s ease'; // Transisi perlahan
                el.style.opacity = '0'; // Buat transparan
                setTimeout(() => el.remove(), 400); // Hapus elemen dari DOM
            }

            // Bersihkan parameter dari URL agar popup tidak muncul saat halaman di refresh
            const url = new URL(window.location);
            url.searchParams.delete('status');
            url.searchParams.delete('msg');
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url);
        }

        // Jalankan fungsi dismiss secara otomatis setelah 4 detik
        setTimeout(() => {
            dismissAlert('global-alert');
        }, 4000);
    </script>
<?php
}
?>