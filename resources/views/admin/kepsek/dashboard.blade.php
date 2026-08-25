@extends('layouts.app')

@section('header_title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="page-heading mb-3">
    <h3>Dashboard Ringkasan Eksekutif</h3>
    <p class="text-subtitle text-muted">Laporan Eksekutif Presensi & Kedisiplinan Siswa</p>
</div>

<div class="page-content">
    <!-- Stat Cards Summary Hari Ini -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-light-success border-success">
                <div class="card-body px-4 py-3">
                    <h6 class="text-dark font-semibold">Total Siswa Hadir</h6>
                    <h3 class="font-extrabold text-success mb-0">{{ $totalHadir }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light-info border-info">
                <div class="card-body px-4 py-3">
                    <h6 class="text-dark font-semibold">Siswa Izin Hari Ini</h6>
                    <h3 class="font-extrabold text-info mb-0">{{ $totalIzin }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light-warning border-warning">
                <div class="card-body px-4 py-3">
                    <h6 class="text-dark font-semibold">Siswa Sakit Hari Ini</h6>
                    <h3 class="font-extrabold text-warning mb-0">{{ $totalSakit }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light-danger border-danger">
                <div class="card-body px-4 py-3">
                    <h6 class="text-dark font-semibold">Total Alpha Hari Ini</h6>
                    <h3 class="font-extrabold text-danger mb-0">{{ $totalAlpha }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Kelas Belum Absen Hari Ini -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-danger">⚠️ Kelas Belum Melakukan Absensi Hari Ini</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%" class="text-dark">No</th>
                                    <th class="text-dark">Nama Kelas</th>
                                    <th class="text-dark">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelasBelumAbsen as $idx => $k)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td><strong>{{ $k->nama_kelas }}</strong></td>
                                    <td><span class="badge bg-warning text-dark">Belum Diisi Guru</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-success py-3">
                                        <i class="bi bi-check-circle me-1"></i> Semua kelas telah melakukan presensi hari ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Kedisiplinan -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ringkasan Kedisiplinan</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-light-secondary border d-flex align-items-center mb-3">
                        <i class="bi bi-shield-exclamation fs-2 text-danger me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Pelanggaran Bulan Ini</h6>
                            <small class="text-dark">{{ $pelanggaranBulanIni }} Kasus Terdaftar</small>
                        </div>
                    </div>
                    <div class="alert alert-light-primary border d-flex align-items-center">
                        <i class="bi bi-building fs-2 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Total Rombongan Belajar</h6>
                            <small class="text-dark">{{ $totalKelas }} Kelas Aktif ({{ $totalSiswa }} Siswa)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection