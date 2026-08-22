@extends('layouts.app')

@section('title', 'Edit Guru - Admin')
@section('header_title', 'Edit Akun Guru')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Form Edit Akun Login Guru</h4>
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

            <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="nip_nik" class="form-label font-bold">NIP / NIK</label>
                    <input type="text" class="form-control" id="nip_nik" name="nip_nik" value="{{ old('nip_nik', $guru->nip_nik) }}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="nama" class="form-label font-bold">Nama Lengkap (Beserta Gelar)</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="email" class="form-label font-bold">Email Login</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $guru->email) }}" required>
                </div>
                
                <div class="form-group mb-4">
                    <label for="password" class="form-label font-bold">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah password saat ini.</small>
                </div>
                
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Update Akun</button>
                <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection