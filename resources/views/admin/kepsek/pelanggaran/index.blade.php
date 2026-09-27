@extends('layouts.app')
@section('header_title', 'Ringkasan Pelanggaran Siswa (Kepala Sekolah)')
@section('content')
<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle datatable">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Status Approval</th>
                                    <th>Tindak Lanjut / Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggarans as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal_kejadian)->translatedFormat('d M Y') }}</td>
                                    <td><strong>{{ $p->siswa->nama ?? '-' }}</strong></td>
                                    <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                    <td><span class="badge bg-danger">{{ $p->poin }} Poin</span></td>
                                    <td>
                                        @if($p->status == 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($p->status == 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $p->rencana_tindak_lanjut ?? '-' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada riwayat pelanggaran.</td>
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