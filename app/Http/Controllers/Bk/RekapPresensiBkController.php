<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PresensiDetail;
use App\Models\BkKelas;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;

class RekapPresensiBkController extends Controller
{
public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil HANYA kelas binaan milik guru BK yang sedang login
        $kelases = BkKelas::with('kelas')
            ->where('bk_user_id', $user->id)
            ->get()
            ->pluck('kelas')
            ->filter();

        // Fallback: Jika belum ada kelas binaan spesifik, ambil semua kelas
        if ($kelases->isEmpty()) {
            $kelases = Kelas::all();
        }

        $selectedKelas   = $request->input('kelas_id', $kelases->first()->id ?? null);
        $selectedTanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $rekapHarian       = [];
        $rekapSemester     = [];
        $selectedKelasData = null;

        if ($selectedKelas) {
            // Tarik data kelas beserta relasi wali kelas untuk info card di view
            $selectedKelasData = Kelas::with('waliKelas')->find($selectedKelas);

            $siswas = Siswa::where('kelas_id', $selectedKelas)->orderBy('nama', 'asc')->get();

            foreach ($siswas as $siswa) {
                // A. Olah Data Rekap Harian (Status Kehadiran Hari Terpilih)
                $detailsHarian = PresensiDetail::where('siswa_id', $siswa->id)
                    ->whereHas('presensi', function ($q) use ($selectedKelas, $selectedTanggal) {
                        $q->where('kelas_id', $selectedKelas)
                          ->whereDate('tanggal', $selectedTanggal);
                    })->get();

                $statusHarian = 'Hadir';
                if ($detailsHarian->where('status', 'alpa')->isNotEmpty()) {
                    $statusHarian = 'Alpha';
                } elseif ($detailsHarian->where('status', 'sakit')->isNotEmpty()) {
                    $statusHarian = 'Sakit';
                } elseif ($detailsHarian->where('status', 'izin')->isNotEmpty()) {
                    $statusHarian = 'Izin';
                } elseif ($detailsHarian->isEmpty()) {
                    $statusHarian = 'Belum Diabsen';
                }

                $rekapHarian[] = (object) [
                    'nis'    => $siswa->nis ?? $siswa->nisn ?? '-',
                    'nama'   => $siswa->nama,
                    'status' => $statusHarian
                ];

                // B. Olah Data Akumulasi Per Semester
                $allDetails = PresensiDetail::where('siswa_id', $siswa->id)
                    ->whereHas('presensi', function ($q) use ($selectedKelas) {
                        $q->where('kelas_id', $selectedKelas);
                    })->get();

                $rekapSemester[] = (object) [
                    'nis'   => $siswa->nis ?? $siswa->nisn ?? '-',
                    'nama'  => $siswa->nama,
                    'hadir' => $allDetails->where('status', 'hadir')->count(),
                    'sakit' => $allDetails->where('status', 'sakit')->count(),
                    'izin'  => $allDetails->where('status', 'izin')->count(),
                    'alpha' => $allDetails->where('status', 'alpa')->count(),
                ];
            }
        }

        return view('admin.bk.rekap_presensi.index', compact(
            'kelases', 'selectedKelas', 'selectedTanggal', 'rekapHarian', 'rekapSemester', 'selectedKelasData'
        ));
    }
    public function exportPdf(Request $request)
        {
            $kelasId = $request->input('kelas_id');
            $kelas   = Kelas::findOrFail($kelasId);

            $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('nama', 'asc')->get();
            $rekapSemester = [];

            foreach ($siswas as $siswa) {
                $allDetails = PresensiDetail::where('siswa_id', $siswa->id)
                    ->whereHas('presensi', function ($q) use ($kelasId) {
                        $q->where('kelas_id', $kelasId);
                    })->get();

                $rekapSemester[] = (object) [
                    'nis'   => $siswa->nis ?? $siswa->nisn ?? '-',
                    'nama'  => $siswa->nama,
                    'hadir' => $allDetails->where('status', 'hadir')->count(),
                    'sakit' => $allDetails->where('status', 'sakit')->count(),
                    'izin'  => $allDetails->where('status', 'izin')->count(),
                    'alpha' => $allDetails->where('status', 'alpa')->count(),
                ];
            }

            // Load view khusus cetak PDF
            $pdf = Pdf::loadView('admin.bk.rekap_presensi.pdf', compact('kelas', 'rekapSemester'));
            
            // Download file PDF dengan nama otomatis
            return $pdf->download('Rekap_Presensi_BK_' . str_replace(' ', '_', $kelas->nama_kelas) . '.pdf');
        }
}