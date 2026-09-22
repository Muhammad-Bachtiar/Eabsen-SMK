<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Ambil data kelas beserta relasi jurusan dan wali kelas
        $kelases = Kelas::with(['jurusan', 'waliKelas'])->get();
        return view('admin.kelas.index', compact('kelases'));
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        $gurus    = User::whereHas('role', function($q){
            $q->whereIn('nama_role', ['guru', 'bk']);
        })->get();

        // TAMBAHKAN $jurusans KE DALAM COMPACT
        return view('admin.kelas.create', compact('jurusans', 'gurus'));
    }
    private function formatNamaKelas($input)
    {
        if (empty($input)) return '';
        $string = strtoupper(str_replace('-', ' ', trim($input)));
        $string = preg_replace('/([a-zA-Z]+)(\d+)/', '$1 $2', $string);
        $string = preg_replace('/(\d+)([a-zA-Z]+)/', '$1 $2', $string);
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'    => 'required|unique:kelas,nama_kelas',
            'jurusan_id'    => 'required|exists:jurusans,id',
            'tingkat'       => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:users,id'
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas ini sudah terdaftar.',
            'jurusan_id.required' => 'Pilihan jurusan wajib diisi.',
            'tingkat.required'    => 'Tingkat kelas wajib dipilih.',
        ]);

        Kelas::create($request->all());
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        // Sesuaikan parameter $kela dari Route Resource
        $kelas    = $kela; 
        $jurusans = Jurusan::all();
        $gurus    = User::whereHas('role', function($q){
            $q->whereIn('nama_role', ['guru', 'bk']);
        })->get();

        // TAMBAHKAN $jurusans KE DALAM COMPACT
        return view('admin.kelas.edit', compact('kelas', 'jurusans', 'gurus'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $kelas = $kela;

        $request->validate([
            'nama_kelas'    => 'required|unique:kelas,nama_kelas,' . $kelas->id,
            'jurusan_id'    => 'required|exists:jurusans,id',
            'tingkat'       => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:users,id'
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas ini sudah terdaftar.',
            'jurusan_id.required' => 'Pilihan jurusan wajib diisi.',
            'tingkat.required'    => 'Tingkat kelas wajib dipilih.',
        ]);

        $kelas->update($request->all());
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }
    

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }
}