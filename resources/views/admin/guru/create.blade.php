@extends('layouts.app')

@section('title', 'Tambah Akun Guru - Admin')
@section('header_title', 'Tambah Akun Guru')

@section('content')
<div class="page-content">
    <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
            <h5 class="fw-bold mb-4">Form Tambah Akun Login Guru</h5>

            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">NIP / NIK</label>
                    <input type="text" name="nip_nik" class="form-control" placeholder="Contoh: 198501012010011001" value="{{ old('nip_nik') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Nama Lengkap (Beserta Gelar)</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Drs. H. Bambang Sudarsono, M.Pd" value="{{ old('nama') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Email Login</label>
                    <input type="email" name="email" class="form-control" placeholder="Contoh: bambang@smk.sch.id" value="{{ old('email') }}" required>
                </div>

                <!-- Field Password dengan Tombol Mata (Toggle Password) -->
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Password Login</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Minimal 6 karakter" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small mb-1">Role / Jabatan</label>
                    <select name="role_id" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ (old('role_id') == $role->id || strtolower($role->nama_role) == 'guru') ? 'selected' : '' }}>
                                @if(strtolower($role->nama_role) == 'guru') Guru Pengajar
                                @elseif(strtolower($role->nama_role) == 'bk') Bimbingan Konseling (BK)
                                @elseif(strtolower($role->nama_role) == 'kepala_sekolah') Kepala Sekolah
                                @elseif(strtolower($role->nama_role) == 'waka_kesiswaan') Waka Kesiswaan
                                @else {{ strtoupper($role->nama_role) }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Akun
                    </button>
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#togglePassword').click(function() {
            let passwordInput = $('#passwordInput');
            let eyeIcon = $('#eyeIcon');

            // Cek tipe input saat ini
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                eyeIcon.removeClass('bi-eye-slash').addClass('bi-eye');
            } else {
                passwordInput.attr('type', 'password');
                eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
            }
        });
    });
</script>
@endpush