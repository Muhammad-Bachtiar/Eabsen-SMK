@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h4>Edit Jenis Pelanggaran</h4></div>
    <div class="card-body">
        <form action="{{ route('koordinator-bk.jenis-pelanggaran.update', $pelanggaran->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label>Nama Pelanggaran</label>
                <input type="text" name="nama_pelanggaran" class="form-control" value="{{ $pelanggaran->nama_pelanggaran }}" required>
            </div>
            
            <div class="form-group mb-3">
                <label>Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="ringan" {{ $pelanggaran->kategori == 'ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="sedang" {{ $pelanggaran->kategori == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="berat" {{ $pelanggaran->kategori == 'berat' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label>Poin Pelanggaran</label>
                <input type="number" name="poin" class="form-control" value="{{ $pelanggaran->poin }}" required>
            </div>
            
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('koordinator-bk.jenis-pelanggaran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection