@extends('layouts.app')

@section('title', 'Master Data Jenis Pelanggaran - Admin')
@section('header_title', 'Master Data Jenis Pelanggaran')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Jenis Pelanggaran</h5>
            <a href="{{ route('admin.jenis-pelanggaran.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pelanggaran
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
                <table class="table table-striped table-hover datatable" id="tableJenisPelanggaran">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Nama Pelanggaran</th>
                            <th>Kategori</th>
                            <th>Poin</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggarans as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="fw-bold">{{ $p->nama_pelanggaran }}</span></td>
                            <td>
                                @if($p->kategori == 'ringan')
                                    <span class="badge bg-success">Ringan</span>
                                @elseif($p->kategori == 'sedang')
                                    <span class="badge bg-warning text-dark">Sedang</span>
                                @else
                                    <span class="badge bg-danger">Berat</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $p->poin }} Poin</span></td>
                            <td>
                                <a href="{{ route('admin.jenis-pelanggaran.edit', $p->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.jenis-pelanggaran.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
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
