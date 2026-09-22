@extends('layouts.app')

@section('header_title', 'Dashboard')

@section('content')
<div class="page-content">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body pt-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary me-2"></i>Input Presensi Mata Pelajaran</h5>

            <form id="formPresensi" action="{{ route('guru.presensi.store') }}" method="POST">
                @csrf
                
                <!-- Header Filter -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}" {{ (isset($kelas_id) &&$kelas_id == $k->id) ? 'selected' : '' }}>{{$k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small mb-1">Pilih Mata Pelajaran</label>
                        <select name="mapel_id" id="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ (isset($mapel_id) &&$mapel_id == $m->id) ? 'selected' : '' }}>{{$m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Container Accordion & Pilihan Jam (Selalu Tampil) -->
                <div id="areaAccordion" style="position: relative;">
                    
                    <!-- Opsi Jam Pelajaran & Checkbox Pilih Semua -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-secondary small">Pilih Jam yang Ingin Diisi:</span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAllJam">
                            <label class="form-check-label small fw-bold text-primary" for="checkAllJam">Pilih Semua Jam</label>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border mb-3">
                        @foreach($jamPelajarans as $jam)
                            <div class="form-check">
                                <input class="form-check-input jam-checkbox" 
                                    type="checkbox" 
                                    name="jam[]" 
                                    value="{{ $jam->jam_ke }}" 
                                    id="jam_{{ $jam->jam_ke }}">
                                
                                <label class="form-check-label fw-bold" for="jam_{{ $jam->jam_ke }}">
                                    Jam {{ $jam->jam_ke }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Container Accordion Jam Pelajaran 1 - 10 -->
                    <div class="accordion mt-3" id="accordionPresensi">
                        @foreach($jamPelajarans as $jam)
                            <div class="accordion-item mb-2 border rounded shadow-sm">
                                <h2 class="accordion-header" id="heading_{{ $jam->jam_ke }}">
                                    <button class="accordion-button collapsed py-2" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse_{{ $jam->jam_ke }}">
                                        
                                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                            <span><strong>Jam ke-{{ $jam->jam_ke }}</strong> ({{ $jam->jam_mulai ?? '-' }} - {{ $jam->jam_selesai ?? '-' }})</span>
                                            <span class="badge bg-secondary" id="badge_status_jam_{{ $jam->jam_ke }}">Belum Dipilih</span>
                                        </div>
                                    </button>
                                </h2>

                                <div id="collapse_{{ $jam->jam_ke }}" 
                                     class="accordion-collapse collapse" 
                                     data-bs-parent="#accordionPresensi">
                                    <div class="accordion-body" id="body_jam_{{ $jam->jam_ke }}">
                                        <p class="text-muted text-center my-2 small">Pilih kelas terlebih dahulu untuk melihat riwayat atau menginput data.</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" id="btnSimpan" class="btn btn-primary w-100 py-2 mt-3 fw-bold" style="display: none;">
                        <i class="bi bi-save me-1"></i> Simpan Presensi
                    </button>

                    <div id="loadingAccordion" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; justify-content:center; align-items:center;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </form>
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span id="toastMessage">Presensi berhasil disimpan!</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let listSiswaData = [];
    let riwayatDataByJam = {};

    // ============================================================
    // LOAD DATA PRESENSI (dengan callback opsional)
    // ============================================================
    function loadDataPresensi(callback) {
        let kelasId = $('#kelas_id').val();
        let tanggal = $('#tanggal').val();
        let mapelId = $('#mapel_id').val();

        if (!kelasId || !tanggal) {
            listSiswaData = [];
            riwayatDataByJam = {};
            renderAccordion();
            if (typeof callback === 'function') callback();
            return;
        }

         $('#loadingAccordion').css('display', 'flex');

        $.ajax({
            url: "{{ route('guru.presensi.getData') }}",
            type: 'GET',
            data: {
                kelas_id: kelasId,
                tanggal: tanggal,
                mapel_id: mapelId
            },
            success: function(res) {
                listSiswaData = res.siswas || [];
                riwayatDataByJam = res.riwayat || {};
                renderAccordion();
                if (typeof callback === 'function') callback();
            },
            error: function(xhr) {
                console.error("AJAX Error:", xhr.responseText);
                listSiswaData = [];
                riwayatDataByJam = {};
                renderAccordion();
                if (typeof callback === 'function') callback();
            },
            complete: function() {
            $('#loadingAccordion').css('display', 'none');
        }
        });
    }

    // ============================================================
    // RENDER ACCORDION
    // ============================================================
    function renderAccordion() {
        let kelasId = $('#kelas_id').val();
        let checkedJams = [];

        $('.jam-checkbox:checked:not(:disabled)').each(function() {
            checkedJams.push(parseInt($(this).val()));
        });

        let jamInputAktif = checkedJams.length > 0 ? Math.min(...checkedJams) : null;

        for (let i = 1; i <= 10; i++) {
            let itemJam = riwayatDataByJam[i] || riwayatDataByJam[i.toString()];

            if (itemJam) {
                // STATUS 1: SUDAH ADA RIWAYAT
                $(`#jam_${i}`).prop('disabled', true).prop('checked', false);
                $(`#badge_status_jam_${i}`)
                    .removeClass('bg-secondary bg-warning bg-success')
                    .addClass('bg-info text-dark')
                    .html(`<i class="bi bi-lock-fill me-1"></i>Diisi: ${itemJam.pencatat_nama} (${itemJam.mapel_nama})`);

                let htmlList = `
                    <div class="alert alert-light border mb-2 py-2 small">
                        <strong>Dicatat oleh:</strong> ${itemJam.pencatat_nama} | <strong>Mapel:</strong> ${itemJam.mapel_nama}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%" class="text-center">No</th>
                                    <th>Nama Siswa</th>
                                    <th width="25%">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>`;

                $.each(itemJam.details, function(idx, s) {
                    let badgeClass = s.status === 'hadir' ? 'bg-success'
                        : (s.status === 'sakit' ? 'bg-warning text-dark'
                        : (s.status === 'izin' ? 'bg-info text-dark' : 'bg-danger'));
                    htmlList += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td><strong>${s.nama}</strong></td>
                            <td><span class="badge ${badgeClass} w-100">${s.status.toUpperCase()}</span></td>
                            <td><small class="text-muted">${s.keterangan ? s.keterangan : '-'}</small></td>
                        </tr>`;
                });

                htmlList += `</tbody></table></div>`;
                $(`#body_jam_${i}`).html(htmlList);

            } else if (checkedJams.includes(i)) {
                // STATUS 2: DICENTANG
                if (i === jamInputAktif) {
                    $(`#badge_status_jam_${i}`)
                        .removeClass('bg-secondary bg-info')
                        .addClass('bg-success')
                        .text('Input Aktif (Jam Multi)');

                    let htmlForm = `
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Mengisi sekaligus untuk jam tercentang: <strong>${checkedJams.join(', ')}</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="8%" class="text-center">No</th>
                                        <th>Nama Siswa</th>
                                        <th width="30%">Status Kehadiran</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    if (listSiswaData && listSiswaData.length > 0) {
                        $.each(listSiswaData, function(idx, siswa) {
                            htmlForm += `
                                <tr>
                                    <td class="text-center">${idx + 1}</td>
                                    <td><strong>${siswa.nama}</strong><br><small class="text-muted">${siswa.nis ?? '-'}</small></td>
                                    <td>
                                        <select name="status[${siswa.id}]" class="form-select form-select-sm fw-bold">
                                            <option value="hadir" selected>Hadir</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="izin">Izin</option>
                                            <option value="alpa">Alpa</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan[${siswa.id}]" class="form-control form-control-sm" placeholder="Keterangan (opsional)">
                                    </td>
                                </tr>`;
                        });
                    } else {
                        htmlForm += `<tr><td colspan="4" class="text-center text-muted py-3">Data siswa tidak ditemukan di kelas ini.</td></tr>`;
                    }

                    htmlForm += `</tbody></table></div>`;
                    $(`#body_jam_${i}`).html(htmlForm);
                    $(`#collapse_${i}`).collapse('show');

                } else {
                    $(`#badge_status_jam_${i}`)
                        .removeClass('bg-secondary bg-info')
                        .addClass('bg-warning text-dark')
                        .text(`Mengikuti Jam ke-${jamInputAktif}`);

                    $(`#body_jam_${i}`).html(`
                        <div class="alert alert-warning mb-0 py-2 text-center small">
                            <i class="bi bi-link-45deg me-1"></i> Isian presensi jam ini otomatis disamakan dengan <strong>Jam ke-${jamInputAktif}</strong>.
                        </div>
                    `);
                    $(`#collapse_${i}`).collapse('hide');
                }

            } else {
                // STATUS 3: BELUM DICENTANG
                $(`#badge_status_jam_${i}`)
                    .removeClass('bg-success bg-warning bg-info text-dark')
                    .addClass('bg-secondary')
                    .text('Belum Dipilih');

                let pesanteks = kelasId
                    ? `Jam ke-${i} belum dipilih.`
                    : `Pilih kelas terlebih dahulu untuk melihat riwayat atau menginput data.`;

                $(`#body_jam_${i}`).html(`<p class="text-muted text-center my-2 small">${pesanteks}</p>`);
                $(`#collapse_${i}`).collapse('hide');
            }
        }

        if (checkedJams.length > 0) {
            $('#btnSimpan').show();
        } else {
            $('#btnSimpan').hide();
        }
    }

    // ============================================================
    // EVENT HANDLER
    // ============================================================
    $('#kelas_id, #tanggal, #mapel_id').change(function() {
        $('.jam-checkbox').prop('checked', false).prop('disabled', false);
        $('#checkAllJam').prop('checked', false);
        loadDataPresensi();
    });

    $(document).on('change', '.jam-checkbox', function() {
        renderAccordion();
    });

    $('#checkAllJam').change(function() {
        let isChecked = $(this).is(':checked');
        $('.jam-checkbox:not(:disabled)').prop('checked', isChecked);
        renderAccordion();
    });

    // ============================================================
    // SUBMIT VIA AJAX (tanpa reload halaman)
    // ============================================================
    $('#formPresensi').on('submit', function(e) {
        e.preventDefault();

        let $btn = $('#btnSimpan');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Menyimpan...');

        let jamChecked = [];
        $('.jam-checkbox:checked:not(:disabled)').each(function() {
            jamChecked.push($(this).val());
        });

        if (jamChecked.length === 0) {
            alert('Pilih minimal 1 jam pelajaran.');
            $btn.prop('disabled', false).html(originalText);
            return;
        }

        let formData = $(this).serializeArray();
        formData = formData.filter(item => item.name !== 'jam[]');
        jamChecked.forEach(function(j) {
            formData.push({ name: 'jam[]', value: j });
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $.param(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $('#toastMessage').text(res.message || 'Presensi berhasil disimpan!');
                    var toastEl = document.getElementById('successToast');
                    var toast = new bootstrap.Toast(toastEl, {
                        delay: 3000 // Notifikasi akan hilang otomatis setelah 3 detik
                    });
                    toast.show();

                    let jamBaru = res.jam_diisi || [];

                    $('.jam-checkbox').prop('checked', false);
                    $('#checkAllJam').prop('checked', false);

                    // Reload data TANPA mengubah filter
                    loadDataPresensi(function() {
                        jamBaru.forEach(function(jam) {
                            $('#collapse_' + jam).collapse('show');
                        });
                        if (jamBaru.length > 0) {
                            let target = document.getElementById('heading_' + jamBaru[0]);
                            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                } else {
                    alert(res.message || 'Gagal menyimpan.');
                }
            },
            error: function(xhr) {
                let msg = 'Gagal menyimpan presensi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ============================================================
    // INISIALISASI PERTAMA
    // ============================================================
    loadDataPresensi(function() {
        @if(!empty($presensiId) && !empty($jamYangBaruDiisi))
            var jamBaru = @json($jamYangBaruDiisi);

            jamBaru.forEach(function(jam) {
                $('#collapse_' + jam).collapse('show');
            });

            if (jamBaru.length > 0) {
                var target = document.getElementById('heading_' + jamBaru[0]);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        @endif
    });

});  // ← PENUTUP $(document).ready — pastikan hanya SATU
</script>
@endpush