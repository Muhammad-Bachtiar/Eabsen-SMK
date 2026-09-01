@extends('layouts.app')

@section('header_title', 'Dashboard - Input Presensi')

@section('content')
<div class="page-content">
    <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
            
            <h5 class="fw-bold mb-3">Input Presensi Mata Pelajaran</h5>

            <form action="{{ route('guru.presensi.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small mb-1">Pilih Mata Pelajaran</label>
                        <select name="mapel_id" id="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small d-block mb-2">Centang Jam Pelajaran (Boleh lebih dari satu)</label>
                    <div class="d-flex flex-wrap gap-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jam[]" value="{{ $i }}" id="jam{{ $i }}">
                            <label class="form-check-label text-secondary" for="jam{{ $i }}">Jam {{ $i }}</label>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Form Radio Button Pilihan Guru -->
                <div id="areaSiswa" style="display: none;" class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">Daftar Siswa</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody id="tempatSiswa">
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 mt-2">
                        <i class="bi bi-save me-1"></i> Simpan Presensi
                    </button>
                </div>
            </form>

            <!-- Hasil Simpan Presensi -->
            @if(isset($presensiSelesai) && $presensiSelesai)
            <hr class="my-5">
            <h5 class="fw-bold text-dark mb-3">Daftar Kehadiran Siswa</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="20%">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="15%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailsSelesai as $index => $dt)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $dt->siswa->nis ?? '-' }}</td>
                            <td><strong>{{ $dt->siswa->nama ?? '-' }}</strong></td>
                            <td class="text-center">
                                @if(strtolower($dt->status) == 'hadir')
                                    <span class="badge bg-success px-3 py-2">Hadir</span>
                                @elseif(strtolower($dt->status) == 'sakit')
                                    <span class="badge bg-warning text-dark px-3 py-2">Sakit</span>
                                @elseif(strtolower($dt->status) == 'izin')
                                    <span class="badge bg-primary px-3 py-2">Izin</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">Alpa</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function loadSiswa(kelasId) {
            if(kelasId) {
                $('#areaSiswa').show();
                $('#tempatSiswa').html('<tr><td colspan="3" class="text-center py-3">Memuat data siswa...</td></tr>');

                $.ajax({
                    url: '/guru/presensi/get-siswa/' + kelasId,
                    type: 'GET',
                    success: function(response) {
                        let baris = '';
                        if(response.length > 0) {
                            $.each(response, function(index, siswa) {
                                baris += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td><strong>${siswa.nama}</strong><br><small class="text-muted">${siswa.nis ?? '-'}</small></td>
                                        <td>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="hadir" id="h_${siswa.id}" checked>
                                                    <label class="form-check-label text-success fw-bold" for="h_${siswa.id}">Hadir</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="sakit" id="s_${siswa.id}">
                                                    <label class="form-check-label text-warning fw-bold" for="s_${siswa.id}">Sakit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="izin" id="i_${siswa.id}">
                                                    <label class="form-check-label text-primary fw-bold" for="i_${siswa.id}">Izin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="alpa" id="a_${siswa.id}">
                                                    <label class="form-check-label text-danger fw-bold" for="a_${siswa.id}">Alpa</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            baris = '<tr><td colspan="3" class="text-center text-danger py-3">Belum ada siswa di kelas ini.</td></tr>';
                        }
                        $('#tempatSiswa').html(baris);
                    },
                    error: function() {
                        $('#tempatSiswa').html('<tr><td colspan="3" class="text-center text-danger py-3">Gagal memuat data siswa.</td></tr>');
                    }
                });
            } else {
                $('#areaSiswa').hide();
                $('#tempatSiswa').html('');
            }
        }

        $('#kelas_id').change(function() {
            let kelasId = $(this).val();
            loadSiswa(kelasId);
        });
    });
</script>
@endpush