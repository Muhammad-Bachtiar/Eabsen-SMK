@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h4>Tambah Jenis Pelanggaran</h4></div>
    <div class="card-body">
        <form action="{{ route('koordinator-bk.jenis-pelanggaran.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label>Nama Pelanggaran</label>
                <input type="text" name="nama_pelanggaran" class="form-control" placeholder="Contoh: Terlambat masuk kelas" required>
            </div>
            
            <div class="form-group mb-3">
                <label>Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="ringan">Ringan</option>
                    <option value="sedang">Sedang</option>
                    <option value="berat">Berat</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label>Poin Pelanggaran</label>
                <input type="number" name="poin" class="form-control" placeholder="Contoh: 10" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('koordinator-bk.jenis-pelanggaran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection