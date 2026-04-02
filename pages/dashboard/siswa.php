<?php
session_start();
$requiredRole = ['siswa'];


require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/middleware/role.php';
require_once BASE_PATH . '/includes/helpers.php';
$currentUser = [
    'nama' => $_SESSION['nama'],
    'role' => $_SESSION['role'],
];
// ambil id_siswa dari session
$idSiswa = $_SESSION['id_siswa'];

// query ke tabel Siswa
$stmt = $pdo->prepare("SELECT nama_siswa, point FROM Siswa WHERE id_siswa = ?");
$stmt->execute([$idSiswa]);
$siswa = $stmt->fetch();


// Ambil detail identitas siswa
$stmt = $pdo->prepare("SELECT nama_siswa, nis, nisn, kelas, jurusan, point, jenis_kelamin FROM Siswa WHERE id_siswa = ?");
$stmt->execute([$idSiswa]);
$siswa = $stmt->fetch();



// Ambil riwayat pelanggaran terakhir siswa ini
$queryRiwayat = "SELECT p.*, jp.nama_jenis, jp.point as point_potong 
                 FROM pelanggaran p
                 JOIN jenis_pelanggaran jp ON p.id_jenis = jp.id_jenis
                 WHERE p.id_siswa = ? 
                 ORDER BY p.tanggal_pelaporan DESC 
                 LIMIT 10";
$stmtRiwayat = $pdo->prepare($queryRiwayat);
$stmtRiwayat->execute([$idSiswa]);
$riwayat = $stmtRiwayat->fetchAll();

// Logic warna status berdasarkan poin
$poin = $siswa['point'] ?? 0;
$statusColor = "text-emerald-600";
$bgColor = "bg-emerald-50";
$barColor = "bg-emerald-500";

if ($poin <= 50) {
    $statusColor = "text-amber-600";
    $bgColor = "bg-amber-50";
    $barColor = "bg-amber-500";
}
if ($poin <= 25) {
    $statusColor = "text-red-600";
    $bgColor = "bg-red-50";
    $barColor = "bg-red-500";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | Sistem Pelanggaran</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>/src/public/assets/img/logo_sekolah.png" type="image/x-icon">
    <?php require_once BASE_PATH . '/layout/layout.php'; ?>
</head>

<body class="h-screen bg-zinc-50 overflow-x-hidden">
    <?php require_once BASE_PATH . '/includes/ui/alert/alert.php'; ?>
    <?php require_once BASE_PATH . '/includes/ui/header/header.php'; ?>
    <main class="container mx-auto p-6 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-zinc-900 tracking-tight">Halo, <?= htmlspecialchars($currentUser['nama']) ?>! 👋</h1>
                <p class="text-zinc-500 text-sm font-medium">Tetaplah disiplin dan patuhi aturan sekolah.</p>
            </div>
            <div class="px-4 py-2 <?= $bgColor ?> rounded-xl border border-zinc-200">
                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Status Kedisiplinan</p>
                <p class="<?= $statusColor ?> font-bold text-sm uppercase"><?= $poin > 50 ? 'Sangat Baik' : ($poin > 25 ? 'Perlu Perhatian' : 'Bahaya') ?></p>
            </div>
        </div>



        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-1 space-y-4 sticky top-4">
                <div class="bg-white p-8 rounded-lg border border-zinc-200 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-1">Sisa Poin Anda</p>
                        <h2 class="text-6xl font-black text-zinc-900"><?= $poin ?></h2>

                        <div class="mt-6 w-full h-3 bg-zinc-100 rounded-full overflow-hidden">
                            <div class="h-full <?= $barColor ?> transition-all duration-1000" style="width: <?= $poin ?>%"></div>
                        </div>
                        <p class="mt-3 text-[10px] text-zinc-400 font-medium italic">*Batas minimum poin adalah 0 untuk dikembalikan ke ortu.</p>
                    </div>
                    <span class="icon-shield absolute -right-4 -bottom-4 w-32 h-32 text-zinc-50 opacity-50"></span>
                </div>

                <div class="bg-white p-6 rounded-lg border border-zinc-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
                    <div class="h-20 w-20 bg-zinc-900 rounded-2xl flex items-center justify-center text-white text-3xl font-black shrink-0 shadow-lg shadow-zinc-200">
                        <?= strtoupper(substr($siswa['nama_siswa'], 0, 1)) ?>
                    </div>

                    <div class="flex-1 flex flex-col gap-4 w-full">
                        <div class="space-y-1 text-center md:text-left">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Nama Lengkap</p>
                            <p class="text-sm font-bold text-zinc-900 leading-tight"><?= htmlspecialchars($siswa['nama_siswa']) ?></p>
                        </div>
                        <div class="space-y-1 text-center md:text-left  border-zinc-100 ">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">NIS / NISN</p>
                            <p class="text-sm font-bold text-zinc-900"><?= $siswa['nis'] ?> / <?= $siswa['nisn'] ?></p>
                        </div>
                        <div class="space-y-1 text-center md:text-left  border-zinc-100 ">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Kelas / Jurusan</p>
                            <p class="text-sm font-bold text-zinc-900"><?= $siswa['kelas'] ?> / <?= $siswa['jurusan'] ?></p>
                        </div>
                        <div class="space-y-1 text-center md:text-left  border-zinc-100 ">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Jenis Kelamin</p>
                            <p class="text-sm font-bold text-zinc-900"><?= ($siswa['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan' ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900 p-6 rounded-lg text-white">
                    <h3 class="font-bold text-sm mb-2 flex items-center gap-2">
                    <span class="icon-medal-reborn w-4 h-4 text-orange-600"></span> Tips Disiplin
                    </h3>
                    <p class="text-xs text-zinc-400 leading-relaxed">Poin Anda akan berkurang jika melakukan pelanggaran. Pastikan untuk selalu menaati tata tertib agar terhindar dari pemanggilan orang tua.</p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-zinc-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 flex justify-between items-center">
                        <h3 class="font-bold text-zinc-800">Riwayat Pelanggaran Terakhir</h3>
                        <span class="icon-history text-zinc-400 w-5 h-5"></span>
                    </div>

                    <div class="divide-y divide-zinc-50">
                        <?php if (empty($riwayat)): ?>
                            <div class="p-12 text-center">
                                <div class="bg-emerald-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
                                    <span class="icon-check-circle w-8 h-8"></span>
                                </div>
                                <p class="text-zinc-500 font-bold">Luar Biasa!</p>
                                <p class="text-xs text-zinc-400">Kamu belum memiliki catatan pelanggaran.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($riwayat as $r): ?>
                                <div class="p-5 flex items-center justify-between hover:bg-zinc-50 transition-all">
                                    <div class="flex gap-4 items-center">
                                        <div class="bg-red-50 p-3 rounded-full text-red-600">
                                            <span class="icon-siren w-5 h-5"></span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-zinc-900"><?= htmlspecialchars($r['nama_jenis']) ?></h4>
                                            <p class="text-xs text-zinc-400 font-medium"><?= date('d M Y', strtotime($r['tanggal_pelaporan'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-red-600 text-lg font-black ">-<?= $r['point_potong'] ?></p>
                                        <p class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Point</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 bg-zinc-50 text-center">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Kontak Guru BK Jika Ada Kekeliruan Data</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>