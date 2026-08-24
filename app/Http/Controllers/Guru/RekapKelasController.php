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
        // Cek apakah yang login adalah admin
        $isAdmin = optional($user->role)->nama_role === 'admin' || $user->role_id == 1; // sesuaikan pengecekan role admin Anda

        // 1. Ambil daftar kelas (Admin bisa lihat semua, Guru hanya kelas yang diampu)
        if ($isAdmin) {
            $kelases = \App\Models\Kelas::select('id', 'nama_kelas')->get();
        } else {
            $kelases = \DB::table('guru_mapel_kelas')
                ->join('kelas', 'guru_mapel_kelas.kelas_id', '=', 'kelas.id')
                ->where('guru_mapel_kelas.guru_id', $user->id)
                ->select('kelas.id', 'kelas.nama_kelas')
                ->distinct()
                ->get();
        }

        $selectedKelas = $request->input('kelas_id', $kelases->first()->id ?? null);
        $selectedTanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // 2. Tarik daftar presensi
        $riwayatSesi = [];
        if ($selectedKelas) {
            $query = Presensi::with(['mapel', 'pencatat', 'presensiJams.jamPelajaran'])
                ->where('kelas_id', $selectedKelas)
                ->whereDate('tanggal', $selectedTanggal);

            // Jika bukan admin, batasi hanya buatan guru tersebut
            if (!$isAdmin) {
                $query->where('dicatat_oleh', $user->id);
            }

            $riwayatSesi = $query->get();
        }

        return view('admin.guru.rekap_kelas.index', compact(
            'kelases', 'selectedKelas', 'selectedTanggal', 'riwayatSesi'
        ));
    }
    }