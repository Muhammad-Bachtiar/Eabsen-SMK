@extends('layouts.app')

@section('header_title', 'Dashboard Bimbingan Konseling')

@section('content')
<div class="page-content">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body p-4">
                    <h4 class="text-white fw-bold">Panel Bimbingan & Konseling (BK)</h4>
                    <p class="mb-0">Selamat datang, {{ $user->nama ?? $user->name }}. Pantau kedisiplinan, rekap poin pelanggaran, dan tindak lanjut siswa di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body pt-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-pencil-square text-primary me-2"></i>Input Presensi Bimbingan Konseling
            </h5>

            <form id="formPresensiBk" action="{{ route('bk.presensi.store') }}" method="POST">
                @csrf

                {{-- Header Filter --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                               value="{{ $tanggal ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}" {{ (isset($kelas_id) && $kelas_id == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }} {{ $k->is_binaan ? '⭐ Binaan' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small mb-1">Pilih Mata Pelajaran</label>
                        <select name="mapel_id" id="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ (isset($mapel_id) && $mapel_id == $m->id) ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Checkbox Jam --}}
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
                            <input class="form-check-input jam-checkbox" type="checkbox"
                                   name="jam[]" value="{{ $jam->jam_ke }}" id="jam_{{ $jam->jam_ke }}">
                            <label class="form-check-label fw-bold" for="jam_{{ $jam->jam_ke }}">
                                Jam {{ $jam->jam_ke }}
                            </label>
                        </div>
                    @endforeach
                </div>

                {{-- Accordion --}}
                <div class="accordion mt-3" id="accordionPresensiBk">
                    @foreach($jamPelajarans as $jam)
                        <div class="accordion-item mb-2 border rounded shadow-sm">
                            <h2 class="accordion-header" id="heading_{{ $jam->jam_ke }}">
                                <button class="accordion-button collapsed py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse_{{ $jam->jam_ke }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <span><strong>Jam ke-{{ $jam->jam_ke }}</strong>
                                            ({{ $jam->waktu_mulai ?? '-' }} - {{ $jam->waktu_selesai ?? '-' }})</span>
                                        <span class="badge bg-secondary" id="badge_status_jam_{{ $jam->jam_ke }}">Belum Dipilih</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse_{{ $jam->jam_ke }}" class="accordion-collapse collapse"
                                 data-bs-parent="#accordionPresensiBk">
                                <div class="accordion-body" id="body_jam_{{ $jam->jam_ke }}">
                                    <p class="text-muted text-center my-2 small">Pilih kelas terlebih dahulu untuk melihat riwayat atau menginput data.</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" id="btnSimpanBk" class="btn btn-primary w-100 py-2 mt-3 fw-bold" style="display: none;">
                    <i class="bi bi-save me-1"></i> Simpan Presensi BK
                </button>
            </form>
        </div>
    </div>

    <!-- Cards Statistik Mazer -->
    <div class="row">
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Pelanggaran</h6>
                            <h6 class="font-extrabold mb-0">{{ $totalPelanggaran }} Kasus</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon orange mb-2">
                                <i class="bi bi-hourglass-split text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Menunggu Approval</h6>
                            <h6 class="font-extrabold mb-0">{{ $kasusMenunggu }} Kasus</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-check-circle-fill text-white"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Disetujui / Selesai</h6>
                            <h6 class="font-extrabold mb-0">{{ $kasusSelesai }} Kasus</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Tengah: Kelas Binaan & Alpha Monitor -->
    <div class="row">
        <!-- Widget Kelas Binaan -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-people-fill text-primary me-2"></i>Kelas Binaan Saya
                    </h4>
                    <span class="badge bg-light-primary text-primary fs-6">{{ $totalSiswaBinaan }} Siswa</span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse($kelasBinaan as $kb)
                            <div class="col-6 col-md-4">
                                <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $kb->nama_kelas }}</h6>
                                        <small class="text-muted">Binaan</small>
                                    </div>
                                    <span class="badge bg-primary rounded-circle p-1"><i class="bi bi-check-lg"></i></span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                Belum ada data penugasan kelas binaan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Siswa Sering Alpha -->
        <div class="col-12 col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="bi bi-bell-fill text-danger me-2"></i>Pantauan Siswa Sering Alpha</h4>
                    <p class="text-subtitle text-muted mb-0">Deteksi dini siswa dengan akumulasi Alpha terbanyak</p>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($topAlpha as $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $item->siswa->nama ?? '-' }}</h6>
                                    <small class="text-muted">{{ $item->siswa->kelas->nama_kelas ?? 'Kelas -' }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill">{{ $item->total_alpha }}x Alpha</span>
                            </div>
                        @empty
                            <div class="text-center p-3 text-muted">Tidak ada siswa dengan catatan Alpha.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Bawah: Riwayat Pelanggaran Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Pelanggaran Siswa Terbaru</h4>
                    <a href="{{ route('bk.pelanggaran.index') }}" class="btn btn-sm btn-outline-info">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th class="text-center">Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggaranTerbaru as $p)
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-bold">{{ $p->siswa->nama ?? '-' }}</p>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-danger text-danger fw-bold">+{{ $p->poin }}</span>
                                    </td>
                                    <td>
                                        @if($p->status == 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($p->status == 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada catatan pelanggaran siswa.</td>
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
@push('scripts')
<script>
$(document).ready(function () {
    let listSiswaData = [];
    let riwayatDataByJam = {};

    // ============================================================
    // LOAD DATA PRESENSI (dengan callback)
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

        $('#accordionPresensiBk').css('opacity', 0.5);

        $.ajax({
            url: "{{ route('bk.presensi.getData') }}",
            type: 'GET',
            data: { kelas_id: kelasId, tanggal: tanggal, mapel_id: mapelId },
            success: function (res) {
                listSiswaData = res.siswas || [];
                riwayatDataByJam = res.riwayat || {};
                renderAccordion();
                if (typeof callback === 'function') callback();
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                listSiswaData = [];
                riwayatDataByJam = {};
                renderAccordion();
                if (typeof callback === 'function') callback();
            },
            complete: function () {
                $('#accordionPresensiBk').css('opacity', 1);
            }
        });
    }

    // ============================================================
    // RENDER ACCORDION
    // ============================================================
    function renderAccordion() {
        let kelasId = $('#kelas_id').val();
        let checkedJams = [];

        $('.jam-checkbox:checked:not(:disabled)').each(function () {
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
                        <strong>Dicatat oleh:</strong> ${itemJam.pencatat_nama} |
                        <strong>Mapel:</strong> ${itemJam.mapel_nama}
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

                $.each(itemJam.details, function (idx, s) {
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
                            <i class="bi bi-info-circle me-1"></i> Mengisi sekaligus untuk jam tercentang:
                            <strong>${checkedJams.join(', ')}</strong>
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
                        $.each(listSiswaData, function (idx, siswa) {
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
                            <i class="bi bi-link-45deg me-1"></i> Isian presensi jam ini otomatis disamakan dengan
                            <strong>Jam ke-${jamInputAktif}</strong>.
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
            $('#btnSimpanBk').show();
        } else {
            $('#btnSimpanBk').hide();
        }
    }

    // ============================================================
    // EVENT HANDLER
    // ============================================================
    $('#kelas_id, #tanggal, #mapel_id').change(function () {
        $('.jam-checkbox').prop('checked', false).prop('disabled', false);
        $('#checkAllJam').prop('checked', false);
        loadDataPresensi();
    });

    $(document).on('change', '.jam-checkbox', function () {
        renderAccordion();
    });

    $('#checkAllJam').change(function () {
        let isChecked = $(this).is(':checked');
        $('.jam-checkbox:not(:disabled)').prop('checked', isChecked);
        renderAccordion();
    });

    // ============================================================
    // SUBMIT VIA AJAX
    // ============================================================
    $('#formPresensiBk').on('submit', function (e) {
        e.preventDefault();

        let $btn = $('#btnSimpanBk');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Menyimpan...');

        let jamChecked = [];
        $('.jam-checkbox:checked:not(:disabled)').each(function () {
            jamChecked.push($(this).val());
        });

        if (jamChecked.length === 0) {
            showToast('warning', 'Pilih minimal 1 jam pelajaran.');
            $btn.prop('disabled', false).html(originalText);
            return;
        }

        let formData = $(this).serializeArray();
        formData = formData.filter(item => item.name !== 'jam[]');
        jamChecked.forEach(function (j) {
            formData.push({ name: 'jam[]', value: j });
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $.param(formData),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    // Toast sukses (dari layout global)
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Presensi BK berhasil disimpan!');
                    } else {
                        alert(res.message || 'Presensi BK berhasil disimpan!');
                    }

                    let jamBaru = res.jam_diisi || [];
                    $('.jam-checkbox').prop('checked', false);
                    $('#checkAllJam').prop('checked', false);

                    loadDataPresensi(function () {
                        jamBaru.forEach(function (jam) {
                            $('#collapse_' + jam).collapse('show');
                        });
                        if (jamBaru.length > 0) {
                            let target = document.getElementById('heading_' + jamBaru[0]);
                            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                } else {
                    showToast('error', res.message || 'Gagal menyimpan.');
                }
            },
            error: function (xhr) {
                let msg = 'Gagal menyimpan presensi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast('error', msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ============================================================
    // INISIALISASI PERTAMA
    // ============================================================
    loadDataPresensi(function () {
        @if(!empty($presensiId) && !empty($jamYangBaruDiisi))
            var jamBaru = @json($jamYangBaruDiisi);
            jamBaru.forEach(function (jam) {
                $('#collapse_' + jam).collapse('show');
            });
            if (jamBaru.length > 0) {
                var target = document.getElementById('heading_' + jamBaru[0]);
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        @endif
            });
});
</script>
@endpush
@endsection