<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| RUTE AUTENTIKASI UTAMA (JANGAN DIHAPUS)
|--------------------------------------------------------------------------
*/

// Rute untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// Rute yang butuh autentikasi dasar (harus login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| GRUP RUTE BERDASARKAN ROLE (HAK AKSES)
|--------------------------------------------------------------------------
*/

// --- GRUP RUTE ADMIN ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Rute Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute Master Data Mata Pelajaran
    Route::resource('mapel', \App\Http\Controllers\Admin\MataPelajaranController::class);
    
    // Rute Master Data Kelas
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);
    
    // Rute Tambahan Import & Template Siswa
    Route::get('siswa/template', [\App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])->name('siswa.download-template');
    Route::post('siswa/import', [\App\Http\Controllers\Admin\SiswaController::class, 'import'])->name('siswa.import');
    // Rute Master Data Siswa
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class);
    
    // Rute Import & Template Guru
    Route::get('guru/template', [\App\Http\Controllers\Admin\GuruController::class, 'downloadTemplate'])->name('guru.download-template');
    Route::post('guru/import', [\App\Http\Controllers\Admin\GuruController::class, 'import'])->name('guru.import');
    // Rute Master Data Guru
    Route::resource('guru', \App\Http\Controllers\Admin\GuruController::class);
    
    // Rute Master Penugasan Guru
    Route::resource('penugasan', \App\Http\Controllers\Admin\GuruMapelKelasController::class);
    
    // Rute Master Jam Pelajaran
    Route::resource('jam-pelajaran', \App\Http\Controllers\Admin\JamPelajaranController::class);
    
    // Rute Master Jenis Pelanggaran
    Route::resource('jenis-pelanggaran', \App\Http\Controllers\Admin\JenisPelanggaranController::class);
    
    // Rute Master Penugasan BK
    Route::resource('penugasan-bk', \App\Http\Controllers\Admin\BkKelasController::class);
});

// --- GRUP RUTE GURU ---
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Rute input presensi masuk ke sini
    Route::get('presensi', [\App\Http\Controllers\Guru\PresensiController::class, 'index'])->name('presensi.index');
    
    // Rute Presensi (Absensi) Guru
    Route::get('presensi/create', [\App\Http\Controllers\Guru\PresensiController::class, 'create'])->name('presensi.create');
    Route::post('presensi/store', [\App\Http\Controllers\Guru\PresensiController::class, 'store'])->name('presensi.store');
    // Rute AJAX untuk menyedot data siswa
    Route::get('presensi/get-siswa/{kelas_id}', [\App\Http\Controllers\Guru\PresensiController::class, 'getSiswa'])->name('presensi.get-siswa');
    Route::get('presensi/{id}', [\App\Http\Controllers\Guru\PresensiController::class, 'show'])->name('presensi.show');

});
// --- GRUP RUTE KOORDINATOR BK ---
Route::middleware(['auth', 'role:koordinator_bk'])->prefix('koordinator-bk')->name('koordinator-bk.')->group(function () {

    // Rute Master Data Jenis Pelanggaran
    Route::resource('jenis-pelanggaran', \App\Http\Controllers\KoordinatorBk\JenisPelanggaranController::class);

});

// --- GRUP RUTE BK ---
Route::middleware(['auth', 'role:bk'])->prefix('bk')->name('bk.')->group(function () {
    // Nanti rute catat pelanggaran masuk ke sini
    
    // Rute Dashboard BK (yang barusan kita benerin)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'bk'])->name('dashboard');

    // Rute Paket Komplit CRUD Pelanggaran
    Route::resource('pelanggaran', \App\Http\Controllers\Bk\PelanggaranController::class);
    
});

// --- GRUP RUTE WAKA KESISWAAN ---
Route::middleware(['auth', 'role:waka_kesiswaan'])->prefix('waka')->name('waka.')->group(function () {
    // Nanti rute persetujuan (approval) pelanggaran masuk ke sini
});

// --- GRUP RUTE KEPALA SEKOLAH ---
Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    // Nanti rute rekap laporan akhir masuk ke sini
});