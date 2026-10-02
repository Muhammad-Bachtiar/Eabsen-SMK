@extends('layouts.app')

@section('header_title', 'Presensi Kelas Binaan')

@section('content')
<div class="page-heading mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('bk.presensi.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Input Presensi BK
    </a>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle {{ $riwayat->isNotEmpty() ? 'datatable' : '' }}">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Kelas Binaan</th>
                            <th>Tipe Presensi</th>
                            <th>Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</td>
                            <td><strong>{{ $r->kelas->nama_kelas ?? '-' }}</strong></td>
                            <td><span class="badge bg-info text-dark">Bimbingan Konseling (BK)</span></td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }} WIB</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat presensi BK yang diinput.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection