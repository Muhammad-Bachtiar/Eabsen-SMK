<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Pelanggaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h2 { text-align: center; margin-bottom: 5px; }
        h4 { text-align: center; margin-top: 0; color: #666; font-weight: normal; }
        .box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .box h5 { margin: 0 0 8px 0; padding-bottom: 5px; border-bottom: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 4px 6px; vertical-align: top; }
        .label { width: 30%; font-weight: bold; color: #555; }
        .badge { padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 10px; }
        .badge-danger { background: #dc3545; }
        .badge-primary { background: #0d6efd; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-success { background: #198754; }
        .timeline-item { border-left: 3px solid #0d6efd; padding-left: 10px; margin-bottom: 10px; }
        .timeline-item .meta { font-size: 10px; color: #666; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 15px 0; }
    </style>
</head>
<body>
    <h2>DETAIL PELANGGARAN SISWA</h2>
    <h4>E-Jurnal SMKSA</h4>
    <hr>

    <div class="box">
        <h5>Informasi Siswa</h5>
        <table>
            <tr><td class="label">Nama</td><td>: {{ $pelanggaran->siswa->nama ?? '-' }}</td></tr>
            <tr><td class="label">NIS / NISN</td><td>: {{ $pelanggaran->siswa->nis ?? $pelanggaran->siswa->nisn ?? '-' }}</td></tr>
            <tr><td class="label">Kelas</td><td>: {{ $pelanggaran->siswa->kelas->nama_kelas ?? '-' }}</td></tr>
            <tr>
            <td class="label">Wali Kelas</td><td>: {{ $pelanggaran->siswa->kelas->waliKelas->nama ?? $pelanggaran->siswa->kelas->waliKelas->name ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="box">
        <h5>Detail Pelanggaran</h5>
        <table>
            <tr><td class="label">Tanggal Kejadian</td><td>: {{ \Carbon\Carbon::parse($pelanggaran->tanggal_kejadian)->translatedFormat('d F Y') }}</td></tr>
            <tr><td class="label">Jenis Pelanggaran</td><td>: {{ $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td></tr>
            <tr><td class="label">Kategori</td><td>: {{ ucfirst($pelanggaran->jenisPelanggaran->kategori ?? '-') }}</td></tr>
            <tr><td class="label">Poin</td><td>: {{ $pelanggaran->poin }} Poin</td></tr>
            <tr><td class="label">Dicatat Oleh</td><td>: {{ $pelanggaran->pencatat->nama ?? $pelanggaran->pencatat->name ?? '-' }}</td></tr>
            <tr><td class="label">Deskripsi</td><td>: {{ $pelanggaran->deskripsi }}</td></tr>
        </table>
    </div>

    <div class="box">
        <h5>Rencana Tindak Lanjut (BK)</h5>
        <p>{{ $pelanggaran->rencana_tindak_lanjut ?? 'Belum ada rencana tindak lanjut.' }}</p>
    </div>

    <div class="box">
        <h5>Riwayat Persetujuan & Tindak Lanjut</h5>
        @if($riwayat->isEmpty())
            <p><em>Belum ada riwayat persetujuan.</em></p>
        @else
            @foreach($riwayat as $r)
                <div class="timeline-item">
                    <strong>{{ $r->user->nama ?? $r->user->name ?? 'Petugas' }}</strong>
                    — <span class="badge badge-{{ $r->status_baru === 'disetujui' ? 'primary' : ($r->status_baru === 'ditolak' ? 'danger' : 'success') }}">
                        {{ ucfirst($r->status_baru) }}
                    </span>
                    <div class="meta">{{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div style="margin-top: 5px;">{{ $r->catatan }}</div>
                </div>
            @endforeach
        @endif
    </div>

    <hr>
    <p style="text-align: center; font-size: 9px; color: #999;">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </p>
</body>
</html>