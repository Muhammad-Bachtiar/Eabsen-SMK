@extends('layouts.app')

@section('header_title', 'Dashboard Guru')

@section('content')
<div class="page-content">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body p-4">
                    <h4 class="text-white fw-bold">Selamat Datang, {{ $user->nama ?? $user->name }}!</h4>
                    <p class="mb-0">Selamat beraktivitas. Silakan kelola presensi siswa pada kelas dan mata pelajaran yang Anda ampu hari ini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Card Mazer Style -->
    <div class="row">
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-door-open-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Kelas Diampu</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalKelas }} Kelas</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-book-half text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Mata Pelajaran</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalMapel }} Mapel</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-clipboard-check-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Absen Hari Ini</h6>
                            <h6 class="font-extrabold mb-0">{{ $presensiHariIni }} Sesi</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Kelas & Mapel Diampu -->
        <div class="col-12 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kelas & Mapel Diampu</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($mapelDiampu as $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $item->nama_kelas }}</h6>
                                    <p class="mb-0 text-sm text-muted">{{ $item->nama_mapel }}</p>
                                </div>
                                <span class="badge bg-light-primary text-primary rounded-pill">Aktif</span>
                            </div>
                        @empty
                            <div class="text-center p-3 text-muted">Belum ada penugasan kelas/mapel.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Input Presensi Terakhir (Poin 5 & 6) -->
        <div class="col-12 col-xl-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Riwayat Input Presensi Terakhir</h4>
                    <a href="{{ route('guru.presensi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-lg">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPresensi as $presensi)
                                <tr>
                                    <td class="col-auto">
                                        <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="col-auto">
                                        <span class="badge bg-light-info text-info">{{ $presensi->kelas->nama_kelas ?? '-' }}</span>
                                    </td>
                                    <td class="col-auto">
                                        <p class="mb-0">{{ $presensi->mapel->nama_mapel ?? '-' }}</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada riwayat presensi yang diinput.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection