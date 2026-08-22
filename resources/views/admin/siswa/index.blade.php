@extends('layouts.app')

@section('title', 'Data Siswa - Admin')
@section('header_title', 'Master Data Siswa')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">Daftar Siswa</h5>
            
            <!-- Kumpulan Tombol Aksi -->
            <div class="d-flex gap-2">
                <!-- Tombol Download Template -->
                <a href="{{ route('admin.siswa.download-template') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> Download Template
                </a>
                
                <!-- Tombol Buka Modal Upload -->
                <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload me-1"></i> Import Excel
                </button>
                
                <!-- Tombol Tambah Manual -->
                <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
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
                <table class="table table-striped table-hover datatable" id="tableSiswa">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 15%">NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Status</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $siswa)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-light-secondary text-secondary">{{ $siswa->nis }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light-primary text-primary me-2">
                                        <span class="avatar-content">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</span>
                                    </div>
                                    <span class="fw-bold">{{ $siswa->nama }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light-info text-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                            </td>
                            <td>
                                @if($siswa->jenis_kelamin == 'L')
                                    <span class="badge bg-light-primary text-primary">Laki-laki</span>
                                @else
                                    <span class="badge bg-light-danger text-danger">Perempuan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($siswa->status ?? 'aktif') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
        
                                <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?')">
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

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label font-bold">Pilih File Excel / CSV (.xlsx, .xls, .csv)</label>
                            <input class="form-control" type="file" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle me-1"></i> Pastikan format kolom sesuai dengan template yang di-download.
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