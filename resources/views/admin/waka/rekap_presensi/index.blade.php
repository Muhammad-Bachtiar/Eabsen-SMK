@extends('layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center mb-3">
    <h3>Rekap Presensi Harian Siswa</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12">
            <!-- Filter Tanggal -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('waka.rekap_presensi.index') }}" class="row g-3 align-items-center">
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
                                @forelse($rekapData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $row['kelas'] }}</strong></td>
                                    <td>{{ $row['jurusan'] }}</td>
                                    <td class="text-center"><span class="badge bg-success">{{ $row['hadir'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-info">{{ $row['izin'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-warning">{{ $row['sakit'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-danger">{{ $row['alpha'] }}</span></td>
                                    <td class="text-center fw-bold">{{ $row['total_siswa'] }}</td>
                                    <td class="text-center">
                                        @if($row['status_input'] == 'Sudah Diisi')
                                            <span class="badge bg-light-success text-success"><i class="fas fa-check-circle"></i> Sudah Diisi</span>
                                        @else
                                            <span class="badge bg-light-secondary text-secondary"><i class="fas fa-clock"></i> Belum Diisi</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Data kelas tidak ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection