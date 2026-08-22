@extends('layouts.app')

@section('title', 'Tambah Jenis Pelanggaran - Admin')
@section('header_title', 'Tambah Jenis Pelanggaran')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Tambah Jenis Pelanggaran</h4>
        </div>
        <div class="card-body">
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.jenis-pelanggaran.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="nama_pelanggaran" class="form-label font-bold">Nama Pelanggaran</label>
                    <input type="text" class="form-control" id="nama_pelanggaran" name="nama_pelanggaran" placeholder="Contoh: Terlambat Masuk Sekolah" value="{{ old('nama_pelanggaran') }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="kategori" class="form-label font-bold">Kategori Pelanggaran</label>
                    <select class="form-select" id="kategori" name="kategori" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="ringan" {{ old('kategori') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('kategori') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat" {{ old('kategori') == 'berat' ? 'selected' : '' }}>Berat</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="poin" class="form-label font-bold">Poin Pelanggaran</label>
                    <input type="number" class="form-control" id="poin" name="poin" min="1" placeholder="Contoh: 5" value="{{ old('poin') }}" required>
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.jenis-pelanggaran.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection
