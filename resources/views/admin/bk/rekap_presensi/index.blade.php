@extends('layouts.app')

@section('header_title', 'Rekap Presensi Kelas Binaan')

@section('content')

<div class="page-content">
    <!-- Filter Kelas Binaan & Tanggal -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bk.rekap_presensi.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Pilih Kelas Binaan:</label>
                    <select name="kelas_id" class="form-select">
                        @foreach($kelases as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tanggal Harian:</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $selectedTanggal }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan Rekap
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabulasi Navigasi: Harian vs Accumulation Semester -->
    <ul class="nav nav-tabs mb-3" id="bkRekapTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="harian-tab" data-bs-toggle="tab" data-bs-target="#tab-harian" type="button" role="tab">
                <i class="bi bi-calendar-check me-1"></i> Rekap Harian ({{ \Carbon\Carbon::parse($selectedTanggal)->translatedFormat('d M Y') }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="semester-tab" data-bs-toggle="tab" data-bs-target="#tab-semester" type="button" role="tab">
                <i class="bi bi-calculator me-1"></i> Akumulasi Semester
            </button>
        </li>
    </ul>

    <div class="tab-content" id="bkRekapTabContent">
        <!-- TAB 1: REKAP HARIAN -->
        <div class="tab-pane fade show active" id="tab-harian" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>NIS / NISN</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapHarian as $index => $r)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-light-secondary text-dark">{{ $r->nis }}</span></td>
                                    <td><strong>{{ $r->nama }}</strong></td>
                                    <td class="text-center">
                                        @if($r->status == 'Hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($r->status == 'Sakit')
                                            <span class="badge bg-warning text-dark">Sakit</span>
                                        @elseif($r->status == 'Izin')
                                            <span class="badge bg-info text-dark">Izin</span>
                                        @elseif($r->status == 'Alpha')
                                            <span class="badge bg-danger">Alpha ⚠️</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Diabsen</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data siswa di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: AKUMULASI SEMESTER -->
        <div class="tab-pane fade" id="tab-semester" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('bk.rekap_presensi.export_pdf', ['kelas_id' => $selectedKelas]) }}" class="btn btn-danger btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF Rekap Semester
                            </a>
                        </div>
                        @if($selectedKelasData)
                        <div class="alert alert-light-info border-info d-flex align-items-center mb-3">
                            <i class="bi bi-person-badge fs-3 me-3 text-info"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Wali Kelas: {{ $selectedKelasData->waliKelas->nama ?? 'Belum Ditentukan' }}</h6>
                                <small class="text-muted">NIP/NIK: {{ $selectedKelasData->waliKelas->nip_nik ?? '-' }}</small>
                            </div>
                        </div>
                        @endif
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center text-success">Hadir</th>
                                    <th class="text-center text-warning">Sakit</th>
                                    <th class="text-center text-info">Izin</th>
                                    <th class="text-center text-danger">Alpha</th>
                                    <th class="text-center">Tindakan BK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapSemester as $index => $s)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $s->nama }}</strong></td>
                                    <td class="text-center fw-bold text-success">{{ $s->hadir }}</td>
                                    <td class="text-center fw-bold text-warning">{{ $s->sakit }}</td>
                                    <td class="text-center fw-bold text-info">{{ $s->izin }}</td>
                                    <td class="text-center fw-bold text-danger">{{ $s->alpha }}</td>
                                    <td class="text-center">
                                        @if($s->alpha >= 3)
                                            <span class="badge bg-danger">⚠️ Perlu Pemanggilan</span>
                                        @else
                                            <span class="badge bg-light-success text-success">Aman</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data akumulasi presensi.</td>
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