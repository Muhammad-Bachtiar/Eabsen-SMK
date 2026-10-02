<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use App\Models\PresensiJam;
use App\Models\MataPelajaran;
use App\Models\JamPelajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        'mapel_id' => 'required',           // ← BK sekarang pilih mapel
        'tanggal'  => 'required|date',
        'jam'      => 'required|array|min:1',
        'status'   => 'required|array',
    ]);

    DB::beginTransaction();
    try {
        // --- CEK DUPLIKAT JAM (server-side lock) ---
        $existing = DB::table('presensi_jams')
            ->join('presensis', 'presensi_jams.presensi_id', '=', 'presensis.id')
            ->where('presensis.kelas_id', $request->kelas_id)
            ->where('presensis.tanggal', $request->tanggal)
            ->whereIn('presensi_jams.jam_pelajaran_id', $request->jam)
            ->pluck('presensi_jams.jam_pelajaran_id')
            ->toArray();

        if (!empty($existing)) {
            DB::rollBack();

            // Kalau AJAX → return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam berikut sudah terisi: Jam ' . implode(', ', $existing),
                ], 422);
            }

            // Kalau form biasa → redirect back
            return back()->with('error', 'Jam berikut sudah terisi: Jam ' . implode(', ', $existing));
        }

        // --- SIMPAN HEADER PRESENSI ---
        $presensi = Presensi::create([
            'kelas_id'     => $request->kelas_id,
            'mapel_id'     => $request->mapel_id,
            'tanggal'      => $request->tanggal,
            'dicatat_oleh' => Auth::id(),
            'jenis'        => 'bk',
        ]);

        // --- SIMPAN JAM (multi-jam) ---
        foreach ($request->jam as $jamKe) {
            PresensiJam::create([
                'presensi_id'      => $presensi->id,
                'jam_pelajaran_id' => $jamKe,
            ]);
        }

        // --- SIMPAN DETAIL SISWA ---
        foreach ($request->status as $siswaId => $st) {
            PresensiDetail::create([
                'presensi_id' => $presensi->id,
                'siswa_id'    => $siswaId,
                'status'      => $st,
                'keterangan'  => $request->keterangan[$siswaId] ?? null,   // ← fix: catatan → keterangan
            ]);
        }

        DB::commit();

        // --- RESPON ---
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Presensi BK berhasil disimpan!',
                'presensi_id' => $presensi->id,
                'jam_diisi'   => $request->jam,
            ]);
        }

        return redirect()->route('bk.presensi.index')
            ->with('success', 'Presensi BK berhasil disimpan!');

    } catch (\Exception $e) {
        DB::rollBack();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return back()->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
    }
}
    public function getSiswa($kelasId)
{
    $siswas = \App\Models\Siswa::where('kelas_id', $kelasId)
                ->orderBy('nama', 'asc')
                ->get();

    return response()->json($siswas);
}
public function getData(Request $request)
{
    try {
        $kelasId = $request->kelas_id;
        $tanggal = $request->tanggal;

        if (!$kelasId || !$tanggal) {
            return response()->json([
                'siswas'  => [],
                'riwayat' => (object)[],
            ]);
        }

        // 1. Data siswa
        $siswas = Siswa::where('kelas_id', $kelasId)
            ->orderBy('nama', 'asc')
            ->get(['id', 'nama', 'nis']);

        // 2. Riwayat presensi SEMUA jenis (mapel + bk) di kelas & tanggal ini
        //    → biar nyambung, tidak ada filter 'jenis'
        $presensis = Presensi::with([
                'pencatat',
                'mapel',
                'presensiDetails.siswa',
                'presensiJams.jamPelajaran',
            ])
            ->where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->get();

        $riwayatFormatted = [];

        foreach ($presensis as $presensi) {
            foreach ($presensi->presensiJams as $pJam) {
                $jamKe = $pJam->jamPelajaran
                    ? $pJam->jamPelajaran->jam_ke
                    : $pJam->jam_pelajaran_id;

                $riwayatFormatted[$jamKe] = [
                    'presensi_id'   => $presensi->id,
                    'jenis'         => $presensi->jenis,
                    'pencatat_nama' => $presensi->pencatat
                        ? ($presensi->pencatat->nama ?? $presensi->pencatat->name)
                        : 'Petugas',
                    'mapel_nama'    => $presensi->jenis === 'bk'
                        ? 'Bimbingan Konseling (BK)'
                        : ($presensi->mapel ? $presensi->mapel->nama_mapel : 'Mapel'),
                    'details'       => $presensi->presensiDetails->map(function ($d) {
                        return [
                            'siswa_id'   => $d->siswa_id,
                            'nama'       => $d->siswa ? $d->siswa->nama : 'Siswa',
                            'status'     => $d->status,
                            'keterangan' => $d->keterangan,
                        ];
                    }),
                ];
            }
        }

        return response()->json([
            'siswas'  => $siswas,
            'riwayat' => (object) $riwayatFormatted,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => true,
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
        ], 500);
    }
}
}