@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header border-secondary d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Presensi Mengajar</h5>
            <a href="{{ route('guru.presensi.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Input Presensi Baru
            </a>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="20%">Kelas</th>
                            <th width="25%">Mata Pelajaran</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPresensi as $index => $presensi)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="text-center">{{ $presensi->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $presensi->mapel->nama_mapel ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('guru.presensi.show', $presensi->id) }}" class="btn btn-info btn-sm text-white">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">
                                Belum ada riwayat presensi yang dicatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection