<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\PresensiDetail;
use App\Models\PresensiJam;
use App\Models\GuruMapelKelas; // Sesuaikan jika nama model penugasan Anda beda
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

        $query = \App\Models\Presensi::with(['kelas', 'mapel', 'pencatat']);
        
        // Jika bukan admin, filter hanya buatan guru yang login
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
        $jadwals = GuruMapelKelas::with(['kelas', 'mapel'])
            ->where('guru_id', $user->id)
            ->get();

        $presensiSelesai = null;
        $detailsSelesai  = [];

        // Jika baru saja melakukan simpan presensi
        if ($request->has('presensi_id')) {
            $presensiSelesai = Presensi::with(['kelas', 'mapel'])->find($request->presensi_id);
            if ($presensiSelesai) {
                $detailsSelesai = PresensiDetail::with('siswa')
                    ->where('presensi_id', $presensiSelesai->id)
                    ->get();
            }
        }

        return view('admin.guru.presensi.create', compact('jadwals', 'user', 'presensiSelesai', 'detailsSelesai'));
    }

    // Fungsi AJAX untuk memunculkan daftar siswa tanpa reload halaman
    public function getSiswa($kelas_id)
    {
        $siswas = Siswa::where('kelas_id', $kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();
            
        return response()->json($siswas);
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

        // 1. Simpan Header Presensi
        $presensi = Presensi::create([
            'tanggal'      => $request->tanggal,
            'kelas_id'     => $request->kelas_id,
            'mapel_id'     => $request->mapel_id,
            'dicatat_oleh' => Auth::id(),
        ]);

        // 2. Simpan Jam Pelajaran
        foreach ($request->jam as $j) {
            \DB::table('presensi_jams')->insert([
                'presensi_id'      => $presensi->id,
                'jam_pelajaran_id' => $j,
            ]);
        }

        // 3. Simpan Detail Kehadiran Siswa
        foreach ($request->status as $siswaId => $st) {
            PresensiDetail::create([
                'presensi_id' => $presensi->id,
                'siswa_id'    => $siswaId,
                'status'      => $st,
            ]);
        }

        // Redirect kembali ke Dashboard Guru dengan membawa presensi_id agar tabel ringkasan muncul di bawah
        return redirect()->route('guru.dashboard', [
            'presensi_id' => $presensi->id
        ])->with('success', 'Presensi berhasil disimpan!');
    }

    public function show($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'admin' || $user->role_id == 1;

        $query = \App\Models\Presensi::with(['kelas', 'mapel', 'pencatat']);

        // Jika BUKAN admin, kunci akses hanya untuk guru pencatatnya sendiri
        if (!$isAdmin) {
            $query->where('dicatat_oleh', $user->id);
        }

        // Ambil data presensi (akan 404 HANYA jika ID memang tidak ada di DB, atau guru mencoba buka milik guru lain)
        $presensi = $query->findOrFail($id);

        // Tarik jam pelajaran yang dicentang
        $jams = \App\Models\PresensiJam::where('presensi_id', $id)
                        ->pluck('jam_pelajaran_id')
                        ->toArray();

        // Tarik detail siswa beserta statusnya
        $details = \App\Models\PresensiDetail::with('siswa')
                        ->where('presensi_id', $id)
                        ->get();

        return view('admin.guru.presensi.show', compact('presensi', 'jams', 'details'));
    }
}