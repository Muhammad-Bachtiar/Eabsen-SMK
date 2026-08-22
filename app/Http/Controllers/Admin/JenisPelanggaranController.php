<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    public function index()
    {
        $pelanggarans = JenisPelanggaran::orderBy('kategori', 'asc')->orderBy('poin', 'asc')->get();
        return view('admin.jenis-pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        return view('admin.jenis-pelanggaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin' => 'required|integer|min:1',
        ]);

        JenisPelanggaran::create($request->all());
        return redirect()->route('admin.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pelanggaran = JenisPelanggaran::findOrFail($id);
        return view('admin.jenis-pelanggaran.edit', compact('pelanggaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggaran' => 'required',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin' => 'required|integer|min:1',
        ]);

        $pelanggaran = JenisPelanggaran::findOrFail($id);
        $pelanggaran->update($request->all());

        return redirect()->route('admin.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JenisPelanggaran::findOrFail($id)->delete();
        return redirect()->route('admin.jenis-pelanggaran.index')->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
