@extends('layouts.app')

@section('title', 'Edit Jam Pelajaran - Admin')
@section('header_title', 'Edit Jam Pelajaran')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Edit Jam Pelajaran</h4>
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

            <form action="{{ route('admin.jam-pelajaran.update', $jam->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="jam_ke" class="form-label font-bold">Jam Ke- (Angka)</label>
                    <input type="number" class="form-control" id="jam_ke" name="jam_ke" value="{{ old('jam_ke', $jam->jam_ke) }}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="waktu_mulai" class="form-label font-bold">Waktu Mulai</label>
                    <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', $jam->waktu_mulai) }}" required>
                </div>

                <div class="form-group mb-4">
                    <label for="waktu_selesai" class="form-label font-bold">Waktu Selesai</label>
                    <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', $jam->waktu_selesai) }}" required>
                </div>
                
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Update Data</button>
                <a href="{{ route('admin.jam-pelajaran.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection