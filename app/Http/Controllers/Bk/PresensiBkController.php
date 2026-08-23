<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use Auth;
use DB;

class PresensiBkController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil riwayat presensi yang diinput oleh BK ini (jenis = bk)
        $riwayat = Presensi::with('kelas')
            ->where('dicatat_oleh', $user->id)
            ->where('jenis', 'bk') // Membedakan dari presensi mapel
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.bk.presensi.index', compact('riwayat'));
    }

    public function create()
    {
        $user = Auth::user();

        // Ambil kelas binaan BK dari tabel bk_kelas
        $kelases = DB::table('bk_kelas')
            ->join('kelas', 'bk_kelas.kelas_id', '=', 'kelas.id')
            ->where('bk_kelas.bk_user_id', $user->id)
            ->select('kelas.id', 'kelas.nama_kelas')
            ->get();

        return view('admin.bk.presensi.create', compact('kelases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'tanggal'  => 'required|date',
            'status'   => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $presensi = Presensi::create([
                'kelas_id'     => $request->kelas_id,
                'tanggal'      => $request->tanggal,
                'dicatat_oleh' => Auth::id(),
                'jenis'        => 'bk', // Penanda presensi jenis BK
            ]);

            foreach ($request->status as $siswaId => $st) {
                PresensiDetail::create([
                    'presensi_id' => $presensi->id,
                    'siswa_id'    => $siswaId,
                    'status'      => $st,
                    'catatan'     => $request->catatan[$siswaId] ?? null,
                ]);
            }
        });

        return redirect()->route('bk.presensi.index')->with('success', 'Presensi kelas binaan berhasil disimpan!');
    }
    public function getSiswa($kelasId)
{
    $siswas = \App\Models\Siswa::where('kelas_id', $kelasId)
                ->orderBy('nama', 'asc')
                ->get();

    return response()->json($siswas);
}
}