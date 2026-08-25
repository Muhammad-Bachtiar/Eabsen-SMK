@extends('layouts.app')

@section('header_title', 'Dashboard Waka Kesiswaan')

@section('content')
<div class="page-heading mb-3">
    <h3>Dashboard Waka Kesiswaan</h3>
    <p class="text-subtitle text-muted">Pemantauan Presensi Harian & Persetujuan Pelanggaran Siswa</p>
</div>

<div class="page-content">
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon purple me-3"><i class="bi bi-door-open-fill"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold">Total Kelas</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalKelas }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon blue me-3"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold">Kelas Absen Hari Ini</h6>
                            <h6 class="font-extrabold mb-0">{{ $kelasAbsenHariIni }} / {{ $totalKelas }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon green me-3"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold">Total Siswa</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalSiswa }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon red me-3"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold">Butuh Approval</h6>
                            <h6 class="font-extrabold mb-0">{{ $pelanggaranPending }} Pelanggaran</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Presensi Harian -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Pantauan Presensi Kelas Hari Ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }})</h4>
            <a href="{{ route('waka.rekap_presensi.index') }}" class="btn btn-primary btn-sm">Lihat Detail Rekap</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th class="text-center text-success">Hadir</th>
                            <th class="text-center text-info">Izin</th>
                            <th class="text-center text-warning">Sakit</th>
                            <th class="text-center text-danger">Alpha</th>
                            <th class="text-center">Status Absen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapPresensi as $r)
                        <tr>
                            <td><strong>{{ $r->nama_kelas }}</strong></td>
                            <td>{{ $r->nama_jurusan }}</td>
                            <td class="text-center fw-bold text-success">{{ $r->hadir }}</td>
                            <td class="text-center fw-bold text-info">{{ $r->izin }}</td>
                            <td class="text-center fw-bold text-warning">{{ $r->sakit }}</td>
                            <td class="text-center fw-bold text-danger">{{ $r->alpha }}</td>
                            <td class="text-center">
                                @if($r->sudah_diisi)
                                    <span class="badge bg-success">Sudah Diisi</span>
                                @else
                                    <span class="badge bg-secondary">Belum Diisi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection