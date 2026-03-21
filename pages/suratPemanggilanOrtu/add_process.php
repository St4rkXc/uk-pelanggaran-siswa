<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once BASE_PATH . '/middleware/auth.php';
$requiredRole = ['admin', 'guru_bk'];

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'guru_bk') {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_siswa = $_POST['id_siswa'] ?? null;
    $nomor_surat = $_POST['nomor_surat'] ?? null;
    $keperluan = $_POST['keperluan'] ?? ''; 
    $tanggal_temu = $_POST['tanggal_temu'] ?? null;
    $tanggal_surat = date('Y-m-d'); // Tanggal surat terbit hari ini

    if (!$id_siswa || !$nomor_surat || !$tanggal_temu) {
        die("Data tidak lengkap, bro!");
    }

    try {
        // MULAI TRANSAKSI
        $pdo->beginTransaction();

        // 1. Insert ke tabel induk 'surat'
        // Kita perlu ambil ID yang baru saja dibuat untuk tabel detail
        $sqlSurat = "INSERT INTO surat (id_jenis_surat, jenis_surat, nomor_surat) 
                     VALUES (:id_jenis, :jenis_surat, :nomor)";

        // Kita butuh ID unik buat id_jenis_surat. 
        // Tip: Karena ini tabel detail baru, kita bisa pake ID dari tabel detail nanti.
        // Tapi cara paling aman adalah insert detail dulu atau pake dummy ID sementara.
        // Di sini kita asumsikan id_jenis_surat adalah ID dari tabel surat_panggilan_ortu.

        // 2. Insert ke tabel detail 'surat_panggilan_ortu' dulu buat dapet ID-nya
        $sqlDetail = "INSERT INTO surat_panggilan_ortu (id_siswa, keperluan, tanggal_temu, tanggal_surat) 
                      VALUES (:id_siswa, :keperluan, :tanggal_temu, :tanggal_surat)";

        $stmtDetail = $pdo->prepare($sqlDetail);
        $stmtDetail->execute([
            ':id_siswa' => $id_siswa,
            ':keperluan' => $keperluan,
            ':tanggal_temu' => $tanggal_temu,
            ':tanggal_surat' => $tanggal_surat
        ]);

        // Ambil ID detail yang barusan di-insert
        $lastDetailId = $pdo->lastInsertId();

        // 3. Sekarang baru masukkan ke tabel induk 'surat' pake ID detail tadi
        $stmtSurat = $pdo->prepare($sqlSurat);
        $stmtSurat->execute([
            ':id_jenis' => $lastDetailId,
            ':jenis_surat' => 'surat_panggilan_ortu',
            ':nomor' => $nomor_surat
        ]);

        // COMMIT SEMUA TRANSAKSI
        $pdo->commit();

        // Redirect balik ke halaman index dengan pesan sukses
        $_SESSION['success'] = "Surat Panggilan Ortu berhasil diterbitkan!";
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        // JIKA GAGAL, BATALKAN SEMUA (ROLLBACK)
        $pdo->rollBack();
        die("Waduh, gagal simpan data: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
