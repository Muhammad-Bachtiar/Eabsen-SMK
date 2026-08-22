@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Input Presensi Mata Pelajaran</h5>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('guru.presensi.store') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Pilih Kelas & Mata Pelajaran</label>
                        <select class="form-select" id="pilihJadwal" required>
                            <option value="">-- Pilih Jadwal Anda --</option>
                            @foreach($jadwals as $j)
                                <option value="{{ $j->kelas_id }}" data-mapel="{{ $j->mapel_id }}">
                                    {{ $j->kelas->nama_kelas }} - {{ $j->mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        
                        <input type="hidden" name="kelas_id" id="kelas_id">
                        <input type="hidden" name="mapel_id" id="mapel_id">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Centang Jam Pelajaran (Boleh lebih dari satu)</label>
                    <div class="d-flex flex-wrap gap-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="jam[]" value="{{ $i }}" id="jam{{ $i }}">
                            <label class="form-check-label" for="jam{{ $i }}">Jam {{ $i }}</label>
                        </div>
                        @endfor
                    </div>
                </div>

                <hr>

                <div id="areaSiswa" style="display: none;">
                    <h5 class="mb-3">Daftar Siswa</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Nama Siswa</th>
                                    <th>Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody id="tempatSiswa">
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Simpan Presensi</button>
                </div>
            </form>
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
                $('#tempatSiswa').html('<tr><td colspan="3" class="text-center">Memuat...</td></tr>');

                $.ajax({
                    url: '/guru/presensi/get-siswa/' + kelasId,
                    type: 'GET',
                    success: function(response) {
                        let baris = '';
                        if(response.length > 0) {
                            $.each(response, function(index, siswa) {
                                baris += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td>${siswa.nama} <br><small class="text-muted">${siswa.nis}</small></td>
                                        <td>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="hadir" id="h_${siswa.id}" checked>
                                                    <label class="form-check-label text-success" for="h_${siswa.id}">Hadir</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="sakit" id="s_${siswa.id}">
                                                    <label class="form-check-label text-warning" for="s_${siswa.id}">Sakit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="izin" id="i_${siswa.id}">
                                                    <label class="form-check-label text-primary" for="i_${siswa.id}">Izin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="alpa" id="a_${siswa.id}">
                                                    <label class="form-check-label text-danger" for="a_${siswa.id}">Alpa</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            baris = '<tr><td colspan="3" class="text-center text-danger">Belum ada data siswa di kelas ini.</td></tr>';
                        }
                        $('#tempatSiswa').html(baris);
                    },
                    error: function() {
                        $('#tempatSiswa').html('<tr><td colspan="3" class="text-center text-danger">Gagal mengambil data dari server. Coba refresh halamannya.</td></tr>');
                    }
                });
            } else {
                $('#areaSiswa').hide();
                $('#tempatSiswa').html('');
            }
        }

        $('#pilihJadwal').change(function() {
            let kelasId = $(this).val();
            let mapelId = $(this).find(':selected').data('mapel');
            $('#kelas_id').val(kelasId);
            $('#mapel_id').val(mapelId);
            loadSiswa(kelasId);
        });

        if($('#pilihJadwal').val()) {
            $('#pilihJadwal').trigger('change');
        }
    });
</script>
@endpush