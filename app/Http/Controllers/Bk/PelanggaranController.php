<?php
namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
use App\Models\TindakLanjutPelanggaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PelanggaranController extends Controller
{
    public function index()
    {
        $pelanggarans = PelanggaranSiswa::with([
                'siswa.kelas.waliKelas',
                'jenisPelanggaran',
                'penyetuju',
                'tindakLanjut.user',
            ])
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
    public function show($id)
{
    $user = Auth::user();
    $role = $user->role->nama_role ?? null;

    // Validasi akses
    $allowed = in_array($role, ['admin', 'bk', 'waka_kesiswaan'])
        || ($role === 'bk' && $user->is_koordinator_bk);

    if (!$allowed) {
        abort(403, 'Anda tidak memiliki hak untuk melihat detail pelanggaran ini.');
    }

    $pelanggaran = PelanggaranSiswa::with([
        'siswa.kelas.waliKelas',
        'jenisPelanggaran',
        'pencatat',
        'penyetuju',
        'tindakLanjut.user',
    ])->findOrFail($id);

    // Urutkan riwayat tindak lanjut dari yang paling lama
    $riwayat = $pelanggaran->tindakLanjut->sortBy('created_at');

    return view('admin.bk.pelanggaran.show', compact('pelanggaran', 'riwayat'));
}

    public function cetakPdf($id)
    {
        $user = Auth::user();
        $role = $user->role->nama_role ?? null;

        if (!in_array($role, ['admin', 'bk', 'waka_kesiswaan']) && !$user->is_koordinator_bk) {
            abort(403);
        }

        $pelanggaran = PelanggaranSiswa::with([
            'siswa.kelas',
            'jenisPelanggaran',
            'pencatat',
            'penyetuju',
            'tindakLanjut.user',
        ])->findOrFail($id);

        $riwayat = $pelanggaran->tindakLanjut->sortBy('created_at');

        $pdf = Pdf::loadView('admin.bk.pelanggaran.pdf', compact('pelanggaran', 'riwayat'))
            ->setPaper('A4', 'portrait');

        $namaFile = 'Detail_Pelanggaran_' . ($pelanggaran->siswa->nis ?? $pelanggaran->siswa->nisn ?? $pelanggaran->id)
            . '_' . \Carbon\Carbon::parse($pelanggaran->tanggal_kejadian)->format('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }
    public function selesaikan($id)
    {
        $pelanggaran = PelanggaranSiswa::findOrFail($id);

        if ($pelanggaran->status !== 'disetujui') {
            return back()->with('error', 'Hanya pelanggaran yang sudah disetujui yang bisa ditandai selesai.');
        }

        DB::transaction(function () use ($pelanggaran) {
            $pelanggaran->update(['status' => 'selesai']);

            TindakLanjutPelanggaran::create([
                'pelanggaran_id' => $pelanggaran->id,
                'oleh_user_id'   => Auth::id(),
                'catatan'        => 'Tindak lanjut dieksekusi. Status diubah menjadi selesai oleh ' . (Auth::user()->nama ?? 'BK'),
                'status_baru'    => 'selesai',
            ]);
        });

        return back()->with('success', 'Pelanggaran berhasil ditandai selesai.');
    }
}