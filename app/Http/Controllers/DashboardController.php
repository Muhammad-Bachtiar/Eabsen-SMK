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
use App\Models\PresensiJam;
use App\Models\PresensiDetail;
use App\Models\PelanggaranSiswa;
use Carbon\Carbon;

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
 public function guru(Request $request)
    {
        $user = Auth::user();

        // ============================================================
        // 1. Data untuk form presensi (dipakai dashboard.blade.php)
        // ============================================================
        $kelases       = Kelas::orderBy('nama_kelas', 'asc')->get();
        $mapels        = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        $jamPelajarans = JamPelajaran::orderBy('jam_ke', 'asc')->get();

        // Default filter dari query string (kalau ada)
        $tanggal  = $request->input('tanggal', date('Y-m-d'));
        $kelas_id = $request->input('kelas_id');
        $mapel_id = $request->input('mapel_id');

        // ============================================================
        // 2. Data presensi hari ini (opsional, untuk ringkasan dashboard)
        // ============================================================
        $presensiHariIni = Presensi::with(['kelas', 'mapel', 'pencatat'])
            ->where('dicatat_oleh', $user->id)
            ->whereDate('tanggal', date('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        // ============================================================
        // 3. Data siswa (kalau dashboard butuh daftar siswa)
        //    Sesuaikan dengan kebutuhan view Anda
        // ============================================================
        $siswas = Siswa::orderBy('nama', 'asc')->limit(0)->get();
        // ↑ limit(0) supaya kosong kalau memang tidak dipakai.
        //   Kalau butuh semua, hapus ->limit(0).

        // ============================================================
        // 4. Proses presensi_id dari redirect setelah submit
        //    → supaya accordion auto-open di jam yang baru diisi
        // ============================================================
        $presensiId = $request->input('presensi_id');
        $jamYangBaruDiisi = [];

        if ($presensiId) {
            $jamYangBaruDiisi = PresensiJam::where('presensi_id', $presensiId)
                ->pluck('jam_pelajaran_id')
                ->toArray();
        }

        // ============================================================
        // 5. Kirim ke view — PASTIKAN semua variabel yang di-compact
        //    sudah didefinisikan di atas.
        // ============================================================
        return view('admin.guru.dashboard', compact(
            'user',
            'kelases',
            'mapels',
            'jamPelajarans',
            'tanggal',
            'kelas_id',
            'mapel_id',
            'presensiHariIni',
            'siswas',
            'presensiId',
            'jamYangBaruDiisi'
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
        $today = Carbon::today()->toDateString();

        // 1. Stat Cards
        $totalKelas  = Kelas::count();
        $totalSiswa  = Siswa::count();
        
        // Kelas yang sudah diabsensi hari ini
        $kelasAbsenHariIni = Presensi::whereDate('tanggal', $today)
            ->distinct('kelas_id')
            ->count('kelas_id');

        // Pelanggaran yang butuh persetujuan Waka
        $pelanggaranPending = PelanggaranSiswa::where('status', 'menunggu_persetujuan')->count();

        // 2. Ringkasan Presensi Harian Per Kelas (Untuk Widget Dashboard)
        $kelases = Kelas::with('jurusan')->get();
        $rekapPresensi = [];

        foreach ($kelases as $k) {
            $presensiIds = Presensi::where('kelas_id', $k->id)
                ->whereDate('tanggal', $today)
                ->pluck('id');

            $sudahDiisi = $presensiIds->isNotEmpty();
            $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0;

            if ($sudahDiisi) {
                $details = PresensiDetail::whereIn('presensi_id', $presensiIds)->get();
                $grouped = $details->groupBy('siswa_id');

                foreach ($grouped as $siswaDetails) {
                    $statuses = $siswaDetails->pluck('status')->map(fn($s) => strtolower($s));
                    if ($statuses->contains('alpa')) $alpha++;
                    elseif ($statuses->contains('sakit')) $sakit++;
                    elseif ($statuses->contains('izin')) $izin++;
                    else $hadir++;
                }
            }

            $rekapPresensi[] = (object) [
                'nama_kelas'   => $k->nama_kelas,
                'nama_jurusan' => $k->jurusan->nama_jurusan ?? '-',
                'sudah_diisi'  => $sudahDiisi,
                'hadir'        => $hadir,
                'izin'         => $izin,
                'sakit'        => $sakit,
                'alpha'        => $alpha,
            ];
        }

        return view('admin.waka.dashboard', compact(
            'totalKelas', 'totalSiswa', 'kelasAbsenHariIni', 'pelanggaranPending', 'rekapPresensi', 'today'
        ));
    }

    /**
     * Dashboard Kepala Sekolah.
     */
public function kepalaSekolah()
    {
        $today = Carbon::today()->toDateString();

        // 1. Stat Cards Eksekutif
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();

        // Ringkasan Total Absensi Hari Ini Seluruh Sekolah
        $presensiHariIniIds = Presensi::whereDate('tanggal', $today)->pluck('id');
        $detailsHariIni = PresensiDetail::whereIn('presensi_id', $presensiHariIniIds)->get();
        $groupedSiswa = $detailsHariIni->groupBy('siswa_id');

        $totalHadir = 0; $totalIzin = 0; $totalSakit = 0; $totalAlpha = 0;
        foreach ($groupedSiswa as $siswaDetails) {
            $statuses = $siswaDetails->pluck('status')->map(fn($s) => strtolower($s));
            if ($statuses->contains('alpa')) $totalAlpha++;
            elseif ($statuses->contains('sakit')) $totalSakit++;
            elseif ($statuses->contains('izin')) $totalIzin++;
            else $totalHadir++;
        }

        // Total Pelanggaran Terdaftar Bulan Ini
        $pelanggaranBulanIni = PelanggaranSiswa::whereMonth('created_at', Carbon::now()->month)->count();

        // 2. Daftar Kelas yang Belum Diabsen Hari Ini (Perhatian Kepsek)
        $kelasSudahAbsenIds = Presensi::whereDate('tanggal', $today)->pluck('kelas_id')->unique();
        $kelasBelumAbsen    = Kelas::whereNotIn('id', $kelasSudahAbsenIds)->get();

        return view('admin.kepsek.dashboard', compact(
            'totalSiswa', 'totalKelas', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpha', 
            'pelanggaranBulanIni', 'kelasBelumAbsen', 'today'
        ));
    }
}
