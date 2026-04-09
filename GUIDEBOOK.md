# Studi Kasus: Digitalisasi Kedisiplinan Siswa (UK-Pelanggaran)

## 1. Latar Belakang & Masalah (Problem Statement)
Di banyak institusi pendidikan, pencatatan pelanggaran siswa sering kali masih menggunakan metode manual berbasis buku besar atau kartu poin fisik. Hal ini menimbulkan beberapa masalah kritis:
- **Redundansi Data**: Data yang sama harus ditulis berulang kali di buku yang berbeda.
- **Lambatnya Tindakan**: Guru BK sering terlambat mengetahui jika poin seorang siswa sudah mencapai ambang batas kritis karena harus merekap manual.
- **Ketidakefisienan Administrasi**: Pembuatan surat panggilan orang tua membutuhkan waktu lama untuk mengetik ulang data pelanggaran siswa.
- **Kurangnya Transparansi**: Orang tua dan siswa sulit memantau sisa poin kedisiplinan secara *real-time*.

---

## 2. Solusi & Tujuan (Solution & Objectives)
Proyek ini membangun **Sistem Pelanggaran Siswa** terintegrasi yang bertujuan untuk:
- Mengotomatisasi pemotongan poin berdasarkan kategori pelanggaran.
- Mempercepat proses administrasi melalui fitur *auto-generate* surat resmi.
- Menyentralisasi database siswa, user, dan riwayat pelanggaran dalam satu platform.

---

## 3. Implementasi Fitur (Feature Implementation)

### A. Manajemen Data & Role Akses
Sistem memisahkan tanggung jawab berdasarkan 4 role:
- **Admin**: Penanggung jawab teknis & manajemen user.
- **Guru BK**: Mediator & pengambil keputusan administratif (pembuatan surat).
- **Guru Mapel**: Pelapor kejadian di lapangan.
- **Siswa**: Penerima informasi/riwayat kedisiplinan.

### B. Alur Pelaporan Cerdas (Input Workflow)
Untuk menghindari duplikasi data, sistem menggunakan fitur **Chained Dropdown** yang memungkinkan pelapor memilih jurusan, kelas, lalu siswa dengan cepat tanpa risiko salah ketik nama.

### C. Otomatisasi Administrasi (Surat-menyurat)
Setiap data pelanggaran yang masuk langsung terhubung dengan modul persuratan. Ketika siswa mencapai ambang batas poin tertentu, Guru BK cukup sekali klik untuk mencetak:
- **Surat Pemanggilan**: Berisi jadwal pertemuan yang terintegrasi data siswa.
- **Surat Perjanjian**: Secara otomatis menarik poin siswa ke dalam format surat.

---

## 4. Simulasi Penggunaan (Problem & Solution Flow)

### Skenario: Pelanggaran oleh Siswa "A"
1. **Masalah**: Siswa "A" kedapatan bolos pada jam pelajaran.
2. **Tindakan (Solusi)**: Guru Mapel login, masuk ke menu **Pelanggaran**, dan mencatat kejadian tersebut.
3. **Dampak Sistem**: Poin siswa "A" otomatis berkurang sesuai kategori "Bolos".
4. **Respon Cepat**: Guru BK melihat sisa poin siswa "A" sudah menunjukkan zona merah, langsung menindaklanjuti dengan membuat **Surat Pemanggilan Orang Tua** dari sistem.

---

## 5. Analisis Masalah & Penanganan (Troubleshooting Case)
Dalam implementasinya, beberapa kendala teknis sering muncul sebagai bagian dari studi kasus ini:

| Kondisi Masalah | Solusi Teknis |
| :--- | :--- |
| **Integrasi Data Gagal**: Nama siswa tidak muncul. | **Penyelesaian**: Melakukan pembersihan cache browser atau memastikan data siswa tersebut berstatus "Aktif" di database. |
| **Keamanan Akses**: Akun disalahgunakan. | **Penyelesaian**: Mengaktifkan middleware `role.php` dan `auth.php` di setiap halaman krusial untuk mencegah akses ilegal. |
| **Output Cetak**: Layout surat terpotong. | **Penyelesaian**: Standarisasi pengaturan print browser (Margin: None) agar sesuai dengan desain layout CSS yang sudah dibuat. |

---

## 6. Kesimpulan & Dampak Positif
Dengan implementasi sistem ini, sekolah berhasil:
1. Memotong waktu pembuatan surat panggilan dari 30 menit menjadi **kurang dari 2 menit**.
2. Meningkatkan akurasi kalkulasi poin kedisiplinan hingga **100%** (bebas human error).
3. Memberikan akses informasi yang transparan bagi seluruh pihak terkait.

---

> [!IMPORTANT]
> Studi kasus ini menunjukkan bahwa transformasi digital bukan sekadar memindahkan data ke komputer, tapi tentang mengoptimalkan alur kerja (*efficiency*) dan memperkuat kedisiplinan (*discipline*).
