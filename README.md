# E-Absen SMK - Sistem Manajemen Presensi & Akademik

Sistem Informasi Presensi dan Manajemen Akademik berbasis Laravel yang dirancang untuk menangani penginputan presensi harian, konsolidasi rekapitulasi tingkat BK dan Waka Kesiswaan, serta manajemen bimbingan konseling secara terintegrasi.

---

## 🚀 Fitur Utama

- **Multi-Role Access Control**: 
  - **Admin**: Pengelolaan master data (Siswa, Staf Pengajar, Mapel, Kelas, Penugasan Mengajar & BK, serta Jam Pelajaran).
  - **Guru Mapel**: Input presensi jam pelajaran harian dan pemantauan rekap absensi per kelas.
  - **Guru BK**: Pemantauan presensi harian kelas binaan, akumulasi semester, deteksi otomatis siswa perlu bimbingan, serta export laporan PDF.
  - **Waka Kesiswaan**: Rekapitulasi presensi harian konsolidasi seluruh kelas/jurusan dan approval pelanggaran siswa.
  - **Kepala Sekolah**: Executive monitoring & laporan presensi read-only.

- **Manajemen Data & Presensi**:
  - Batch import data Guru, BK, dan Siswa via file Excel.
  - Konsolidasi status absensi otomatis (Prioritas: Alpha > Sakit > Izin > Hadir).
  - Cetak Rekap Presensi Semester dalam format PDF lengkap dengan verifikasi Wali Kelas & BK.

---

## 🛠️ Penggunaan Library & Paket Utama

- **[Laravel Framework](https://laravel.com/)** - Core Web Framework.
- **[Spatie Simple Excel](https://github.com/spatie/simple-excel)** - Import & Read data massal via Excel/CSV.
- **[Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)** - Generate & Export dokumen PDF.

---

## 💻 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP >= 8.1
- Composer
- Database Engine (MySQL / MariaDB)

### 2. Langkah-Langkah Instalasi
1. **Clone Repositori**
   ```bash
   git clone <URL_REPOSITORI_ANDA>
   cd e-absen-smk