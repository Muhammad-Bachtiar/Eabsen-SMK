<?php

namespace App\Http\Controllers\Waka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PelanggaranSiswa;
use App\Models\TindakLanjutPelanggaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalPelanggaranController extends Controller
{
    private function authorizeApprover()
    {
    $user = Auth::user();
    $role = $user->role->nama_role ?? null;

    $isWaka         = $role === 'waka_kesiswaan' || $role === 'admin';
    $isKoordinatorBK = $role === 'bk' && $user->is_koordinator_bk;

    if (!$isWaka && !$isKoordinatorBK) {
        abort(403, 'Akses ditolak. Hanya Waka Kesiswaan atau Koordinator BK yang bisa menyetujui pelanggaran.');
    }
    }
    public function index(Request $request)
    {
        $this->authorizeApprover(); 
        // Tarik data pelanggaran yang statusnya masih 'menunggu_persetujuan'
        $pelanggarans = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran'])
                                        ->where('status', 'menunggu_persetujuan')
                                        ->orderBy('tanggal_kejadian', 'desc')
                                        ->get();
        // Tab 2: Riwayat (disetujui/ditolak/selesai) — dengan filter
            $query = PelanggaranSiswa::with(['siswa.kelas', 'jenisPelanggaran', 'penyetuju'])
                ->whereIn('status', ['disetujui', 'ditolak', 'selesai']);

            // Filter tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_kejadian', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_kejadian', '<=', $request->tanggal_sampai);
            }
            // Filter status
            if ($request->filled('filter_status')) {
                $query->where('status', $request->filter_status);
            }
            // Filter "Disetujui Oleh"
            if ($request->filled('filter_penyetuju')) {
                $query->where('disetujui_oleh', $request->filter_penyetuju);
            }

            $riwayat = $query->orderBy('tanggal_persetujuan', 'desc')->get();

            // Daftar penyetuju (untuk dropdown filter)
            $penyetujus = \App\Models\User::whereIn('id', function($q) {
                    $q->select('disetujui_oleh')->from('pelanggaran_siswas')->whereNotNull('disetujui_oleh');
                })
                ->orderBy('nama')
                ->get(['id', 'nama']);

            return view('admin.waka.approval.index', compact('pelanggarans', 'riwayat', 'penyetujus'));
        }

    public function proses(Request $request, $id)
    {
         $this->authorizeApprover();

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $id) {
            $pelanggaran = PelanggaranSiswa::findOrFail($id);

            // 1. Update status utama di tabel pelanggaran_siswas
            $pelanggaran->update([
                'status' => $request->status,
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
            ]);

            // 2. Insert riwayat keputusan ke tabel tindak_lanjut_pelanggarans
            TindakLanjutPelanggaran::create([
                'pelanggaran_id' => $pelanggaran->id,
                'oleh_user_id' => Auth::id(),
                'catatan' => $request->catatan,
                'status_baru' => $request->status,
            ]);
        });

        return redirect()->back()->with('success', 'Keputusan berhasil disimpan dan dicatat ke riwayat tindak lanjut!');
    }
}