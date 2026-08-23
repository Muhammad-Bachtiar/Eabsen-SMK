<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PelanggaranSiswa;

class LaporanPelanggaranController extends Controller
{
    public function index()
    {
        // Hapus 'disetujuiOleh' dari dalam array with()
        $pelanggarans = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran'])
                                        ->orderBy('tanggal_kejadian', 'desc')
                                        ->get();

        return view('admin.kepsek.pelanggaran.index', compact('pelanggarans'));
    }
}