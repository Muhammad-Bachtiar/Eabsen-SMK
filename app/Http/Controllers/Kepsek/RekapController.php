<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $kelases = Kelas::with('jurusan')->orderBy('nama_kelas', 'asc')->get();
        $rekapKelas = [];

        foreach ($kelases as $kelas) {
            $presensiIds = Presensi::where('kelas_id', $kelas->id)
                ->whereDate('tanggal', $tanggal)
                ->pluck('id');

            $sudahDiisi = $presensiIds->isNotEmpty();
            $hadir = 0; $izin = 0; $sakit = 0; $alpha = 0; $totalTerdata = 0;

            if ($sudahDiisi) {
                $details = PresensiDetail::whereIn('presensi_id', $presensiIds)->get();
                $groupedBySiswa = $details->groupBy('siswa_id');

                foreach ($groupedBySiswa as $siswaId => $siswaDetails) {
                    $statuses = $siswaDetails->pluck('status')->map(fn($s) => strtolower($s));

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

        return view('admin.kepsek.rekap_presensi.index', compact('tanggal', 'rekapKelas'));
    }
}