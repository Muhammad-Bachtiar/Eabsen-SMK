# Bug & Miss Code — Eabsen SMK Syafi'i Akrom

Review dilakukan terhadap **Controller & Migration** dengan acuan `skema-eabsen-smk.pdf`.

## Ringkasan Status

| Area | Status |
|---|---|
| Migration | ✅ Sesuai skema (15 tabel), hanya saran penguatan |
| Controller | ⚠️ Ada bug kritis (role koordinator BK, route error, otorisasi hilang) dan banyak modul belum dibuat |

---

## A. Migration

### A.1 Sesuai skema ✓
Semua kolom & tipe pada 15 tabel sudah cocok dengan skema:
`roles`, `users`, `jurusans`, `kelas`, `siswas`, `mata_pelajarans`, `jam_pelajarans`, `guru_mapel_kelas`, `presensis`, `presensi_jams`, `presensi_details`, `bk_kelas`, `jenis_pelanggarans`, `pelanggaran_siswas`, `tindak_lanjut_pelanggarans`.

### A.2 Catatan minor
- `tindak_lanjut_pelanggarans` menambah kolom `updated_at` — skema hanya `created_at`. Tidak bermasalah, tapi tidak sesuai skema sepenuhnya.

### A.3 Rekomendasi penguatan (opsional)
- Tambah unique constraint agar duplikasi dicegah di level DB:
  - `guru_mapel_kelas (guru_id, kelas_id, mapel_id)`
  - `presensi_jams (presensi_id, jam_pelajaran_id)`
  - `bk_kelas (bk_user_id, kelas_id)`
- `presensis.mapel_id` bisa ditambahkan conditional check via aplikasi (wajib jika `jenis = mapel`), karena DB tidak bisa memvalidasi kondisi tsb.

---

## B. Controller — Bug Kritis

### B.1 Koordinator BK diperlakukan sebagai role, padahal skema §2 menyebut flag
- **File:** `routes/web.php:82`, `DashboardController.php:85`
- **Masalah:** Group route `koordinator-bk` memakai middleware `role:koordinator_bk`, tetapi role tsb **tidak ada** di tabel `roles` (seeder hanya 5 role: admin, guru, bk, waka_kesiswaan, kepala_sekolah). Akibatnya:
  - Semua route koordinator BK **selalu 403** (tidak pernah bisa diakses).
  - `case 'koordinator_bk'` di DashboardController tidak akan pernah tercapai.
- **Rekomendasi:** Buat middleware baru (mis. `koordinator_bk`) yang cek `role == bk && is_koordinator_bk == true`, lalu pakai di group route tsb.

### B.2 Route admin merujuk controller yang tidak ada
- **File:** `routes/web.php:62`
- **Masalah:** `Route::resource('jenis-pelanggaran', Admin\JenisPelanggaranController::class)` — controller tersebut **tidak ada** di `app/Http/Controllers/Admin/` → akan error `Class not found`.
- **Rekomendasi:** Sesuai skema §3.13 & §4.2, master jenis pelanggaran hanya dikelola Koordinator BK → hapus route ini dari grup admin.

### B.3 Modul Waka Kesiswaan belum ada sama sekali (skema §4.3)
- **File:** `routes/web.php:102-104` (group route `waka` kosong)
- **Missing controller:** belum ada controller untuk:
  - Rekap presensi harian semua kelas/jurusan.
  - Daftar pelanggaran status `menunggu_persetujuan`.
  - Setujui/tolak pelanggaran → update `status`, `disetujui_oleh`, `tanggal_persetujuan`, dan **tulis log ke `tindak_lanjut_pelanggarans`** (skema §5.C).
- **Rekomendasi:** Buat `Waka\RekapPresensiController` & `Waka\PersetujuanController` + route-nya.

### B.4 Modul Kepala Sekolah belum ada sama sekali (skema §4.4)
- **File:** `routes/web.php:107-109` (group route `kepsek` kosong)
- **Missing:** rekap presensi harian read-only seluruh kelas/jurusan + ringkasan pelanggaran (tanpa aksi).
- **Rekomendasi:** Buat `Kepsek\RekapController` + route-nya.

### B.5 Presensi BK (kelas binaan) belum ada (skema §4.2, alur B)
- **Missing controller:** belum ada input presensi untuk kelas binaan BK dengan `jenis = 'bk'` (tanpa mapel), plus rekap per kelas binaan.
- **Rekomendasi:** Buat `Bk\PresensiController` (index/create/store, filter kelas dari `bk_kelas`) + route-nya.

---

## C. Controller — Bug di Modul yang Sudah Ada

### C.1 Guru\PresensiController — otorisasi & validasi lemah
- **File:** `app/Http/Controllers/Guru/PresensiController.php:55-103` (store), `:45-53` (getSiswa)
- **Masalah:**
  1. Tidak cek apakah `kelas_id` + `mapel_id` ada di `guru_mapel_kelas` milik guru yang login → **guru bisa mengabsen kelas/mapel yang bukan diampunya** (skema §4.1 & §5.A).
  2. Validasi `kelas_id`/`mapel_id` tanpa `exists:...` (hanya `required`).
  3. `jam` tanpa `min:1` (bisa submit tanpa centang jam).
  4. `status.*` tanpa `in:hadir,izin,sakit,alpa,terlambat` → nilai invalid akan ditolak DB (rollback) tapi UX jelek.
  5. `getSiswa($kelas_id)` tanpa otorisasi — user role mana pun yang login bisa ambil data siswa kelas mana pun.
- **Rekomendasi:**
  - Sebelum simpan, validasi penugasan: `GuruMapelKelas::where('guru_id', Auth::id())->where('kelas_id', ...)->where('mapel_id', ...)->exists()`.
  - Tambah `exists` & `in:` rules + `jam' => 'required|array|min:1'`.
  - Batasi `getSiswa` hanya untuk kelas yang diampu (atau sekalian cek via middleware/authorization).

### C.2 Bk\PelanggaranController — alur tindak lanjut belum lengkap
- **File:** `app/Http/Controllers/Bk/PelanggaranController.php`
- **Masalah:**
  1. `store()` tidak menerima `rencana_tindak_lanjut` (skema §5.C step 2: BK isi rencana tindak lanjut lalu kirim ke Waka).
  2. Tidak ada method `update` / `ajukan` untuk mengisi atau merevisi rencana tindak lanjut (termasuk revisi setelah ditolak & ajukan ulang — skema §8.3).
  3. Tidak ada otorisasi kelas binaan — BK bisa mencatat pelanggaran siswa di luar `bk_kelas` miliknya (skema §4.2).
  4. `index()` menampilkan semua pelanggaran semua BK, tanpa filter `dicatat_oleh` / kelas binaan.
  5. Tidak ada penulisan log `tindak_lanjut_pelanggarans` saat status berubah (skema §3.15 log audit).
- **Rekomendasi:** Terima & simpan `rencana_tindak_lanjut` saat create; tambah method `update` (isi rencana & ajukan); filter siswa sesuai kelas binaan; catat log status.

### C.3 Admin\GuruController — tidak sesuai skema users
- **File:** `app/Http/Controllers/Admin/GuruController.php`
- **Masalah:**
  1. `nip_nik` diwajibkan (`required`) — skema §3.2 menyebut **nullable**.
  2. `jurusan_id` (homebase guru, skema §3.2) tidak pernah diisi/disimpan.
  3. `Role::where('nama_role','guru')->first()` tanpa guard → error kalau role belum di-seed (sama di `index()` & `create()`).
  4. Import guru tidak mengisi `jurusan_id` & `status_aktif`.
- **Rekomendasi:** `nip_nik` → nullable; tambah field `jurusan_id` di form; guard null role (seperti yang sudah dilakukan di `import()`).

### C.4 Admin\SiswaController — validasi kurang
- **File:** `app/Http/Controllers/Admin/SiswaController.php`
- **Masalah:** `jenis_kelamin` tanpa `in:L,P` di store/update; import tidak memvalidasi nilai `jenis_kelamin` → nilai invalid ditolak DB (catch) tapi pesan error mentah.
- **Rekomendasi:** tambah rule `in:L,P` dan validasi nilai pada import.

### C.5 Admin\JamPelajaranController — validasi waktu lemah
- **File:** `app/Http/Controllers/Admin/JamPelajaranController.php`
- **Masalah:** `waktu_mulai`/`waktu_selesai` hanya `required` tanpa `date_format:H:i` → bisa masuk format salah.
- **Rekomendasi:** tambah `date_format:H:i`.

### C.6 Admin\GuruMapelKelasController & BkKelasController — null risk role
- **File:** `app/Http/Controllers/Admin/GuruMapelKelasController.php:25-26`, `BkKelasController.php:23-26`
- **Masalah:** `Role::where('nama_role', ...)->first()` tanpa guard → crash jika role belum di-seed.
- **Rekomendasi:** guard null atau gunakan relasi role langsung.

### C.7 DashboardController
- **File:** `app/Http/Controllers/DashboardController.php:85`
- **Masalah:** `case 'koordinator_bk'` unreachable (lihat B.1). Method `bk()` duplikat dari `case 'bk'` — tidak salah, hanya redundan.
- **Rekomendasi:** sesuaikan setelah B.1 dibereskan.

### C.8 Duplikasi presensi tidak dicegah
- **File:** `app/Http/Controllers/Guru/PresensiController.php` (store)
- **Masalah:** submit 2x (double click / refresh) bisa membuat baris `presensis` ganda untuk tanggal+kelas+mapel+jam yang sama.
- **Rekomendasi:** cek keberadaan presensi serupa sebelum simpan, atau tambah unique di DB.

---

## D. Daftar Prioritas Perbaikan

| # | Item | Prioritas |
|---|---|---|
| 1 | Middleware koordinator BK (flag `is_koordinator_bk`) | Tinggi |
| 2 | Hapus route `admin.jenis-pelanggaran` yang merujuk controller tidak ada | Tinggi |
| 3 | Otorisasi & validasi `Guru\PresensiController::store` + `getSiswa` | Tinggi |
| 4 | Controller Waka Kesiswaan (rekap + approval + log tindak lanjut) | Tinggi |
| 5 | Controller BK presensi kelas binaan (`jenis = bk`) | Sedang |
| 6 | Controller Kepala Sekolah (rekap read-only) | Sedang |
| 7 | Lengkapi alur rencana tindak lanjut di `Bk\PelanggaranController` | Sedang |
| 8 | `Admin\GuruController`: nip_nik nullable + jurusan_id | Sedang |
| 9 | Validasi `SiswaController` (jenis_kelamin) & `JamPelajaranController` (format waktu) | Rendah |
| 10 | Unique constraint di migration | Rendah |

---

## E. Referensi Skema (file PDF)

- Peran & hak akses: §2, §4
- Skema database: §3.1–§3.16
- Alur presensi guru: §5.A, §8.1
- Alur presensi BK: §5.B
- Alur pelanggaran & approval: §5.C, §8.2, §8.3
- Koordinator BK sebagai flag: §7