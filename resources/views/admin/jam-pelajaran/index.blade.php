@extends('layouts.app')

@section('title', 'Jam Pelajaran - Admin')
@section('header_title', 'Master Data Jam Pelajaran')

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Jam Pelajaran</h5>
            <a href="{{ route('admin.jam-pelajaran.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Tambah Jam
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
                <table class="table table-striped table-hover datatable" id="tableJam">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Jam Ke-</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jams as $jam)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-primary fs-6">Jam Ke-{{ $jam->jam_ke }}</span></td>
                            <td><span class="fw-semibold text-success"><i class="bi bi-play-circle me-1"></i> {{ \Carbon\Carbon::parse($jam->waktu_mulai)->format('H:i') }}</span></td>
                            <td><span class="fw-semibold text-danger"><i class="bi bi-stop-circle me-1"></i> {{ \Carbon\Carbon::parse($jam->waktu_selesai)->format('H:i') }}</span></td>
                            <td>
                                @php
                                    $mulai = \Carbon\Carbon::parse($jam->waktu_mulai);
                                    $selesai = \Carbon\Carbon::parse($jam->waktu_selesai);
                                    $durasi = $mulai->diffInMinutes($selesai);
                                @endphp
                                <span class="badge bg-light-secondary text-secondary">{{ $durasi }} Menit</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.jam-pelajaran.edit', $jam->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.jam-pelajaran.destroy', $jam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jam pelajaran ini?')">
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