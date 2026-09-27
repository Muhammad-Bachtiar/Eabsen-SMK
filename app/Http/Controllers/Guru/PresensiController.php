<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use App\Models\PresensiJam;
use App\Models\GuruMapelKelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        $user = Auth::id();
        $userData = Auth::user();
        $isAdmin = optional($userData->role)->nama_role === 'admin';

        $query = Presensi::with(['kelas', 'mapel', 'pencatat']);
        
        if (!$isAdmin) {
            $query->where('dicatat_oleh', $user);
        }

        $riwayatPresensi = $query->orderBy('tanggal', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('admin.guru.presensi.index', compact('riwayatPresensi'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        // Data untuk form
    $kelases       = \App\Models\Kelas::orderBy('nama_kelas')->get();
    $mapels        = \App\Models\MataPelajaran::orderBy('nama_mapel')->get();
    $jamPelajarans = \App\Models\JamPelajaran::orderBy('jam_ke')->get();

    // Default filter (opsional, kalau ada request)
    $tanggal  = $request->input('tanggal', date('Y-m-d'));
    $kelas_id = $request->input('kelas_id');
    $mapel_id = $request->input('mapel_id');

    return view('admin.guru.dashboard', compact(
        'user', 'kelases', 'mapels', 'jamPelajarans',
        'tanggal', 'kelas_id', 'mapel_id'
    ));
}

public function getData(Request $request)
{
    try {
        $kelasId = $request->kelas_id;
        $tanggal = $request->tanggal;

        if (!$kelasId || !$tanggal) {
            return response()->json([
                'siswas'  => [],
                'riwayat' => (object)[]
            ]);
        }

        // 1. Data siswa
        $siswas = Siswa::where('kelas_id', $kelasId)
            ->orderBy('nama', 'asc')
            ->get(['id', 'nama', 'nis']);

        // 2. Riwayat presensi (SEMUA mapel di kelas & tanggal ini,
        //    karena beberapa guru bisa mengisi di jam berbeda)
        $presensis = Presensi::with([
            'pencatat',
            'mapel',
            'presensiDetails.siswa',
            'presensiJams.jamPelajaran'
        ])
        ->where('kelas_id', $kelasId)
        ->where('tanggal', $tanggal)
        ->get();

        $riwayatFormatted = [];

        foreach ($presensis as $presensi) {
            foreach ($presensi->presensiJams as $pJam) {
                // jam_ke = id, jadi aman pakai jamPelajaran->jam_ke
                $jamKe = $pJam->jamPelajaran
                    ? $pJam->jamPelajaran->jam_ke
                    : $pJam->jam_pelajaran_id;

                $riwayatFormatted[$jamKe] = [
                    'presensi_id'   => $presensi->id,
                    'pencatat_nama' => $presensi->pencatat
                        ? ($presensi->pencatat->nama ?? $presensi->pencatat->name)
                        : 'Guru',
                    'mapel_nama'    => $presensi->mapel
                        ? $presensi->mapel->nama_mapel
                        : 'Mapel',
                    'details'       => $presensi->presensiDetails->map(function ($d) {
                        return [
                            'siswa_id'   => $d->siswa_id,
                            'nama'       => $d->siswa ? $d->siswa->nama : 'Siswa',
                            'status'     => $d->status,
                            'keterangan' => $d->keterangan
                        ];
                    })
                ];
            }
        }

        return response()->json([
            'siswas'  => $siswas,
            'riwayat' => (object)$riwayatFormatted
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => true,
            'message' => $e->getMessage(),
            'line'    => $e->getLine()
        ], 500);
    }
}

public function store(Request $request)
{
    $request->validate([
        'tanggal'  => 'required|date',
        'kelas_id' => 'required',
        'mapel_id' => 'required',
        'jam'      => 'required|array|min:1',
        'status'   => 'required|array',
    ]);

    DB::beginTransaction();
    try {
        // Cegah duplikasi: cek apakah jam yang dipilih sudah terisi
        $existing = DB::table('presensi_jams')
            ->join('presensis', 'presensi_jams.presensi_id', '=', 'presensis.id')
            ->where('presensis.kelas_id', $request->kelas_id)
            ->where('presensis.tanggal', $request->tanggal)
            ->whereIn('presensi_jams.jam_pelajaran_id', $request->jam)
            ->pluck('presensi_jams.jam_pelajaran_id')
            ->toArray();

        if (!empty($existing)) {
            DB::rollBack();
            $msg = 'Jam berikut sudah terisi oleh guru lain: Jam ' . implode(', ', $existing);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $presensi = Presensi::create([
            'tanggal'      => $request->tanggal,
            'kelas_id'     => $request->kelas_id,
            'mapel_id'     => $request->mapel_id,
            'dicatat_oleh' => Auth::id(),
            'jenis'        => 'mapel',
        ]);

        foreach ($request->jam as $jamKe) {
            DB::table('presensi_jams')->insert([
                'presensi_id'      => $presensi->id,
                'jam_pelajaran_id' => $jamKe,
            ]);
        }

        foreach ($request->status as $siswaId => $st) {
            PresensiDetail::create([
                'presensi_id' => $presensi->id,
                'siswa_id'    => $siswaId,
                'status'      => $st,
                'keterangan'  => $request->keterangan[$siswaId] ?? null,
            ]);
        }

        DB::commit();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Presensi berhasil disimpan!',
                'presensi_id' => $presensi->id,
                'jam_diisi'   => $request->jam,
            ]);
        }

        return redirect()
            ->route('guru.dashboard', [
                'presensi_id' => $presensi->id,
                'kelas_id'    => $request->kelas_id,
                'tanggal'     => $request->tanggal,
                'mapel_id'    => $request->mapel_id,
            ])->with('success', 'Presensi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
}
    public function show($id)
    {
        $user = Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'admin' || $user->role_id == 1;

        $query = Presensi::with(['kelas', 'mapel', 'pencatat']);

        if (!$isAdmin) {
            $query->where('dicatat_oleh', $user->id);
        }

        $presensi = $query->findOrFail($id);

        $jams = PresensiJam::where('presensi_id', $id)
                        ->pluck('jam_pelajaran_id')
                        ->toArray();

        $details = PresensiDetail::with('siswa')
                        ->where('presensi_id', $id)
                        ->get();

        return view('admin.guru.presensi.show', compact('presensi', 'jams', 'details'));
    }
}