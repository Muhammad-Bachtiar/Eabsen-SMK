<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Absen SMK')</title>
    
    <!-- CSS Inti Mazer & Icons -->
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/extensions/bootstrap-icons/font/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/extensions/@fortawesome/fontawesome-free/css/all.min.css') }}">
    
    <!-- DataTables Offline CSS (BS5) -->
    <link rel="stylesheet" href="{{ asset('mazer/extensions/datatables/datatables.min.css') }}">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        /* Sidebar fixed agar tidak ikut scroll */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
        }

        /* Main content offset agar tidak tertutup sidebar */
        #main {
            margin-left: 260px;
        }

        @media screen and (max-width: 1199px) {
            #main {
                margin-left: 0;
            }
        }

        /* Custom DataTables Styling */
        div.dataTables_wrapper div.dataTables_length select {
            width: auto;
            display: inline-block;
            padding: 0.375rem 2rem 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            margin-left: 0.5rem;
        }

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 0.85em;
            font-size: 0.875rem;
            color: #6c757d;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 0.5rem;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            justify-content: flex-end;
        }
    </style>
    @stack('styles')
</head>
<body>
<script>
    document.documentElement.setAttribute('data-bs-theme', 'light');
    localStorage.setItem('theme', 'light');
</script>
    
    <div id="app">
        <!-- Sidebar Navigation -->
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                                <div>
                                    <h4 class="text-primary mb-0 fw-bold">E-Jurnal SMKSA</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="menu">
                    @php
                        $role = Auth::user()->role->nama_role ?? '';
                        $isKoordinatorBK = Auth::user()->is_koordinator_bk ?? false;
                    @endphp
                    <ul class="menu">
                        <!-- Menu Utama -->
                        
                        <li class="sidebar-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                            @if($role == 'admin')
                                <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                            @elseif($role == 'guru')
                                <a href="{{ route('guru.dashboard') }}" class='sidebar-link'>
                            @elseif($role == 'bk')
                                <a href="{{ route('bk.dashboard') }}" class='sidebar-link'>
                            @elseif($role == 'waka_kesiswaan')
                                <a href="{{ route('waka.dashboard') }}" class='sidebar-link'>
                            @elseif($role == 'kepala_sekolah')
                                <a href="{{ route('kepsek.dashboard') }}" class='sidebar-link'>
                            @else
                                <a href="{{ route('dashboard') }}" class='sidebar-link'>
                            @endif
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <!-- 1. Master Data (Admin Only) - COLLAPSE -->
                        @if($role == 'admin')
                        @php
                            $isMasterActive = request()->routeIs(['admin.kelas.*', 'admin.mapel.*', 'admin.siswa.*', 'admin.guru.*', 'admin.jam-pelajaran.*', 'admin.penugasan-bk.*']);
                        @endphp
                        <li class="sidebar-item has-sub {{ $isMasterActive ? 'active submenu-open' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Master Data</span>
                            </a>
                            <ul class="submenu {{ $isMasterActive ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.kelas.index') }}" class="submenu-link">Data Kelas</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.mapel.index') }}" class="submenu-link">Mata Pelajaran</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.siswa.index') }}" class="submenu-link">Data Siswa</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.guru.index') }}" class="submenu-link">Data Guru</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.jam-pelajaran.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.jam-pelajaran.index') }}" class="submenu-link">Jam Pelajaran</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('admin.penugasan-bk.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.penugasan-bk.index') }}" class="submenu-link">Penugasan BK</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- 2. Presensi Mengajar (Guru / Admin) - COLLAPSE -->
                        @if(in_array($role, ['admin', 'guru']))
                        @php
                            $isPresensiActive = request()->routeIs(['guru.rekap_kelas.*']);
                        @endphp
                        <li class="sidebar-item has-sub {{ $isPresensiActive ? 'active submenu-open' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-journal-check"></i>
                                <span>Presensi Mengajar</span>
                            </a>
                            <ul class="submenu {{ $isPresensiActive ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('guru.rekap_kelas.*') ? 'active' : '' }}">
                                    <a href="{{ route('guru.rekap_kelas.index') }}" class="submenu-link">Rekap Absen Kelas</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- 3. Bimbingan Konseling - COLLAPSE -->
                        @if(in_array($role, ['admin', 'bk']))
                        @php
                            $isBkActive = request()->routeIs([
                            'koordinator-bk.jenis-pelanggaran.*', 
                            'bk.presensi.*', 'bk.rekap_presensi.*', 
                            'bk.pelanggaran.*']);
                        @endphp
                        <li class="sidebar-item has-sub {{ $isBkActive ? 'active submenu-open' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-person-heart"></i>
                                <span>Bimbingan Konseling</span>
                            </a>
                            <ul class="submenu {{ $isBkActive ? 'active' : '' }}">
                                @if($role == 'admin' || ($role == 'bk' && $isKoordinatorBK))
                                <li class="submenu-item {{ request()->routeIs('koordinator-bk.jenis-pelanggaran.*') ? 'active' : '' }}">
                                    <a href="{{ route('koordinator-bk.jenis-pelanggaran.index') }}" class="submenu-link">Jenis Pelanggaran</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('waka.approval.*') || request()->routeIs('koordinator-bk.approval.*') ? 'active' : '' }}">
                                    @if($role == 'bk' && $isKoordinatorBK)
                                        <a href="{{ route('koordinator-bk.approval.index') }}" class="submenu-link">Persetujuan Pelanggaran</a>
                                    @else
                                        <a href="{{ route('waka.approval.index') }}" class="submenu-link">Persetujuan Pelanggaran</a>
                                    
                                        @endif
                                </li>
                                @endif

                                @if(in_array($role, ['bk', 'admin']))
                                <!-- <li class="submenu-item {{ request()->routeIs('bk.presensi.*') ? 'active' : '' }}">
                                    <a href="{{ route('bk.presensi.index') }}" class="submenu-link">Presensi Kelas Binaan</a>
                                </li> -->
                                <li class="submenu-item {{ request()->routeIs('bk.rekap_presensi.*') ? 'active' : '' }}">
                                    <a href="{{ route('bk.rekap_presensi.index') }}" class="submenu-link">Rekap Presensi Kelas</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('bk.pelanggaran.*') ? 'active' : '' }}">
                                    <a href="{{ route('bk.pelanggaran.index') }}" class="submenu-link">Pelanggaran Siswa</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        <!-- 4. Kesiswaan - COLLAPSE -->
                        @if(in_array($role, ['admin', 'waka_kesiswaan']))
                        @php
                            $isKesiswaanActive = request()->routeIs(['waka.rekap_presensi.*', 'waka.approval.*']);
                        @endphp
                        <li class="sidebar-item has-sub {{ $isKesiswaanActive ? 'active submenu-open' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-shield-check"></i>
                                <span>Kesiswaan</span>
                            </a>
                            <ul class="submenu {{ $isKesiswaanActive ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('waka.rekap_presensi.*') ? 'active' : '' }}">
                                    <a href="{{ route('waka.rekap_presensi.index') }}" class="submenu-link">Rekap Presensi Harian</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('waka.approval.*') ? 'active' : '' }}">
                                    <a href="{{ route('waka.approval.index') }}" class="submenu-link">Persetujuan Pelanggaran</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- 5. Laporan & Eksekutif - COLLAPSE -->
                        @if(in_array($role, ['admin', 'kepala_sekolah']))
                        @php
                            $isEksekutifActive = request()->routeIs(['kepsek.rekap_presensi.*', 'kepsek.pelanggaran.*']);
                        @endphp
                        <li class="sidebar-item has-sub {{ $isEksekutifActive ? 'active submenu-open' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Laporan & Eksekutif</span>
                            </a>
                            <ul class="submenu {{ $isEksekutifActive ? 'active' : '' }}">
                                <li class="submenu-item {{ request()->routeIs('kepsek.rekap_presensi.*') ? 'active' : '' }}">
                                    <a href="{{ route('kepsek.rekap_presensi.index') }}" class="submenu-link">Rekap Presensi Harian</a>
                                </li>
                                <li class="submenu-item {{ request()->routeIs('kepsek.pelanggaran.*') ? 'active' : '' }}">
                                    <a href="{{ route('kepsek.pelanggaran.index') }}" class="submenu-link">Ringkasan Pelanggaran</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <!-- Akun & Logout -->
                        <li class="sidebar-title">Akun & Sistem</li>

                        <li class="sidebar-item">
                            <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="sidebar-link bg-danger text-white">
                                <i class="bi bi-box-arrow-left text-white"></i>
                                <span>Keluar / Logout</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>                        
        
        <!-- Main Content Area -->
        <div id="main" class="layout-navbar">
            <header class="mb-3">
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <div class="navbar-nav ms-auto mb-lg-0">
                                <!-- User Profile Dropdown -->
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none">
                                        <div class="user-menu d-flex align-items-center">
                                            <div class="user-name text-end me-3">
                                                <h6 class="mb-0 text-gray-600 fw-bold">{{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}</h6>
                                                <p class="mb-0 text-sm text-primary fw-semibold">{{ strtoupper(Auth::user()->role->nama_role ?? 'ADMIN') }}</p>
                                            </div>
                                            <div class="user-img d-flex align-items-center">
                                                <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 40px; height: 40px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                                            <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="javascript:void(0)" onclick="document.getElementById('logout-form-navbar').submit()">
                                                <i class="bi bi-box-arrow-left"></i>
                                                <span>Keluar / Logout</span>
                                            </a>
                                            <form action="{{ route('logout') }}" method="POST" id="logout-form-navbar" style="display:none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            <div id="main-content" class="pt-0">
                <div class="page-heading mb-3">
                    <div class="page-title">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3 class="mb-1 fw-bold">@yield('header_title', 'Dashboard')</h3>
                                <p class="text-subtitle text-muted mb-0">Sistem Informasi Presensi & Manajemen Akademik SMK</p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first text-md-end mb-md-0 mb-3">
                                <span class="badge bg-light-primary text-primary px-3 py-2 fs-6 rounded-pill border">
                                    <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Global Alerts -->
                {{-- Toast Container (pojok kanan atas) --}}
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">

                    @if(session('success'))
                    <div id="toastSuccess" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div id="toastError" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div id="toastWarning" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('warning') }}
                            </div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif

                </div>
                
                <div class="page-content">
                    @yield('content')
                </div>

                <footer>
                    <div class="footer clearfix mb-0 text-muted mt-5">
                        <div class="float-start">
                            <p class="mb-0">
                                2026 &copy; <a href="https://rplsmksa.com/" target=_blank>Codepelita</a></p>
                        </div>
                        <div class="float-end">
                            <p class="mb-0">E-Jurnal SMKSA <span class="text-primary fw-semibold">Mazer</span></p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- JS Inti Mazer & Plugins -->
    <script src="{{ asset('mazer/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('mazer/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('mazer/compiled/js/app.js') }}"></script>
    <script src="{{ asset('mazer/extensions/apexcharts/apexcharts.min.js') }}"></script>
    
    <!-- DataTables Offline JS (BS5) -->
    <script src="{{ asset('mazer/extensions/datatables/datatables.min.js') }}"></script>
    
    <!-- Global DataTables Indonesian Language Configuration -->
    <script>
        $(document).ready(function() {
            if (typeof $.fn.DataTable !== 'undefined') {
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total entri)",
                        infoThousands: ".",
                        lengthMenu: "Tampilkan _MENU_ entri per halaman",
                        loadingRecords: "Sedang memuat data...",
                        processing: "Sedang memproses...",
                        search: "Cari data:",
                        searchPlaceholder: "Ketik kata kunci...",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        paginate: {
                            first: '<i class="bi bi-chevron-double-left"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            last: '<i class="bi bi-chevron-double-right"></i>'
                        },
                        aria: {
                            sortAscending: ": aktifkan untuk mengurutkan kolom ke atas",
                            sortDescending: ": aktifkan untuk mengurutkan kolom ke bawah"
                        }
                    },
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Semua"]],
                    responsive: true,
                    autoWidth: false,
                    dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                         "<'row'<'col-sm-12'tr>>" +
                         "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });

                // Auto initialize all tables with .datatable class or #table1 id
                $('.datatable, #table1').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable();
                    }
                });
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        ['toastSuccess', 'toastError', 'toastWarning'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                new bootstrap.Toast(el, { delay: 4000, autohide: true }).show();
            }
        });
    });

    // ============================================================
    // FUNGSI GLOBAL showToast() — dipanggil dari AJAX / view mana pun
    // ============================================================
    window.showToast = function (type, message, delay = 4000) {
        const bgMap = {
            success: 'text-bg-success',
            error:   'text-bg-danger',
            warning: 'text-bg-warning',
            info:    'text-bg-info'
        };
        const iconMap = {
            success: 'bi-check-circle-fill',
            error:   'bi-exclamation-octagon-fill',
            warning: 'bi-exclamation-triangle-fill',
            info:    'bi-info-circle-fill'
        };

        const bg   = bgMap[type]   || 'text-bg-secondary';
        const icon = iconMap[type] || 'bi-info-circle-fill';

        // Cari / buat container
        let container = document.getElementById('globalToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'globalToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
        }

        // Buat elemen toast
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${bg} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        container.appendChild(toastEl);

        // Tampilkan
        const toast = new bootstrap.Toast(toastEl, { delay: delay, autohide: true });
        toast.show();

        // Hapus elemen setelah tertutup, biar DOM tidak menumpuk
        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    };
</script>
    @stack('scripts')
</body>
</html>