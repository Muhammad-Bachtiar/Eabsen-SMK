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
        // Ambil tanggal filter (default hari ini)
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Tarik semua kelas beserta jurusannya
        $kelases = Kelas::with('jurusan')->orderBy('nama_kelas', 'asc')->get();

        // Olah data rekap per kelas pada tanggal tersebut
        $rekapData = $kelases->map(function ($kelas) use ($tanggal) {
            // Cari presensi utama berdasarkan kelas & tanggal
            $presensi = Presensi::where('kelas_id', $kelas->id)
                                ->whereDate('tanggal', $tanggal)
                                ->first();

            if ($presensi) {
                $details = PresensiDetail::where('presensi_id', $presensi->id)->get();
                $hadir = $details->where('status', 'Hadir')->count();
                $izin  = $details->where('status', 'Izin')->count();
                $sakit = $details->where('status', 'Sakit')->count();
                $alpha = $details->where('status', 'Alpha')->count();
                $statusInput = 'Sudah Diisi';
            } else {
                $hadir = $izin = $sakit = $alpha = 0;
                $statusInput = 'Belum Diisi';
            }

            return [
                'kelas' => $kelas->nama_kelas,
                'jurusan' => $kelas->jurusan->nama_jurusan ?? '-',
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total_siswa' => $hadir + $izin + $sakit + $alpha,
                'status_input' => $statusInput,
            ];
        });

        return view('admin.waka.rekap_presensi.index', compact('rekapData', 'tanggal'));
    }
}