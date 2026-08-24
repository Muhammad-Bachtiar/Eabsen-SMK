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

    public function create()
    {
        $user = Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'admin';

        // Jika admin, ambil semua jadwal penugasan agar bisa testing
        if ($isAdmin) {
            $jadwals = GuruMapelKelas::with(['kelas', 'mapel', 'guru'])->get();
        } else {
            $jadwals = GuruMapelKelas::with(['kelas', 'mapel'])
                ->where('guru_id', $user->id)
                ->get();
        }

        return view('admin.guru.presensi.create', compact('jadwals'));
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
        // Validasi isian guru
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'jam' => 'required|array', // Pastikan minimal ada 1 jam yang dicentang
            'status' => 'required|array' 
        ]);

        try {
            // Pakai database transaction, biar kalau ada error tengah jalan, datanya gak masuk setengah-setengah
            DB::beginTransaction();

            // 1. Bikin Induk Presensi
            $presensi = Presensi::create([
                'tanggal' => $request->tanggal,
                'kelas_id' => $request->kelas_id,
                'jenis' => 'mapel',
                'mapel_id' => $request->mapel_id,
                'dicatat_oleh' => Auth::id(), 
            ]);

            // 2. Simpan Jam Pelajaran (Bisa banyak)
            foreach ($request->jam as $jamKe) {
                PresensiJam::create([
                    'presensi_id' => $presensi->id,
                    'jam_pelajaran_id' => $jamKe  // <--- Ubah bagian ini
                ]);
            }

            // 3. Simpan Status Absen Tiap Siswa
            foreach ($request->status as $siswaId => $statusSiswa) {
                PresensiDetail::create([
                    'presensi_id' => $presensi->id,
                    'siswa_id' => $siswaId,
                    'status' => $statusSiswa
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Mantap! Data absensi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Waduh, gagal menyimpan absen: ' . $e->getMessage());
        }
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