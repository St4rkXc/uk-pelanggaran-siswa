<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Pelanggaran Siswa | Landing Page</title>
    <!-- Tailwind CSS (Output) -->
    <link href="./src/css/output.css" rel="stylesheet">
    <!-- AOS Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts: Manrope (Already in input.css but ensuring it's available) -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(228, 228, 231, 1);
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(244, 244, 245, 0.8), transparent),
                        radial-gradient(circle at bottom left, rgba(228, 228, 231, 0.5), transparent);
        }
    </style>
</head>

<body class="bg-zinc-50 font-['Manrope'] overflow-x-hidden text-zinc-900">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass-card py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl border border-zinc-300 bg-white">
                    <img src="./src/assets/img/logo_sekolah.png" alt="Logo" class="h-8 w-8 object-contain">
                </div>
                <span class="font-heading-5 font-bold tracking-tight">Manajemen Pelanggaran</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="font-paragraph-14 font-medium hover:text-zinc-600 transition-colors">Fitur</a>
                <a href="#stats" class="font-paragraph-14 font-medium hover:text-zinc-600 transition-colors">Statistik</a>
                <a href="#workflow" class="font-paragraph-14 font-medium hover:text-zinc-600 transition-colors">Alur Kerja</a>
                <a href="./auth/login.php" class="button-primary">Masuk ke Sistem</a>
            </div>
            <!-- Mobile Toggle -->
            <button id="mobile-menu-btn" class="md:hidden p-2 text-zinc-900">
                <div class="icon-filter h-6 w-6"></div>
            </button>
        </div>
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="hidden fixed inset-0 bg-white z-60 flex flex-col p-10 space-y-6">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-3">
                    <img src="./src/assets/img/logo_sekolah.png" alt="Logo" class="h-8 w-8">
                    <span class="font-bold">Manajemen Pelanggaran</span>
                </div>
                <button id="close-menu-btn" class="p-2">
                    <div class="icon-delete h-6 w-6"></div>
                </button>
            </div>
            <a href="#features" class="mobile-link text-2xl font-bold">Fitur</a>
            <a href="#stats" class="mobile-link text-2xl font-bold">Statistik</a>
            <a href="#workflow" class="mobile-link text-2xl font-bold">Alur Kerja</a>
            <hr class="border-zinc-100">
            <a href="./auth/login.php" class="button-primary text-center py-4 text-xl">Masuk ke Sistem</a>
        </div>
    </nav>

    <!-- Section 1: Hero -->
    <section class="min-h-screen flex items-center pt-24 hero-gradient relative">
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right" data-aos-duration="1000">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-200 text-zinc-700 text-[12px] font-bold mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-zinc-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-zinc-500"></span>
                    </span>
                    Sistem Pemantauan Terpadu
                </div>
                <h1 class="font-display font-extrabold mb-6 leading-[1.1]">
                    Tegakkan <span class="text-zinc-500 underline decoration-zinc-300">Kedisiplinan</span> Dengan Transparansi Digital.
                </h1>
                <p class="font-paragraph-18 text-zinc-600 mb-8 max-w-lg">
                    Platform manajemen pelanggaran sekolah modern yang membantu guru, siswa, dan orang tua dalam memantau perilaku serta prestasi kedisiplinan secara real-time.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="./auth/login.php" class="button-primary px-8 py-3 text-lg">
                        Mulai Sekarang
                        <i class="icon-arrow-up-right ml-2 h-4 w-4"></i>
                    </a>
                    <a href="#features" class="button-secondary px-8 py-3 text-lg">Pelajari Fitur</a>
                </div>
            </div>
            <div data-aos="zoom-in" data-aos-duration="1200" class="relative hidden md:block">
                <div class="bg-white p-6 rounded-4xl shadow-2xl border border-zinc-200 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    <div class="bg-zinc-100 rounded-xl h-100 w-full flex items-center justify-center overflow-hidden">
                        <!-- Placeholder for dashboard preview -->
                        <div class="text-zinc-400 flex flex-col items-center gap-3">
                            <div class="icon-report h-16 w-16 opacity-30"></div>
                            <span class="font-paragraph-14 font-medium italic uppercase tracking-widest text-zinc-400">Dashboard Preview</span>
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute -bottom-6 -left-6 bg-zinc-900 text-white p-6 rounded-2xl shadow-xl w-48" data-aos="fade-up" data-aos-delay="500">
                    <p class="text-3xl font-bold">100%</p>
                    <p class="text-[12px] opacity-70 italic">Data Aman & Terpusat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Features -->
    <section id="features" class="py-24 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="font-heading-2 font-bold mb-4">Fitur Unggulan Sistem</h2>
                <p class="font-paragraph-16 text-zinc-600 max-w-2xl mx-auto">
                    Kami menyediakan alat yang komprehensif untuk memastikan setiap data pelanggaran tercatat dengan akurat dan dapat dipertanggungjawabkan.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl border border-zinc-100 hover:border-zinc-300 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-zinc-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-zinc-900 group-hover:text-white transition-colors">
                        <i class="icon-search h-7 w-7"></i>
                    </div>
                    <h3 class="font-heading-5 font-bold mb-3">Pelacakan Real-time</h3>
                    <p class="font-paragraph-14 text-zinc-600 leading-relaxed">
                        Pantau riwayat pelanggaran setiap siswa secara langsung dan instan darimana saja melalui dashboard admin.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl border border-zinc-100 hover:border-zinc-300 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-zinc-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-zinc-900 group-hover:text-white transition-colors">
                        <i class="icon-report h-7 w-7"></i>
                    </div>
                    <h3 class="font-heading-5 font-bold mb-3">Laporan Otomatis</h3>
                    <p class="font-paragraph-14 text-zinc-600 leading-relaxed">
                        Hasilkan laporan surat pemanggilan orang tua, surat perjanjian, dan pindah secara otomatis hanya dengan beberapa klik.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl border border-zinc-100 hover:border-zinc-300 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-zinc-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-zinc-900 group-hover:text-white transition-colors">
                        <i class="icon-user h-7 w-7"></i>
                    </div>
                    <h3 class="font-heading-5 font-bold mb-3">Manajemen User</h3>
                    <p class="font-paragraph-14 text-zinc-600 leading-relaxed">
                        Akses khusus untuk Guru Mapel, Guru BK, dan Admin untuk menjaga integritas data sesuai peran masing-masing.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Statistics -->
    <section id="stats" class="py-24 bg-zinc-900 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-zinc-800 rounded-full blur-[100px] opacity-20 -mr-20 -mt-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div data-aos="zoom-in" data-aos-delay="100">
                    <p class="text-5xl font-extrabold mb-2">100%</p>
                    <p class="font-paragraph-14 opacity-60">Digitalisasi Data</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <p class="text-5xl font-extrabold mb-2">1k+</p>
                    <p class="font-paragraph-14 opacity-60">Siswa Terintegrasi</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <p class="text-5xl font-extrabold mb-2">50+</p>
                    <p class="font-paragraph-14 opacity-60">Tenaga Pendidik</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="400">
                    <p class="text-5xl font-extrabold mb-2">24/7</p>
                    <p class="font-paragraph-14 opacity-60">Akses Sistem</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Workflow -->
    <section id="workflow" class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="font-heading-2 font-bold mb-4">Bagaimana Ini Bekerja?</h2>
                <p class="font-paragraph-16 text-zinc-600">Alur kerja sederhana untuk efisiensi maksimal di sekolah anda.</p>
            </div>
            <div class="relative">
                <!-- Line decorator for desktop -->
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-zinc-100 -translate-y-1/2"></div>
                
                <div class="grid md:grid-cols-3 gap-12 relative z-10">
                    <!-- Step 1 -->
                    <div class="bg-white p-6 text-center" data-aos="fade-right" data-aos-delay="100">
                        <div class="w-16 h-16 bg-zinc-900 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-xl border-4 border-white">
                            1
                        </div>
                        <h4 class="font-heading-5 font-bold mb-3">Pencatatan</h4>
                        <p class="font-paragraph-14 text-zinc-600">Guru menginput data pelanggaran siswa secara digital melalui aplikasi.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="bg-white p-6 text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="w-16 h-16 bg-zinc-900 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-xl border-4 border-white">
                            2
                        </div>
                        <h4 class="font-heading-5 font-bold mb-3">Validasi & Poin</h4>
                        <p class="font-paragraph-14 text-zinc-600">Sistem otomatis menghitung akumulasi poin dan menentukan kategori pelanggaran.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="bg-white p-6 text-center" data-aos="fade-left" data-aos-delay="500">
                        <div class="w-16 h-16 bg-zinc-900 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold shadow-xl border-4 border-white">
                            3
                        </div>
                        <h4 class="font-heading-5 font-bold mb-3">Tindakan Lanjut</h4>
                        <p class="font-paragraph-14 text-zinc-600">Penerbitan surat teguran atau pemanggilan sebagai bentuk pembinaan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: CTA / Final -->
    <section id="cta" class="py-24 bg-zinc-50 border-t border-zinc-200">
        <div class="container mx-auto px-6 text-center" data-aos="zoom-in">
            <h2 class="font-heading-2 font-extrabold mb-8 tracking-tight">Siap Untuk Meningkatkan <br> Kedisiplinan Sekolah?</h2>
            <p class="font-paragraph-18 text-zinc-600 mb-10 max-w-xl mx-auto">
                Bergabunglah dengan puluhan guru yang telah beralih ke manajemen digital untuk masa depan sekolah yang lebih baik.
            </p>
            <div class="flex justify-center gap-4">
                <a href="./auth/login.php" class="button-primary px-10 py-4 text-xl">Masuk Sistem Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-12 border-t border-zinc-200">
        <div class="container mx-auto px-6 grid md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <img src="./src/assets/img/logo_sekolah.png" alt="Logo" class="h-8 w-8">
                    <span class="font-heading-5 font-bold">UKK Pelanggaran</span>
                </div>
                <p class="font-paragraph-14 text-zinc-500 max-w-sm mb-6">
                    Solusi manajemen kedisiplinan siswa modern untuk mendukung terciptanya lingkungan belajar yang kondusif dan tertib.
                </p>
            </div>
            <div>
                <h5 class="font-bold mb-6">Navigasi</h5>
                <ul class="space-y-3 font-paragraph-14 text-zinc-500">
                    <li><a href="#" class="hover:text-zinc-900">Tentang</a></li>
                    <li><a href="#features" class="hover:text-zinc-900">Fitur</a></li>
                    <li><a href="#workflow" class="hover:text-zinc-900">Alur Kerja</a></li>
                    <li><a href="./auth/login.php" class="hover:text-zinc-900">Login Admin</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6">Kontak</h5>
                <ul class="space-y-3 font-paragraph-14 text-zinc-500">
                    <li>support@sekolah.sch.id</li>
                    <li>+62 123 4567 890</li>
                    <li>Jl. Pendidikan No. 123</li>
                </ul>
            </div>
        </div>
        <div class="container mx-auto px-6 mt-12 pt-8 border-t border-zinc-100 text-center font-paragraph-12 text-zinc-400">
            &copy; 2024 Manajemen Pelanggaran Siswa. All rights reserved.
        </div>
    </footer>

    <!-- Scripts -->
    <!-- AOS Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            easing: 'ease-out-quint',
            duration: 800
        });

        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        const toggleMenu = () => {
            mobileMenu.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        };

        menuBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));

        // Smooth Scroll for Navbar Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>

</body>

</html>