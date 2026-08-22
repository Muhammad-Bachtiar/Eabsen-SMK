<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Mengambil semua data kelas beserta relasi jurusan
        $kelases = Kelas::with('jurusan')->get();
        return view('admin.kelas.index', compact('kelases'));
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        return view('admin.kelas.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tingkat' => 'required|in:X,XI,XII',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar.',
            'jurusan_id.required' => 'Pilihan jurusan wajib diisi.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
        ]);

        Kelas::create($request->all());
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $jurusans = Jurusan::all();
        return view('admin.kelas.edit', compact('kelas', 'jurusans'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kelas->id,
            'jurusan_id' => 'required|exists:jurusans,id',
            'tingkat' => 'required|in:X,XI,XII',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar.',
            'jurusan_id.required' => 'Pilihan jurusan wajib diisi.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
        ]);

        $kelas->update($request->all());
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        
        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }
}