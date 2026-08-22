@extends('layouts.app')

@section('title', 'Penugasan Mengajar Guru - Admin')
@section('header_title', 'Penugasan Mengajar Guru')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Penugasan Mengajar</h5>
            <a href="{{ route('admin.penugasan.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Penugasan
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
                <table class="table table-striped table-hover datatable" id="tablePenugasan">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Nama Guru</th>
                            <th>NIP/NIK</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penugasans as $tugas)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-primary text-primary me-2">
                                        <span class="avatar-content">{{ strtoupper(substr($tugas->guru->nama ?? 'G', 0, 1)) }}</span>
                                    </div>
                                    <span class="fw-bold">{{ $tugas->guru->nama ?? 'Guru Dihapus' }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light-secondary text-secondary">{{ $tugas->guru->nip_nik ?? '-' }}</span></td>
                            <td><span class="badge bg-light-success text-success">{{ $tugas->mapel->nama_mapel ?? 'Mapel Dihapus' }}</span></td>
                            <td><span class="badge bg-light-info text-info">{{ $tugas->kelas->nama_kelas ?? 'Kelas Dihapus' }}</span></td>
                            <td>
                                <a href="{{ route('admin.penugasan.edit', $tugas->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                
                                <form action="{{ route('admin.penugasan.destroy', $tugas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cabut penugasan mengajar ini?')">
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