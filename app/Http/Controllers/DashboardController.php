<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\BkKelas;
use App\Models\JamPelajaran;
use App\Models\JenisPelanggaran;
use App\Models\Presensi;
use App\Models\PelanggaranSiswa;

class DashboardController extends Controller
{
    /**
     * Router dashboard berdasarkan role.
     * Dipakai oleh route umum 'dashboard' (target redirect setelah login).
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->nama_role ?? 'admin';

        return match ($role) {
            'guru'           => $this->guru(),
            'bk'             => $this->bk(),
            'waka_kesiswaan' => $this->waka(),
            'kepala_sekolah' => $this->kepalaSekolah(),
            default          => $this->admin(),
        };
    }

    /**
     * Dashboard Admin - ringkasan statistik semua data.
     */
    public function admin()
    {
        $user = Auth::user();

        $totalSiswa = Siswa::count();
        $totalGuru = User::whereHas('role', fn($q) => $q->where('nama_role', 'guru'))->count();
        $totalKelas = Kelas::count();
        $totalMapel = MataPelajaran::count();
        $totalPenugasan = GuruMapelKelas::count();
        $totalPenugasanBk = BkKelas::count();
        $totalJamPelajaran = JamPelajaran::count();
        $totalJenisPelanggaran = JenisPelanggaran::count();

        $genderLaki = Siswa::where('jenis_kelamin', 'L')->count();
        $genderPerempuan = Siswa::where('jenis_kelamin', 'P')->count();

        $totalPresensi = Presensi::count();
        $totalPelanggaran = PelanggaranSiswa::count();

        $siswasTerbaru = Siswa::with('kelas')->latest()->take(5)->get();
        $gurusTerbaru = User::whereHas('role', fn($q) => $q->where('nama_role', 'guru'))->latest()->take(5)->get();
        $penugasansTerbaru = GuruMapelKelas::with(['guru', 'kelas', 'mapel'])->latest()->take(5)->get();
        $jamsList = JamPelajaran::orderBy('jam_ke', 'asc')->get();

        return view('admin.dashboard', compact(
            'user',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'totalPenugasan',
            'totalPenugasanBk',
            'totalJamPelajaran',
            'totalJenisPelanggaran',
            'genderLaki',
            'genderPerempuan',
            'totalPresensi',
            'totalPelanggaran',
            'siswasTerbaru',
            'gurusTerbaru',
            'penugasansTerbaru',
            'jamsList'
        ));
    }

    /**
     * Dashboard Guru.
     */
    public function guru()
    {
        $user = Auth::user();
        return view('admin.guru.dashboard', compact('user'));
    }

    /**
     * Dashboard BK / Koordinator BK.
     * Koordinator BK mendapat dashboard khusus.
     */
    public function bk()
    {
        $user = Auth::user();

        if ($user->is_koordinator_bk) {
            return view('admin.koordinator-bk.dashboard', compact('user'));
        }

        return view('admin.bk.dashboard', compact('user'));
    }

    /**
     * Dashboard Koordinator BK (explicit).
     */
    public function koordinatorBk()
    {
        $user = Auth::user();
        return view('admin.koordinator-bk.dashboard', compact('user'));
    }

    /**
     * Dashboard Waka Kesiswaan.
     */
    public function waka()
    {
        $user = Auth::user();
        return view('admin.waka.dashboard', compact('user'));
    }

    /**
     * Dashboard Kepala Sekolah.
     */
    public function kepalaSekolah()
    {
        $user = Auth::user();
        return view('admin.kepsek.dashboard', compact('user'));
    }
}
