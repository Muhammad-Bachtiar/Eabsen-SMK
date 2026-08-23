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

    // 1. Ambil daftar kelas & mapel yang diampu dari relasi guru_mapel_kelas
    // Asumsi model GuruMapelKelas atau query builder langsung ke tabel guru_mapel_kelas
    $mapelDiampu = \DB::table('guru_mapel_kelas')
        ->join('kelas', 'guru_mapel_kelas.kelas_id', '=', 'kelas.id')
        ->join('mata_pelajarans', 'guru_mapel_kelas.mapel_id', '=', 'mata_pelajarans.id')
        ->where('guru_mapel_kelas.guru_id', $user->id)
        ->select('kelas.nama_kelas', 'mata_pelajarans.nama_mapel', 'guru_mapel_kelas.kelas_id')
        ->get();

    // 2. Ambil riwayat presensi yang diinput oleh guru ini
    $riwayatPresensi = \App\Models\Presensi::with(['kelas', 'mapel'])
        ->where('dicatat_oleh', $user->id)
        ->orderBy('tanggal', 'desc')
        ->limit(5)
        ->get();

    // 3. Ringkasan angka statistik
    $totalKelas = $mapelDiampu->pluck('kelas_id')->unique()->count();
    $totalMapel = $mapelDiampu->pluck('nama_mapel')->unique()->count();
    $presensiHariIni = \App\Models\Presensi::where('dicatat_oleh', $user->id)
        ->whereDate('tanggal', now()->toDateString())
        ->count();

    return view('admin.guru.dashboard', compact(
        'user', 'mapelDiampu', 'riwayatPresensi', 'totalKelas', 'totalMapel', 'presensiHariIni'
    ));
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

        $kelasBinaan = \DB::table('bk_kelas')
        ->join('kelas', 'bk_kelas.kelas_id', '=', 'kelas.id')
        ->where('bk_kelas.bk_user_id', $user->id)
        ->select('kelas.id', 'kelas.nama_kelas')
        ->get();

    $totalSiswaBinaan = 0;
    if ($kelasBinaan->isNotEmpty()) {
        $totalSiswaBinaan = \App\Models\Siswa::whereIn('kelas_id', $kelasBinaan->pluck('id'))->count();
    }

    // 2. Ringkasan Statistik Pelanggaran
    $totalPelanggaran = \App\Models\PelanggaranSiswa::count();
    $kasusMenunggu    = \App\Models\PelanggaranSiswa::where('status', 'menunggu_persetujuan')->count();
    $kasusSelesai     = \App\Models\PelanggaranSiswa::where('status', 'disetujui')->count();

    // 3. 5 Pelanggaran Terbaru
    $pelanggaranTerbaru = \App\Models\PelanggaranSiswa::with(['siswa', 'jenisPelanggaran'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // 4. Top 5 Siswa Sering Alpha
    $topAlpha = \App\Models\PresensiDetail::with('siswa.kelas')
        ->where('status', 'Alpha')
        ->selectRaw('siswa_id, count(*) as total_alpha')
        ->groupBy('siswa_id')
        ->orderBy('total_alpha', 'desc')
        ->limit(5)
        ->get();

    return view('admin.bk.dashboard', compact(
        'user', 'kelasBinaan', 'totalSiswaBinaan', 'totalPelanggaran', 
        'kasusMenunggu', 'kasusSelesai', 'pelanggaranTerbaru', 'topAlpha'));
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
    $totalHadir = \App\Models\PresensiDetail::whereDate('created_at', now())->where('status', 'Hadir')->count();
    $totalIzin  = \App\Models\PresensiDetail::whereDate('created_at', now())->where('status', 'Izin')->count();
    $totalSakit = \App\Models\PresensiDetail::whereDate('created_at', now())->where('status', 'Sakit')->count();
    $totalAlpha = \App\Models\PresensiDetail::whereDate('created_at', now())->where('status', 'Alpha')->count();

    // Tarik ringkasan pelanggaran
    $totalPelanggaran = \App\Models\PelanggaranSiswa::where('status', 'disetujui')->count();
    $menungguApproval = \App\Models\PelanggaranSiswa::where('status', 'menunggu_persetujuan')->count();

    return view('admin.kepsek.dashboard', compact(
        'user', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpha', 'totalPelanggaran', 'menungguApproval'
    ));
}
}
