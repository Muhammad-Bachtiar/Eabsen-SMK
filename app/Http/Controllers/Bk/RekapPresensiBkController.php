<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Presensi;
use Carbon\Carbon;

class RekapPresensiBkController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::all();
        $selectedKelas = $request->input('kelas_id', $kelases->first()->id ?? null);
        $selectedTanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Tarik gabungan presensi Mapel + BK untuk kelas & tanggal terpilih
        $presensis = [];
        if ($selectedKelas) {
            $presensis = Presensi::with(['pencatat', 'mapel', 'presensiDetails.siswa'])
                ->where('kelas_id', $selectedKelas)
                ->whereDate('tanggal', $selectedTanggal)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('admin.bk.rekap_presensi.index', compact(
            'kelases', 'selectedKelas', 'selectedTanggal', 'presensis'
        ));
    }
}