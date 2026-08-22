@extends('layouts.app')

@section('title', 'Tambah Penugasan BK - Admin')
@section('header_title', 'Tambah Penugasan BK')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="card-title mb-0">Tambah Kelas Binaan BK</h4>
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

            <form action="{{ route('admin.penugasan-bk.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="bk_user_id" class="form-label font-bold">Pilih Guru BK</label>
                    <select name="bk_user_id" id="bk_user_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Guru BK --</option>
                        @foreach($guruBks as $bk)
                            <option value="{{ $bk->id }}">{{ $bk->nama }} ({{ $bk->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-4">
                    <label for="kelas_id" class="form-label font-bold">Pilih Kelas Binaan</label>
                    <select name="kelas_id" id="kelas_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Penugasan</button>
                <a href="{{ route('admin.penugasan-bk.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</section>
@endsection