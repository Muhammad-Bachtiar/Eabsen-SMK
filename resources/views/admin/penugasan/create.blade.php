@extends('layouts.app')

@section('title', 'Tambah Penugasan Mengajar - Admin')
@section('header_title', 'Tambah Penugasan Mengajar')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Tambah Penugasan Guru</h4>
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

            <form action="{{ route('admin.penugasan.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="guru_id" class="form-label font-bold">Pilih Guru</label>
                    <select name="guru_id" id="guru_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Guru Pengajar --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama }} ({{ $guru->nip_nik ?? 'No NIP' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label for="mapel_id" class="form-label font-bold">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" id="mapel_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                [{{ $mapel->kode_mapel }}] {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="kelas_id" class="form-label font-bold">Pilih Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Penugasan</button>
                <a href="{{ route('admin.penugasan.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection