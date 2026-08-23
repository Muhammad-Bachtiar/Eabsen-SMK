@extends('layouts.app')

@section('header_title', 'Input Presensi Kelas Binaan')

@section('content')
<div class="page-heading mb-3">
    <h3>Input Presensi Bimbingan & Konseling</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bk.presensi.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pilih Kelas Binaan:</label>
                        <select name="kelas_id" id="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas Binaan --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal:</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Container Siswa -->
                <div id="areaSiswa" style="display: none;" class="mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Status Presensi</th>
                                </tr>
                            </thead>
                            <tbody id="tempatSiswa">
                                <!-- Diisi via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('bk.presensi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Presensi BK
                    </button>
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
                $('#tempatSiswa').html('<tr><td colspan="3" class="text-center">Memuat data siswa...</td></tr>');

                $.ajax({
                    url: '/bk/get-siswa/' + kelasId,
                    type: 'GET',
                    success: function(response) {
                        let baris = '';
                        if(response.length > 0) {
                            $.each(response, function(index, siswa) {
                                baris += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td><strong>${siswa.nama}</strong> <br><small class="text-muted">${siswa.nis ?? '-'}</small></td>
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
                                                    <input class="form-check-input" type="radio" name="status[${siswa.id}]" value="alpha" id="a_${siswa.id}">
                                                    <label class="form-check-label text-danger" for="a_${siswa.id}">Alpha</label>
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

        $('#kelas_id').change(function() {
            let kelasId = $(this).val();
            loadSiswa(kelasId);
        });

        if($('#kelas_id').val()) {
            $('#kelas_id').trigger('change');
        }
    });
</script>
@endpush