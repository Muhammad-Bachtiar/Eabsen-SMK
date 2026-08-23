@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Persetujuan Pelanggaran Siswa</h3>
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
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Deskripsi Kejadian</th>
                                    <th>Poin</th>
                                    <th width="25%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggarans as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal_kejadian)->translatedFormat('d M Y') }}</td>
                                    <td><strong>{{ $p->siswa->nama ?? '-' }}</strong></td>
                                    <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                    <td><small>{{ $p->deskripsi }}</small></td>
                                    <td><span class="badge bg-danger">{{ $p->poin }} Poin</span></td>
                                    <td>
                                        <!-- Form Eksekusi Approval -->
                                        <form action="{{ route('waka.approval.proses', $p->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-2">
                                                <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan tindak lanjut / keputusan..." required></textarea>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button type="submit" name="status" value="disetujui" class="btn btn-success btn-sm" onclick="return confirm('Setujui laporan ini?')">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                                <button type="submit" name="status" value="ditolak" class="btn btn-danger btn-sm" onclick="return confirm('Tolak laporan ini?')">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Saat ini tidak ada laporan pelanggaran yang menunggu persetujuan.</td>
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