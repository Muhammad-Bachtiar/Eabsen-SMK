@extends('layouts.app')

@section('title', 'Dashboard Administrator - E-Absen SMK')
@section('header_title', 'Dashboard Administrator')

@push('styles')
<style>
    /* Stats Icon - pastikan icon berada di dalam kotak/lingkaran */
    .stats-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .stats-icon i {
        font-size: 1.3rem;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
    }

    .stats-icon.purple {
        background-color: rgb(115, 103, 240);
        color: #fff;
    }

    .stats-icon.blue {
        background-color: rgb(67, 94, 190);
        color: #fff;
    }

    .stats-icon.green {
        background-color: rgb(38, 191, 165);
        color: #fff;
    }

    .stats-icon.red {
        background-color: rgb(255, 71, 87);
        color: #fff;
    }

    /* Avatar icon di baris kedua */
    .avatar.avatar-md {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .avatar.avatar-md i {
        font-size: 1.1rem;
        line-height: 1;
    }

    /* Profile card - simetris dengan card lain */
    .profile-card .avatar.avatar-xl {
        width: 64px;
        height: 64px;
        min-width: 64px;
    }
</style>
@endpush

@section('content')
<section class="row">
    <!-- Kolom Kiri: Metrik Utama, Grafik & Tabel Aktivitas -->
    <div class="col-12 col-lg-9">
        <!-- 1. Row Kartu Statistik Mazer Baris 1 -->
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm hover-elevate">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Siswa</h6>
                                <h4 class="font-extrabold mb-0">{{ number_format($totalSiswa) }}</h4>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <a href="{{ route('admin.siswa.index') }}" class="text-sm text-primary text-decoration-none fw-semibold">
                                Kelola <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm hover-elevate">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Guru</h6>
                                <h4 class="font-extrabold mb-0">{{ number_format($totalGuru) }}</h4>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <a href="{{ route('admin.guru.index') }}" class="text-sm text-primary text-decoration-none fw-semibold">
                                Kelola <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm hover-elevate">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-door-open-fill fs-4 text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Kelas</h6>
                                <h4 class="font-extrabold mb-0">{{ number_format($totalKelas) }}</h4>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <a href="{{ route('admin.kelas.index') }}" class="text-sm text-primary text-decoration-none fw-semibold">
                                Kelola <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm hover-elevate">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-book-half"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Mata Pelajaran</h6>
                                <h4 class="font-extrabold mb-0">{{ number_format($totalMapel) }}</h4>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <a href="{{ route('admin.mapel.index') }}" class="text-sm text-primary text-decoration-none fw-semibold">
                                Kelola <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Row Kartu Statistik Mazer Baris 2 -->
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm bg-light-primary border border-primary border-opacity-25">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-primary text-white me-3">
                                <i class="bi bi-mortarboard-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold mb-0" style="font-size: 0.8rem;">Tugas Mengajar</h6>
                                <h5 class="font-extrabold mb-0 text-primary">{{ $totalPenugasan }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm bg-light-info border border-info border-opacity-25">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-info text-white me-3">
                                <i class="bi bi-person-check-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold mb-0" style="font-size: 0.8rem;">Tugas BK Kelas</h6>
                                <h5 class="font-extrabold mb-0 text-info">{{ $totalPenugasanBk }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm bg-light-success border border-success border-opacity-25">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-success text-white me-3">
                                <i class="bi bi-clock-history fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold mb-0" style="font-size: 0.8rem;">Jam Pelajaran</h6>
                                <h5 class="font-extrabold mb-0 text-success">{{ $totalJamPelajaran }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm bg-light-warning border border-warning border-opacity-25">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-warning text-white me-3">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold mb-0" style="font-size: 0.8rem;">Jenis Pelanggaran</h6>
                                <h5 class="font-extrabold mb-0 text-warning">{{ $totalJenisPelanggaran }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Grafik Aktivitas & Presensi ApexCharts -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Statistik Rekapitulasi Presensi</h4>
                            <p class="text-muted text-sm mb-0">Grafik frekuensi kehadiran dan aktivitas kelas harian</p>
                        </div>
                        <span class="badge bg-primary">Tahun Ajaran 2025/2026</span>
                    </div>
                    <div class="card-body">
                        <div id="chart-profile-visit"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Tabel Siswa Terbaru & Penugasan Terbaru -->
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Siswa Terbaru</h5>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>JK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswasTerbaru as $siswa)
                                    <tr>
                                        <td class="col-6">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-md bg-light-primary text-primary me-3">
                                                    <span class="avatar-content fw-bold">{{ strtoupper(substr($siswa->nama, 0, 2)) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-bold mb-0 text-truncate" style="max-width: 150px;">{{ $siswa->nama }}</p>
                                                    <small class="text-muted">NIS: {{ $siswa->nis }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-3">
                                            <span class="badge bg-light-info text-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                        </td>
                                        <td class="col-3">
                                            @if($siswa->jenis_kelamin == 'L')
                                                <span class="badge bg-light-primary text-primary">Laki-laki</span>
                                            @else
                                                <span class="badge bg-light-danger text-danger">Perempuan</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada data siswa.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Penugasan Mengajar Terbaru</h5>
                        <a href="{{ route('admin.penugasan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg mb-0">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Mapel</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($penugasansTerbaru as $penugasan)
                                    <tr>
                                        <td class="col-5">
                                            <p class="font-bold mb-0 text-truncate" style="max-width: 130px;">{{ $penugasan->guru->nama ?? 'Guru Dihapus' }}</p>
                                            <small class="text-muted">{{ $penugasan->guru->nip_nik ?? '-' }}</small>
                                        </td>
                                        <td class="col-4">
                                            <span class="badge bg-light-success text-success">{{ $penugasan->mapel->nama_mapel ?? '-' }}</span>
                                        </td>
                                        <td class="col-3">
                                            <span class="badge bg-light-secondary text-secondary">{{ $penugasan->kelas->nama_kelas ?? '-' }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada data penugasan.</td>
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

    <!-- Kolom Kanan: Profil, Donut Chart Gender, Jam Pelajaran & Quick Menu -->
    <div class="col-12 col-lg-3">
        <!-- Profil Card Mazer -->
        <div class="card shadow-sm profile-card">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle shadow" style="width: 64px; height: 64px; min-width: 64px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-badge" style="font-size: 1.8rem; line-height: 1;"></i>
                    </div>
                    <div class="ms-3 name">
                        <h5 class="font-bold mb-0 text-truncate" style="max-width: 140px;">{{ $user->nama ?? $user->name ?? 'Admin E-Absen' }}</h5>
                        <span class="badge bg-success rounded-pill mt-1">{{ strtoupper($user->role->nama_role ?? 'ADMIN') }}</span>
                    </div>
                </div>
                <hr>
                <div class="text-muted text-sm">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Email:</span>
                        <span class="fw-semibold text-truncate" style="max-width: 130px;">{{ $user->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status:</span>
                        <span class="badge bg-light-success text-success">Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart Gender Siswa -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Komposisi Siswa</h5>
                <small class="text-muted">Perbandingan Laki-laki & Perempuan</small>
            </div>
            <div class="card-body">
                <div id="chart-visitors-profile"></div>
            </div>
        </div>

        <!-- Ringkasan Jam Pelajaran Aktif -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center pb-2">
                <h5 class="card-title mb-0">Jadwal Jam</h5>
                <a href="{{ route('admin.jam-pelajaran.index') }}" class="btn btn-sm btn-link text-primary p-0">Atur</a>
            </div>
            <div class="card-body pt-2">
                <div class="list-group list-group-flush">
                    @forelse($jamsList->take(4) as $jam)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">{{ $jam->jam_ke }}</span>
                            <span class="fw-semibold text-sm">Jam Ke-{{ $jam->jam_ke }}</span>
                        </div>
                        <span class="badge bg-light-secondary text-secondary text-sm">
                            {{ \Carbon\Carbon::parse($jam->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->waktu_selesai)->format('H:i') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted text-sm mb-0">Belum ada jam pelajaran.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Menu Pintas Cepat (Quick Actions) -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Akses Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.siswa.create') }}" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah Siswa
                    </a>
                    <a href="{{ route('admin.guru.create') }}" class="btn btn-outline-info btn-sm text-start">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah Akun Guru
                    </a>
                    <a href="{{ route('admin.penugasan.create') }}" class="btn btn-outline-success btn-sm text-start">
                        <i class="bi bi-mortarboard-fill me-2"></i> Buat Penugasan
                    </a>
                    <a href="{{ route('admin.jenis-pelanggaran.create') }}" class="btn btn-outline-warning btn-sm text-start">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Input Jenis Pelanggaran
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Grafik Batang Rekap Presensi
        var optionsProfileVisit = {
            annotations: { position: "back" },
            dataLabels: { enabled: false },
            chart: {
                type: "bar",
                height: 280,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: "45%",
                }
            },
            series: [{
                name: "Kehadiran (Absensi)",
                data: [45, 62, 58, 75, 80, 85, 92, 88, 95, 90, 86, 94]
            }],
            colors: ["#435ebe"],
            xaxis: {
                categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"]
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val + " %";
                    }
                }
            }
        };

        var chartProfileVisit = new ApexCharts(
            document.querySelector("#chart-profile-visit"),
            optionsProfileVisit
        );
        chartProfileVisit.render();

        // 2. Grafik Donut Gender Siswa
        var lakiCount = {{ $genderLaki ?? 0 }};
        var perempuanCount = {{ $genderPerempuan ?? 0 }};
        if (lakiCount === 0 && perempuanCount === 0) {
            lakiCount = 1;
            perempuanCount = 1;
        }

        var optionsVisitorsProfile = {
            series: [lakiCount, perempuanCount],
            labels: ["Laki-laki ({{ $genderLaki }})", "Perempuan ({{ $genderPerempuan }})"],
            colors: ["#435ebe", "#55c6e8"],
            chart: {
                type: "donut",
                width: "100%",
                height: 240
            },
            legend: {
                position: "bottom"
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "45%"
                    }
                }
            }
        };

        var chartVisitorsProfile = new ApexCharts(
            document.getElementById("chart-visitors-profile"),
            optionsVisitorsProfile
        );
        chartVisitorsProfile.render();
    });
</script>
@endpush