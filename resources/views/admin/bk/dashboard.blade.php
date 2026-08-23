@extends('layouts.app')

@section('header_title', 'Dashboard Bimbingan Konseling')

@section('content')
<div class="page-content">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body p-4">
                    <h4 class="text-white fw-bold">Panel Bimbingan & Konseling (BK)</h4>
                    <p class="mb-0">Selamat datang, {{ $user->nama ?? $user->name }}. Pantau kedisiplinan, rekap poin pelanggaran, dan tindak lanjut siswa di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Statistik Mazer -->
    <div class="row">
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Pelanggaran</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalPelanggaran }} Kasus</h6>
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
                            <div class="stats-icon orange mb-2">
                                <i class="bi bi-hourglass-split text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Menunggu Approval</h6>
                            <h6 class="font-extrabold mb-0">{{ $kasusMenunggu }} Kasus</h6>
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
                                <i class="bi bi-check-circle-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Disetujui / Selesai</h6>
                            <h6 class="font-extrabold mb-0">{{ $kasusSelesai }} Kasus</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Tengah: Kelas Binaan & Alpha Monitor -->
    <div class="row">
        <!-- Widget Kelas Binaan -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-people-fill text-primary me-2"></i>Kelas Binaan Saya
                    </h4>
                    <span class="badge bg-light-primary text-primary fs-6">{{ $totalSiswaBinaan }} Siswa</span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse($kelasBinaan as $kb)
                            <div class="col-6 col-md-4">
                                <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $kb->nama_kelas }}</h6>
                                        <small class="text-muted">Binaan</small>
                                    </div>
                                    <span class="badge bg-primary rounded-circle p-1"><i class="bi bi-check-lg"></i></span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                Belum ada data penugasan kelas binaan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Siswa Sering Alpha -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="bi bi-bell-fill text-danger me-2"></i>Pantauan Siswa Sering Alpha</h4>
                    <p class="text-subtitle text-muted mb-0">Deteksi dini siswa dengan akumulasi Alpha terbanyak</p>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($topAlpha as $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $item->siswa->nama ?? '-' }}</h6>
                                    <small class="text-muted">{{ $item->siswa->kelas->nama_kelas ?? 'Kelas -' }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill">{{ $item->total_alpha }}x Alpha</span>
                            </div>
                        @empty
                            <div class="text-center p-3 text-muted">Tidak ada siswa dengan catatan Alpha.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Bawah: Riwayat Pelanggaran Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Pelanggaran Siswa Terbaru</h4>
                    <a href="{{ route('bk.pelanggaran.index') }}" class="btn btn-sm btn-outline-info">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th class="text-center">Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggaranTerbaru as $p)
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-bold">{{ $p->siswa->nama ?? '-' }}</p>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-danger text-danger fw-bold">+{{ $p->poin }}</span>
                                    </td>
                                    <td>
                                        @if($p->status == 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($p->status == 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada catatan pelanggaran siswa.</td>
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