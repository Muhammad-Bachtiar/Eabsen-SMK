@extends('layouts.app')

@section('header_title', 'Detail Pelanggaran Siswa')

@section('content')
<div class="page-content">

    <div class="row">
        <div class="col-lg-10 col-xl-9 mx-auto">

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('bk.pelanggaran.cetak_pdf', $pelanggaran->id) }}"
                       class="btn btn-sm btn-danger" target="_blank">
                        <i class="bi bi-file-pdf me-1"></i> Cetak PDF
                    </a>
                    @if($pelanggaran->status === 'disetujui')
                    <form action="{{ route('bk.pelanggaran.selesaikan', $pelanggaran->id) }}"
                          method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-success"
                                onclick="return confirm('Tandai pelanggaran ini selesai?')">
                            <i class="bi bi-check-double me-1"></i> Tandai Selesai
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Card 1: Info Siswa --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-person-fill me-1"></i> Informasi Siswa</h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Nama</span>
                                <span class="fw-bold">: {{ $pelanggaran->siswa->nama ?? '-' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">NIS / NISN</span>
                                <span>: {{ $pelanggaran->siswa->nis ?? $pelanggaran->siswa->nisn ?? '-' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Kelas</span>
                                <span>: {{ $pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Wali Kelas</span>
                                <span>: {{ $pelanggaran->siswa->kelas->waliKelas->nama ?? $pelanggaran->siswa->kelas->waliKelas->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Dicatat Oleh</span>
                                <span>: {{ $pelanggaran->pencatat->nama ?? $pelanggaran->pencatat->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Tanggal</span>
                                <span>: {{ \Carbon\Carbon::parse($pelanggaran->tanggal_kejadian)->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Status</span>
                                <span>:
                                    @if($pelanggaran->status === 'menunggu_persetujuan')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Menunggu Persetujuan</span>
                                    @elseif($pelanggaran->status === 'disetujui')
                                        <span class="badge bg-primary"><i class="bi bi-check"></i> Disetujui</span>
                                    @elseif($pelanggaran->status === 'ditolak')
                                        <span class="badge bg-danger"><i class="bi bi-x"></i> Ditolak</span>
                                    @elseif($pelanggaran->status === 'selesai')
                                        <span class="badge bg-success"><i class="bi bi-check-double"></i> Selesai</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Detail Pelanggaran --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> Detail Pelanggaran</h6>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small mb-1">Jenis Pelanggaran</div>
                            <div class="fw-bold">{{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</div>
                            <small class="text-muted">Kategori: {{ ucfirst($pelanggaran->jenisPelanggaran->kategori ?? '-') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small mb-1">Poin</div>
                            <span class="badge bg-danger fs-6">{{ $pelanggaran->poin }} Poin</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="text-muted small mb-1">Deskripsi Kejadian</div>
                    <p class="mb-0 small">{{ $pelanggaran->deskripsi }}</p>
                </div>
            </div>

            {{-- Card 3: Rencana Tindak Lanjut BK --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check me-1"></i> Rencana Tindak Lanjut (BK)</h6>
                </div>
                <div class="card-body py-3">
                    @if($pelanggaran->rencana_tindak_lanjut)
                        <p class="mb-0 small">{{ $pelanggaran->rencana_tindak_lanjut }}</p>
                    @else
                        <p class="text-muted fst-italic small mb-0">Belum ada rencana tindak lanjut.</p>
                    @endif
                </div>
            </div>

            {{-- Card 4: Riwayat Persetujuan (Timeline) --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Riwayat Persetujuan & Tindak Lanjut</h6>
                </div>
                <div class="card-body py-3">
                    @if($riwayat->isEmpty())
                        <p class="text-muted fst-italic small mb-0">Belum ada riwayat persetujuan untuk pelanggaran ini.</p>
                    @else
                        @foreach($riwayat as $r)
                            @php
                                $badgeClass = match($r->status_baru) {
                                    'disetujui' => 'bg-primary',
                                    'ditolak'   => 'bg-danger',
                                    'selesai'   => 'bg-success',
                                    default     => 'bg-secondary',
                                };
                                $iconClass = match($r->status_baru) {
                                    'disetujui' => 'bi-check-circle-fill',
                                    'ditolak'   => 'bi-x-circle-fill',
                                    'selesai'   => 'bi-check2-all',
                                    default     => 'bi-info-circle-fill',
                                };
                            @endphp
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle {{ $badgeClass }} text-white d-flex align-items-center justify-content-center"
                                         style="width: 32px; height: 32px;">
                                        <i class="bi {{ $iconClass }} small"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold small">
                                                {{ $r->user->nama ?? $r->user->name ?? 'Petugas' }}
                                                <span class="badge {{ $badgeClass }} ms-1">{{ ucfirst($r->status_baru) }}</span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        {{ $r->catatan }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection