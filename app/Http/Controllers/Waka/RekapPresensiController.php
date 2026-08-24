<?php
namespace App\Http\Controllers\Waka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use Carbon\Carbon;

class RekapPresensiController extends Controller
{
public function index(Request $request)
    {
        $selectedTanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Ambil semua kelas beserta relasi jurusannya
        $kelases = Kelas::with('jurusan')->orderBy('nama_kelas', 'asc')->get();

        $rekapKelas = [];

        foreach ($kelases as $kelas) {
            // 1. Cek apakah ada sesi presensi (Mapel/BK) pada kelas dan tanggal ini
            $presensiIds = Presensi::where('kelas_id', $kelas->id)
                ->whereDate('tanggal', $selectedTanggal)
                ->pluck('id');

            $sudahDiisi = $presensiIds->isNotEmpty();

            // 2. Jika presensi sudah diisi, hitung konsolidasi status unik per siswa
            $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0; $totalTerdata = 0;

            if ($sudahDiisi) {
                // Ambil semua detail presensi siswa pada sesi-sesi kelas & tanggal tersebut
                $details = PresensiDetail::whereIn('presensi_id', $presensiIds)->get();

                // Kelompokkan per siswa agar 1 siswa hanya terhitung 1 status harian
                $groupedBySiswa = $details->groupBy('siswa_id');

                foreach ($groupedBySiswa as $siswaId => $siswaDetails) {
                    $statuses = $siswaDetails->pluck('status')->map(fn($s) => strtolower($s));

                    // Prioritas penentuan status harian siswa: Alpha > Sakit > Izin > Hadir
                    if ($statuses->contains('alpa')) {
                        $alpha++;
                    } elseif ($statuses->contains('sakit')) {
                        $sakit++;
                    } elseif ($statuses->contains('izin')) {
                        $izin++;
                    } else {
                        $hadir++;
                    }
                }

                $totalTerdata = $groupedBySiswa->count();
            }

            $rekapKelas[] = (object) [
                'kelas_id'      => $kelas->id,
                'nama_kelas'    => $kelas->nama_kelas,
                'nama_jurusan'  => $kelas->jurusan->nama_jurusan ?? '-',
                'sudah_diisi'   => $sudahDiisi,
                'hadir'         => $hadir,
                'izin'          => $izin,
                'sakit'         => $sakit,
                'alpha'         => $alpha,
                'total_terdata' => $totalTerdata
            ];
        }

        return view('admin.waka.rekap_presensi.index', compact('selectedTanggal', 'rekapKelas'));
    }
}