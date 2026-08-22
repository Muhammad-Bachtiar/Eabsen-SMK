@extends('layouts.app')

@section('title', 'Edit Kelas - Admin')
@section('header_title', 'Edit Data Kelas')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Edit Kelas</h4>
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

            <form action="{{ route('admin.kelas.update', $kelas->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="jurusan_id" class="form-label font-bold">Jurusan</label>
                    <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                        <option value="" disabled>-- Pilih Jurusan --</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $kelas->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="tingkat" class="form-label font-bold">Tingkat</label>
                    <select class="form-select" id="tingkat" name="tingkat" required>
                        <option value="" disabled>-- Pilih Tingkat --</option>
                        <option value="X" {{ old('tingkat', $kelas->tingkat) == 'X' ? 'selected' : '' }}>Tingkat X (Sepuluh)</option>
                        <option value="XI" {{ old('tingkat', $kelas->tingkat) == 'XI' ? 'selected' : '' }}>Tingkat XI (Sebelas)</option>
                        <option value="XII" {{ old('tingkat', $kelas->tingkat) == 'XII' ? 'selected' : '' }}>Tingkat XII (Dua Belas)</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="nama_kelas" class="form-label font-bold">Nama Kelas</label>
                    <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required>
                </div>
                
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Update Data</button>
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection