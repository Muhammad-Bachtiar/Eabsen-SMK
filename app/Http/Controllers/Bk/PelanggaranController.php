<?php
namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use Illuminate\Support\Facades\Auth;

class PelanggaranController extends Controller
{
    public function index()
    {
        // Tarik semua data pelanggaran yang pernah dicatat, urutkan dari yang terbaru
        $pelanggarans = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran'])
                                        ->orderBy('tanggal_kejadian', 'desc')
                                        ->get();

        return view('admin.bk.pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        // Ambil data siswa yang aktif dan daftar jenis pelanggaran untuk dropdown
        $siswas = Siswa::where('status', 'aktif')->orderBy('nama', 'asc')->get();
        $jenisPelanggarans = JenisPelanggaran::orderBy('nama_pelanggaran', 'asc')->get();

        return view('admin.bk.pelanggaran.create', compact('siswas', 'jenisPelanggarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'jenis_pelanggaran_id' => 'required',
            'tanggal_kejadian' => 'required|date',
            'deskripsi' => 'required',
        ]);

        // Trik jitu: Kita ambil poin otomatis dari master data, biar BK gak usah ngetik manual!
        $jenis = JenisPelanggaran::findOrFail($request->jenis_pelanggaran_id);

        PelanggaranSiswa::create([
            'siswa_id' => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'deskripsi' => $request->deskripsi,
            'poin' => $jenis->poin, 
            'dicatat_oleh' => Auth::id(),
            'status' => 'menunggu_persetujuan' // Sesuaikan dengan tulisan ENUM di database jenengan (misal: 'menunggu_persetujuan')
        ]);

        return redirect()->route('bk.pelanggaran.index')->with('success', 'Catatan pelanggaran berhasil disimpan dan menunggu persetujuan!');
    }
}