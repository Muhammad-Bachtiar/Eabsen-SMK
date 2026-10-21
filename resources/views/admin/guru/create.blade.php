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
                    <input type="text" name="nip_nik" class="form-control" placeholder="Contoh: 198501012010011001" value="{{ old('nip_nik') }}" maxlength="16">
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
                <div class="mb-4" id="wrapper_koordinator" style="display: none;">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_koordinator_bk" value="1" id="is_koordinator_bk" {{ old('is_koordinator_bk') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_koordinator_bk">
                            Jadikan sebagai Koordinator BK
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">Centang jika akun ini bertugas sebagai Koordinator BK (mengelola master pelanggaran).</small>
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
    // Gunakan DOMContentLoaded agar script jalan setelah HTML selesai dimuat
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. Toggle Password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', function (e) {
                e.preventDefault();
                
                // Cek tipe input saat ini
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ganti ikon mata
                if (type === 'text') {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                } else {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                }
            });
        }

        // 2. Toggle Checkbox Koordinator BK (Dari patch sebelumnya)
        // Pastikan form role_id punya id="role_id"
        const roleSelect = document.querySelector('select[name="role_id"]');
        const wrapperKoordinator = document.getElementById('wrapper_koordinator');

        if (roleSelect && wrapperKoordinator) {
            function cekRoleBK() {
                let selectedRoleText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
                if (selectedRoleText.includes('bimbingan konseling')) {
                    wrapperKoordinator.style.display = 'block'; // Tampilkan
                } else {
                    wrapperKoordinator.style.display = 'none'; // Sembunyikan
                    const checkbox = document.getElementById('is_koordinator_bk');
                    if(checkbox) checkbox.checked = false;
                }
            }

            // Panggil saat pertama kali load dan saat diganti
            cekRoleBK();
            roleSelect.addEventListener('change', cekRoleBK);
        }
    });
</script>
@endpush