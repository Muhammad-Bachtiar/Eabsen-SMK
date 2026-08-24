<!DOCTYPE html>
<html>
<head>
    <title>Rekap Presensi - {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h4 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .badge-danger { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI PRESENSI SISWA (BIMBINGAN KONSELLING)</h2>
        <h4>Kelas: {{ $kelas->nama_kelas }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS / NISN</th>
                <th>Nama Siswa</th>
                <th width="8%">Hadir</th>
                <th width="8%">Sakit</th>
                <th width="8%">Izin</th>
                <th width="8%">Alpha</th>
                <th width="18%">Catatan BK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapSemester as $index => $s)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $s->nis }}</td>
                <td>{{ $s->nama }}</td>
                <td class="text-center">{{ $s->hadir }}</td>
                <td class="text-center">{{ $s->sakit }}</td>
                <td class="text-center">{{ $s->izin }}</td>
                <td class="text-center">{{ $s->alpha }}</td>
                <td class="text-center">
                    @if($s->alpha >= 3)
                        <span class="badge-danger">Perlu Bimbingan</span>
                    @else
                        Aman
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="border: none; margin-top: 30px;">
        <tr style="border: none;">
            <td style="border: none; text-align: center; width: 50%;">
                Mengetahui,<br>
                Wali Kelas {{ $kelas->nama_kelas }}<br><br><br><br>
                <b><u>{{ $kelas->waliKelas->nama ?? '( .................................... )' }}</u></b><br>
                NIP/NIK. {{ $kelas->waliKelas->nip_nik ?? '-' }}
            </td>
            <td style="border: none; text-align: center; width: 50%;">
                Pekalongan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Guru Bimbingan Konseling<br><br><br><br>
                <b><u>{{ Auth::user()->nama }}</u></b><br>
                NIP/NIK. {{ Auth::user()->nip_nik ?? '-' }}
            </td>
        </tr>
    </table>
    <br><br>
</body>
</html>