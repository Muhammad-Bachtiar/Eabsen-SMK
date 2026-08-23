@extends('layouts.app')

@section('header_title', 'Rekap Presensi Kelas')

@section('content')
<div class="page-content">
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('guru.rekap_kelas.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Pilih Kelas:</label>
                    <select name="kelas_id" class="form-select">
                        @foreach($kelases as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $selectedTanggal }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Timeline Sesi Presensi Hari Ini -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Aktivitas Absensi Kelas</h4>
            <p class="text-subtitle text-muted mb-0">Daftar guru yang telah mengabsen pada tanggal ini</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Jam Pelajaran</th>
                            <th>Mata Pelajaran</th>
                            <th>Diisi Oleh (Guru)</th>
                            <th class="text-center">Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSesi as $index => $sesi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @foreach($sesi->presensiJams as $pj)
                                    <span class="badge bg-light-primary text-primary">Jam {{ $pj->jamPelajaran->jam_ke ?? '-' }}</span>
                                @endforeach
                            </td>
                            <td><strong>{{ $sesi->mapel->nama_mapel ?? '-' }}</strong></td>
                            <td>
                                <i class="bi bi-person-circle me-1 text-secondary"></i>
                                {{ $sesi->pencatat->nama ?? $sesi->pencatat->name ?? '-' }}
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($sesi->created_at)->format('H:i') }} WIB</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas presensi diisi untuk kelas dan tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection