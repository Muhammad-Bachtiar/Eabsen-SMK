<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - E-Absen SMK Syafi'i Akrom</title>
    
    <!-- Mazer Core CSS -->
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/extensions/bootstrap-icons/font/bootstrap-icons.css') }}">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        #auth {
            min-height: 100vh;
        }

        /* Base Auth Left */
        #auth-left {
            padding: 3rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
        }

        /* Container Logo & Nama Sekolah */
        .auth-logo-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .auth-logo-wrapper img {
            height: 60px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }

        .auth-logo-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
            margin: 0;
        }

        .auth-logo-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        /* Form Input Styling */
        .form-group.position-relative {
            position: relative !important;
        }

        .form-control-xl {
            padding-left: 2.8rem !important;
            padding-right: 2.8rem !important;
            height: 48px;
            font-size: 0.95rem;
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1;
        }

        .form-control-xl::placeholder {
            color: #94a3b8;
        }

        .form-control-icon {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            left: 1rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            pointer-events: none;
            z-index: 5;
            color: #64748b;
        }

        .form-control-icon i {
            font-size: 1.15rem;
            line-height: 1;
        }

        .password-toggle-btn {
            position: absolute !important;
            right: 0.8rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            border: none;
            background: transparent;
            color: #64748b;
            z-index: 10;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: #435ebe;
        }

        /* Desktop Sisi Kanan */
        #auth #auth-right {
            background: linear-gradient(to top, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.25) 60%), 
                        url("{{ asset('images/smk.jpg') }}") !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            min-height: 100vh;
            display: flex;
            align-items: flex-end;
            padding: 3rem;
        }

        .glass-card-desktop {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            padding: 1.75rem;
            width: 100%;
        }

        /* KOTAK ABU-ABU MUDA TRANSPARAN KHUSUS UNTUK FORM (MOBILE & DESKTOP) */
        .auth-card-wrapper {
            background: rgba(248, 249, 250, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 1.25rem;
            padding: 2.25rem 1.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* KHUSUS RESPONSIVE MOBILE (ANDROID) */
        @media screen and (max-width: 991px) {
            #auth {
                background: linear-gradient(rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.5)), 
                            url("{{ asset('images/smk.jpg') }}") !important;
                background-size: cover !important;
                background-position: center !important;
                background-attachment: fixed !important;
            }

            #auth-left {
                padding: 1.5rem 1rem;
            }

            .auth-card-wrapper {
                background: rgba(241, 245, 249, 0.9); /* Abu-abu muda transparan presisi */
            }

            .footer-text p {
                color: #ffffff !important;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
            }
        }
    </style>
</head>
<body>
    <script src="{{ asset('mazer/static/js/initTheme.js') }}"></script>
    
    <div id="auth">
        <div class="row h-100 g-0">
            <!-- Sisi Kiri: Form Login -->
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <!-- Kotak Abu-Abu Muda Transparan -->
                    <div class="auth-card-wrapper">
                        <!-- Logo & Nama Sekolah -->
                        <div class="auth-logo-wrapper">
                            <img src="{{ asset('images/logo-SMK.png') }}" alt="Logo SMK Syafi'i Akrom">
                            <div>
                                <h1 class="auth-logo-title">SMK Syafi'i Akrom</h1>
                                <p class="auth-logo-subtitle">Kota Pekalongan</p>
                            </div>
                        </div>
                        
                        <h2 class="auth-title fs-3 mb-1" style="color: #1e293b; font-weight: 700;">E-Jurnal</h2>
                        <p class="auth-subtitle mb-4" style="color: #475569; font-size: 0.9rem;">Sistem Informasi E-Absen & Manajemen Akademik</p>

                        <!-- Alert Errors / Status -->
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
                                    <div>{{ $errors->first() }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                                    <div>{{ session('status') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('login.process') }}" method="POST">
                            @csrf
                            
                            <!-- Input Email -->
                            <div class="form-group position-relative mb-3">
                                <input type="email" 
                                       class="form-control form-control-xl @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Alamat Email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
                                <div class="form-control-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>

                            <!-- Input Password + Toggle Icon -->
                            <div class="form-group position-relative mb-4">
                                <input type="password" 
                                       class="form-control form-control-xl" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Kata Sandi" 
                                       required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <button type="button" 
                                        class="password-toggle-btn" 
                                        id="togglePassword" 
                                        tabindex="-1" 
                                        title="Lihat/Sembunyikan Password">
                                    <i class="bi bi-eye fs-5" id="toggleIcon"></i>
                                </button>
                            </div>

                            <!-- Tombol Submit -->
                            <button type="submit" class="btn btn-primary btn-block btn-lg shadow mt-2 w-100" style="height: 48px; font-size: 1rem; font-weight: 600;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4 text-sm footer-text">
                        <p class="mb-0 fw-semibold">&copy; {{ date('Y') }} Codepelita</p>
                        <p class="mb-0 opacity-75">E-Jurnal System</p>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Background Gambar Gedung SMK (Khusus Layar Desktop) -->
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <div class="glass-card-desktop shadow-lg">
                        <h3 class="text-white fw-bold mb-2">Selamat Datang di E-Absen</h3>
                        <p class="mb-0 text-light opacity-85">Sistem Pencatatan Presensi Digital & Monitoring Pelanggaran Siswa SMK Syafi'i Akrom Pekalongan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Toggle Password -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && icon) {
                togglePassword.addEventListener('click', function (e) {
                    e.preventDefault();
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'text') {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            }
        });
    </script>
</body>
</html>