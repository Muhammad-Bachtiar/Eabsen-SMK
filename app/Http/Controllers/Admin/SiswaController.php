<?php

namespace App\Http\Controllers\Admin;

use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::all();
        return view('admin.siswa.index', compact('siswas'));
    }

    public function create()
    {
        // Ambil data kelas untuk dropdown
        $kelases = Kelas::all();
        return view('admin.siswa.create', compact('kelases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis',
            'nama' => 'required',
            'kelas_id' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        Siswa::create($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Data Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelases = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelases'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis,' . $siswa->id,
            'nama' => 'required',
            'kelas_id' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        $siswa->update($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Data Siswa berhasil diperbarui.');
    }
    
    // 1. Method Download Template File Fisik
        public function downloadTemplate()
        {
            $filePath = public_path('template/template_siswa.xlsx');
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File template belum tersedia di folder public/template/');
            }

            return response()->download($filePath);
        }

        // 2. Method Import Langsung Baca Array/Collection
        public function import(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:2048'
            ]);

            try {
                $file = $request->file('file');

                // Membaca file Excel/CSV menggunakan SimpleExcelReader
                $rows = SimpleExcelReader::create($file->getRealPath(), $file->getClientOriginalExtension())->getRows();

                $rows->each(function(array $row) {
                    // Ambil NIS/NISN, Nama, Kelas, dan Jenis Kelamin dari baris Excel
                    $nis       = $row['nis'] ?? $row['nisn'] ?? null;
                    $nama      = $row['nama'] ?? $row['nama_siswa'] ?? null;
                    $namaKelas = $row['kelas'] ?? $row['nama_kelas'] ?? null;
                    $jk        = $row['jenis_kelamin'] ?? $row['jk'] ?? 'L';

                    if (!empty($nama) && !empty($namaKelas) && !empty($nis)) {
                        // Cari ID Kelas berdasarkan nama kelas di Excel
                        $kelas = Kelas::where('nama_kelas', 'LIKE', '%' . trim($namaKelas) . '%')->first();

                        if ($kelas) {
                            Siswa::updateOrCreate(
                                ['nis' => $nis], // Gunakan 'nis' sebagai kunci pencarian utama
                                [
                                    'nisn'          => $row['nisn'] ?? $nis,
                                    'nama'          => $nama,
                                    'kelas_id'      => $kelas->id,
                                    'jenis_kelamin' => strtoupper($jk),
                                    'status'        => 'aktif',
                                ]
                            );
                        }
                    }
                });

                return redirect()->route('admin.siswa.index')->with('success', 'Data Siswa berhasil diimport!');

            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
            }
        }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data Siswa berhasil dihapus.');
    }

}