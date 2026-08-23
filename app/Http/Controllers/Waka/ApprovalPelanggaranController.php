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
    public function index()
    {
        // Tarik data pelanggaran yang statusnya masih 'menunggu_persetujuan'
        $pelanggarans = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran'])
                                        ->where('status', 'menunggu_persetujuan')
                                        ->orderBy('tanggal_kejadian', 'desc')
                                        ->get();

        return view('admin.waka.approval.index', compact('pelanggarans'));
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $id) {
            $pelanggaran = PelanggaranSiswa::findOrFail($id);

            // 1. Update status utama di tabel pelanggaran_siswas
            $pelanggaran->update([
                'status' => $request->status,
                'rencana_tindak_lanjut' => $request->catatan,
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