@extends('layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center">
    <h3>Data Pelanggaran Siswa</h3>
    <a href="{{ route('bk.pelanggaran.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Catat Pelanggaran
    </a>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggarans as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal_kejadian)->translatedFormat('d M Y') }}</td>
                                    <td>{{ $p->siswa->nama ?? '-' }}</td>
                                    <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                    <td><span class="badge bg-danger">{{ $p->poin }}</span></td>
                                    <td>
                                        @if($p->status == 'menunggu_persetujuan')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu Persetujuan</span>
                                        @elseif($p->status == 'disetujui')
                                            <span class="badge bg-primary"><i class="fas fa-check"></i> Disetujui</span>
                                        @elseif($p->status == 'ditolak')
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Ditolak</span>
                                        @elseif($p->status == 'selesai')
                                            <span class="badge bg-success"><i class="fas fa-check-double"></i> Selesai</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $p->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data pelanggaran yang dicatat.</td>
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