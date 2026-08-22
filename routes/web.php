<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| RUTE AUTENTIKASI (TAMU)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

/*
|--------------------------------------------------------------------------
| RUTE YANG BUTUH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Route dashboard umum: wajib ada bernama 'dashboard' agar middleware 'guest'
    // punya tujuan redirect yang valid (mencegah redirect loop saat user sudah login).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE ADMIN
|--------------------------------------------------------------------------
| Master data dikelola admin. Menu role lain (guru/bk/koordinator-bk/waka/kepsek)
| di bawah sudah dibuka juga untuk admin lewat middleware role:admin,<role>.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Master Data
    Route::resource('mapel', \App\Http\Controllers\Admin\MataPelajaranController::class);
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);

    Route::get('siswa/template', [\App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])->name('siswa.download-template');
    Route::post('siswa/import', [\App\Http\Controllers\Admin\SiswaController::class, 'import'])->name('siswa.import');
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class);

    Route::get('guru/template', [\App\Http\Controllers\Admin\GuruController::class, 'downloadTemplate'])->name('guru.download-template');
    Route::post('guru/import', [\App\Http\Controllers\Admin\GuruController::class, 'import'])->name('guru.import');
    Route::resource('guru', \App\Http\Controllers\Admin\GuruController::class);

    Route::resource('penugasan', \App\Http\Controllers\Admin\GuruMapelKelasController::class);
    Route::resource('jam-pelajaran', \App\Http\Controllers\Admin\JamPelajaranController::class);
    Route::resource('penugasan-bk', \App\Http\Controllers\Admin\BkKelasController::class);
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE GURU (admin juga bisa akses)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');
    Route::get('presensi', [\App\Http\Controllers\Guru\PresensiController::class, 'index'])->name('presensi.index');
    Route::get('presensi/create', [\App\Http\Controllers\Guru\PresensiController::class, 'create'])->name('presensi.create');
    Route::post('presensi/store', [\App\Http\Controllers\Guru\PresensiController::class, 'store'])->name('presensi.store');
    Route::get('presensi/get-siswa/{kelas_id}', [\App\Http\Controllers\Guru\PresensiController::class, 'getSiswa'])->name('presensi.get-siswa');
    Route::get('presensi/{id}', [\App\Http\Controllers\Guru\PresensiController::class, 'show'])->name('presensi.show');
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE KOORDINATOR BK (admin juga bisa akses)
|--------------------------------------------------------------------------
| Koordinator BK bukan role terpisah (skema §2) — cukup role:bk.
| Pembeda is_koordinator_bk ditangani di dashboard & sidebar.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,bk'])->prefix('koordinator-bk')->name('koordinator-bk.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'koordinatorBk'])->name('dashboard');
    Route::resource('jenis-pelanggaran', \App\Http\Controllers\KoordinatorBk\JenisPelanggaranController::class);
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE BK (admin juga bisa akses)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,bk'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'bk'])->name('dashboard');
    Route::resource('pelanggaran', \App\Http\Controllers\Bk\PelanggaranController::class);
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE WAKA KESISWAAN (admin juga bisa akses)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,waka_kesiswaan'])->prefix('waka')->name('waka.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'waka'])->name('dashboard');
    // Nanti rute persetujuan (approval) pelanggaran masuk ke sini
});

/*
|--------------------------------------------------------------------------
| GRUP RUTE KEPALA SEKOLAH (admin juga bisa akses)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,kepala_sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kepalaSekolah'])->name('dashboard');
    // Nanti rute rekap laporan akhir masuk ke sini
});