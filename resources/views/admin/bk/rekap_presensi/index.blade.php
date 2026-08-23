@extends('layouts.app')

@section('header_title', 'Rekap Presensi Gabungan')

@section('content')
<div class="page-heading mb-3">
    <h3>Rekap Presensi Kelas (Gabungan Mapel + BK)</h3>
    <p class="text-subtitle text-muted">Pantau gabungan riwayat presensi dari Guru Mapel maupun Guru BK per kelas</p>
</div>

<div class="page-content">
    <!-- Filter Kelas & Tanggal -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('bk.rekap_presensi.index') }}" class="row g-3 align-items-end">
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
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Sesi Presensi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kategori / Sesi</th>
                            <th>Mata Pelajaran / Sesi BK</th>
                            <th>Penginput (Guru / BK)</th>
                            <th class="text-center">Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensis as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if(($p->jenis ?? 'mapel') == 'bk')
                                    <span class="badge bg-info text-dark">Presensi BK</span>
                                @else
                                    <span class="badge bg-primary">Presensi Mapel</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $p->mapel->nama_mapel ?? 'Bimbingan Konseling' }}</strong>
                            </td>
                            <td>
                                <i class="bi bi-person-circle me-1 text-secondary"></i>
                                {{ $p->pencatat->nama ?? $p->pencatat->name ?? '-' }}
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }} WIB</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas presensi diisi pada kelas dan tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection