@extends('layouts.app')

@section('header_title', 'Rekap Presensi Harian Siswa (Kepala Sekolah)')
@section('content')

<div class="page-content">
    <section class="row">
        <div class="col-12">
            <!-- Filter Tanggal -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('kepsek.rekap_presensi.index') }}" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="col-form-label fw-bold">Pilih Tanggal:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Rekap -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kelas</th>
                                    <th>Jurusan</th>
                                    <th class="text-center text-success">Hadir</th>
                                    <th class="text-center text-info">Izin</th>
                                    <th class="text-center text-warning">Sakit</th>
                                    <th class="text-center text-danger">Alpha</th>
                                    <th class="text-center">Total Terdata</th>
                                    <th class="text-center">Status Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapKelas as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $row->nama_kelas }}</strong></td>
                                <td>{{ $row->nama_jurusan }}</td>
                                <td class="text-center fw-bold text-success">{{ $row->hadir }}</td>
                                <td class="text-center fw-bold text-info">{{ $row->izin }}</td>
                                <td class="text-center fw-bold text-warning">{{ $row->sakit }}</td>
                                <td class="text-center fw-bold text-danger">{{ $row->alpha }}</td>
                                <td class="text-center fw-bold">{{ $row->total_terdata }}</td>
                                <td class="text-center">
                                    @if($row->sudah_diisi)
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
    </section>
</div>
@endsection