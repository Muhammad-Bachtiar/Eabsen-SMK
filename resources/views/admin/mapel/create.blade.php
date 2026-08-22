@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran - Admin')
@section('header_title', 'Tambah Mata Pelajaran')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Tambah Mata Pelajaran</h4>
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

            <form action="{{ route('admin.mapel.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="kode_mapel" class="form-label font-bold">Kode Mapel</label>
                    <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" placeholder="Contoh: PAI, MTK, PWEB" value="{{ old('kode_mapel') }}" required>
                </div>
                <div class="form-group mb-4">
                    <label for="nama_mapel" class="form-label font-bold">Nama Mata Pelajaran</label>
                    <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" placeholder="Contoh: Pemrograman Web dan Perangkat Bergerak" value="{{ old('nama_mapel') }}" required>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection