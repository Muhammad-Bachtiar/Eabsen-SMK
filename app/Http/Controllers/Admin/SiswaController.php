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

                $reader = SimpleExcelReader::create($file->getRealPath(), $file->getClientOriginalExtension());
                $rows = $reader->getRows();

                // Ambil semua data kelas dari database dan format nama kelasnya sebagai pembanding
                $kelases = Kelas::all()->map(function($k) {
                    return [
                        'id' => $k->id,
                        'formatted' => $this->formatNamaKelas($k->nama_kelas)
                    ];
                });

                $importedCount = 0;
                $failedClassCount = 0;

                foreach ($rows as $row) {
                    // Normalize key header Excel menjadi lowercase
                    $cleanRow = [];
                    foreach ($row as $key => $value) {
                        $cleanRow[strtolower(trim($key))] = is_string($value) ? trim($value) : $value;
                    }

                    $nis       = $cleanRow['nis'] ?? $cleanRow['nisn'] ?? null;
                    $nama      = $cleanRow['nama'] ?? $cleanRow['nama_siswa'] ?? $cleanRow['nama lengkap'] ?? null;
                    $namaKelas = $cleanRow['kelas'] ?? $cleanRow['nama_kelas'] ?? $cleanRow['nama kelas'] ?? null;
                    $jk        = $cleanRow['jenis_kelamin'] ?? $cleanRow['jk'] ?? 'L';

                    if (!empty($nama) && !empty($nis) && !empty($namaKelas)) {
                        // Format nama kelas dari Excel
                        $formattedExcelKelas = $this->formatNamaKelas($namaKelas);

                        // Cari ID Kelas yang cocok berdasarkan nama kelas yang sudah diformat
                        $kelasMatched = $kelases->firstWhere('formatted', $formattedExcelKelas);

                        if ($kelasMatched) {
                            Siswa::updateOrCreate(
                                ['nis' => (string) $nis],
                                [
                                    'nisn'          => $cleanRow['nisn'] ?? (string) $nis,
                                    'nama'          => $nama,
                                    'kelas_id'      => $kelasMatched['id'],
                                    'jenis_kelamin' => strtoupper(substr($jk, 0, 1)),
                                    'status'        => 'aktif',
                                ]
                            );
                            $importedCount++;
                        } else {
                            $failedClassCount++;
                        }
                    }
                }

                if ($importedCount === 0) {
                    if ($failedClassCount > 0) {
                        return redirect()->route('admin.siswa.index')
                            ->with('error', "Gagal mengimport data! $failedClassCount siswa tidak dapat di-import karena Kelas tidak ditemukan di Master Data.");
                    }
                    return redirect()->route('admin.siswa.index')
                        ->with('error', 'Gagal mengimport data! Pastikan kolom header Excel sesuai (nis, nama, kelas, jenis_kelamin).');
                }

                return redirect()->route('admin.siswa.index')
                    ->with('success', "Berhasil mengimport $importedCount data siswa!");

            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
            }
        }
        /**
         * Standarisasi format nama kelas (contoh: 'xrpl1', 'X-RPL-1' -> 'X RPL 1')
         */
        private function formatNamaKelas($input)
        {
            if (empty($input)) return '';

            // 1. Ubah strip (-) menjadi spasi & jadikan uppercase
            $string = strtoupper(str_replace('-', ' ', trim($input)));

            // 2. Pisahkan huruf dan angka yang berdempetan dengan spasi
            // Contoh: 'X10' -> 'X 10', 'RPL1' -> 'RPL 1', '10RPL' -> '10 RPL'
            $string = preg_replace('/([a-zA-Z]+)(\d+)/', '$1 $2', $string);
            $string = preg_replace('/(\d+)([a-zA-Z]+)/', '$1 $2', $string);

            // 3. Gabungkan spasi ganda menjadi spasi tunggal
            $string = preg_replace('/\s+/', ' ', $string);

            return trim($string);
        }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data Siswa berhasil dihapus.');
    }

}