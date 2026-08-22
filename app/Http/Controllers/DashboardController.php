<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Role;
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
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->nama_role ?? 'admin';

        // Melempar ke view dasbor masing-masing sesuai role
        switch ($role) {
            case 'admin':
                // Mengumpulkan ringkasan data statistik untuk Dashboard Admin
                $totalSiswa = Siswa::count();
                $totalGuru = User::whereHas('role', fn($q) => $q->where('nama_role', 'guru'))->count();
                $totalKelas = Kelas::count();
                $totalMapel = MataPelajaran::count();
                $totalPenugasan = GuruMapelKelas::count();
                $totalPenugasanBk = BkKelas::count();
                $totalJamPelajaran = JamPelajaran::count();
                $totalJenisPelanggaran = JenisPelanggaran::count();

                // Komposisi Gender Siswa
                $genderLaki = Siswa::where('jenis_kelamin', 'L')->count();
                $genderPerempuan = Siswa::where('jenis_kelamin', 'P')->count();

                // Presensi & Pelanggaran
                $totalPresensi = Presensi::count();
                $totalPelanggaran = PelanggaranSiswa::count();

                // Data Terbaru
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

            case 'guru':
                return view('guru.dashboard', compact('user'));

            case 'bk':
                return view('bk.dashboard', compact('user'));

            case 'waka_kesiswaan':
                return view('waka.dashboard', compact('user'));

            case 'kepala_sekolah':
                return view('kepsek.dashboard', compact('user'));

            case 'koordinator_bk':
                return view('koordinator-bk.dashboard', compact('user'));

            default:
                abort(403, 'Role tidak dikenali.');
        }
    }

    public function bk()
    {
        $user = Auth::user();
        return view('bk.dashboard', compact('user'));
    }
}
