@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Detail Presensi</h5>
            <a href="{{ route('guru.presensi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="30%">Tanggal</th>
                            <td>: {{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>: {{ $presensi->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <td>: {{ $presensi->mapel->nama_mapel ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jam Pelajaran</th>
                            <td>: Jam ke-{{ implode(', ', $jams) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Daftar Kehadiran Siswa</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">NIS</th>
                            <th width="50%">Nama Siswa</th>
                            <th width="30%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $detail->siswa->nis ?? '-' }}</td>
                            <td>{{ $detail->siswa->nama ?? '-' }}</td>
                            <td class="text-center">
                                @if($detail->status == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($detail->status == 'sakit')
                                    <span class="badge bg-warning">Sakit</span>
                                @elseif($detail->status == 'izin')
                                    <span class="badge bg-primary">Izin</span>
                                @else
                                    <span class="badge bg-danger">Alpa</span>
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