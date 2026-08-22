@extends('layouts.app')

@section('title', 'Penugasan Guru BK - Admin')
@section('header_title', 'Penugasan Guru BK Kelas Binaan')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Kelas Binaan Guru BK</h5>
            <a href="{{ route('admin.penugasan-bk.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Penugasan BK
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover datatable" id="tablePenugasanBk">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Nama Guru BK</th>
                            <th>Email BK</th>
                            <th>Kelas Binaan</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penugasans as $tugas)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-info text-info me-2">
                                        <span class="avatar-content">{{ strtoupper(substr($tugas->bkUser->nama ?? 'B', 0, 1)) }}</span>
                                    </div>
                                    <span class="fw-bold">{{ $tugas->bkUser->nama ?? 'Akun BK Dihapus' }}</span>
                                </div>
                            </td>
                            <td>{{ $tugas->bkUser->email ?? '-' }}</td>
                            <td><span class="badge bg-light-primary text-primary">{{ $tugas->kelas->nama_kelas ?? 'Kelas Dihapus' }}</span></td>
                            <td>
                                <form action="{{ route('admin.penugasan-bk.destroy', $tugas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cabut penugasan kelas ini?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection