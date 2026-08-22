@extends('layouts.app')

@section('title', 'Data Guru - Admin')
@section('header_title', 'Master Data Guru')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">Daftar Akun Guru</h5>
            
            <div class="d-flex gap-2">
                <!-- Tombol Download Template -->
                <a href="{{ route('admin.guru.download-template') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> Template
                </a>
                
                <!-- Tombol Modal Upload -->
                <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#importGuruModal">
                    <i class="bi bi-upload me-1"></i> Import
                </button>

                <!-- Tombol Tambah Manual -->
                <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Guru
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover datatable" id="tableGuru">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 20%">NIP / NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Email Login</th>
                            <th>Status</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $guru)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light-secondary text-secondary">{{ $guru->nip_nik ?? '-' }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-primary text-primary me-2">
                                        <span class="avatar-content">{{ strtoupper(substr($guru->nama, 0, 1)) }}</span>
                                    </div>
                                    <span class="fw-bold">{{ $guru->nama }}</span>
                                </div>
                            </td>
                            <td>{{ $guru->email }}</td>
                            <td>
                                <span class="badge bg-success">Aktif</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.guru.edit', $guru->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                
                                <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data akun guru ini?')">
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

    <!-- Modal Import Guru -->
    <div class="modal fade" id="importGuruModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-bold">Pilih File Excel (.xlsx / .csv)</label>
                            <input class="form-control" type="file" name="file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle me-1"></i> Gunakan format kolom dari file template yang telah diunduh.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection