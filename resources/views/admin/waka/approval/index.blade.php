@extends('layouts.app')

@section('header_title', 'Persetujuan Pelanggaran')

@section('content')
<div class="page-content">

    {{-- Nav Tabs --}}
    <ul class="nav nav-tabs mb-3" id="tabApproval" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-pending-btn" data-bs-toggle="tab" data-bs-target="#tab-pending" type="button">
                <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan
                <span class="badge bg-warning text-dark ms-1">{{ $pelanggarans->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-riwayat-btn" data-bs-toggle="tab" data-bs-target="#tab-riwayat" type="button">
                <i class="bi bi-clock-history me-1"></i> Riwayat Persetujuan
                <span class="badge bg-secondary ms-1">{{ $riwayat->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="tabApprovalContent">

        {{-- Tab 1: Menunggu Persetujuan --}}
        <div class="tab-pane fade show active" id="tab-pending" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Deskripsi</th>
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
                                        @php
                                            $prosesRoute = (Auth::user()->is_koordinator_bk ?? false) ? 'koordinator-bk.approval.proses' : 'waka.approval.proses';
                                        @endphp
                                        <form action="{{ route($prosesRoute, $p->id) }}" method="POST"> 
                                            @csrf
                                            <div class="mb-2">
                                                <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Tulis catatan keputusan..." required></textarea>
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
                                    <td colspan="7" class="text-center text-muted">Tidak ada laporan yang menunggu persetujuan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 2: Riwayat Persetujuan --}}
        <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">

            {{-- Filter --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('waka.approval.index') }}" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="riwayat">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Status</label>
                            <select name="filter_status" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="disetujui" {{ request('filter_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('filter_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="selesai" {{ request('filter_status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Disetujui Oleh</label>
                            <select name="filter_penyetuju" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                @foreach($penyetujus as $u)
                                    <option value="{{ $u->id }}" {{ request('filter_penyetuju') == $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('waka.approval.index', ['tab' => 'riwayat']) }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Kejadian</th>
                                    <th>Nama Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                    <th>Disetujui Oleh</th>
                                    <th>Tanggal Persetujuan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $r)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($r->tanggal_kejadian)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <strong>{{ $r->siswa->nama ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $r->siswa->kelas->nama_kelas ?? '-' }}</small>
                                    </td>
                                    <td>{{ $r->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                                    <td><span class="badge bg-danger">{{ $r->poin }}</span></td>
                                    <td>
                                        @if($r->status === 'disetujui')
                                            <span class="badge bg-primary"><i class="bi bi-check"></i> Disetujui</span>
                                        @elseif($r->status === 'ditolak')
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Ditolak</span>
                                        @elseif($r->status === 'selesai')
                                            <span class="badge bg-success"><i class="bi bi-check-double"></i> Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->penyetuju->nama ?? '-' }}</td>
                                    <td>{{ $r->tanggal_persetujuan ? \Carbon\Carbon::parse($r->tanggal_persetujuan)->translatedFormat('d M Y, H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('waka.pelanggaran.detail', $r->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada riwayat persetujuan.</td>
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