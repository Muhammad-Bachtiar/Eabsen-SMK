<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use Carbon\Carbon;
use Auth;

class RekapKelasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil daftar kelas yang diampu guru ini untuk pilihan dropdown
        $kelases = \DB::table('guru_mapel_kelas')
            ->join('kelas', 'guru_mapel_kelas.kelas_id', '=', 'kelas.id')
            ->where('guru_mapel_kelas.guru_id', $user->id)
            ->select('kelas.id', 'kelas.nama_kelas')
            ->distinct()
            ->get();

        // Default filter
        $selectedKelas = $request->input('kelas_id', $kelases->first()->id ?? null);
        $selectedTanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // 2. Tarik daftar presensi (sesi) yang terjadi di kelas & tanggal tersebut
        $riwayatSesi = [];
        if ($selectedKelas) {
            $riwayatSesi = Presensi::with(['mapel', 'pencatat', 'presensiJams.jamPelajaran'])
                ->where('kelas_id', $selectedKelas)
                ->whereDate('tanggal', $selectedTanggal)
                ->get();
        }

        return view('admin.guru.rekap_kelas.index', compact(
            'kelases', 'selectedKelas', 'selectedTanggal', 'riwayatSesi'
        ));
    }
}