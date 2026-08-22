<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Absen SMK')</title>
    
    <!-- CSS Inti Mazer & Icons -->
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/compiled/css/app-dark.css') }}">
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
    <script src="{{ asset('mazer/static/js/initTheme.js') }}"></script>
    
    <div id="app">
        <!-- Sidebar Navigation -->
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                                <div>
                                    <h4 class="text-primary mb-0 fw-bold">E-Absen</h4>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Theme Toggle Dark / Light -->
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                role="img" class="iconify iconify--system-uicons" width="20" height="20"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                        opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                                viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-menu">
                    <ul class="menu">
                        <!-- Menu Utama -->
                        
                        <li class="sidebar-item {{ (request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')) ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        
                        <!-- Master Data -->
                        <li class="sidebar-title">Master Data</li>
                        
                        <li class="sidebar-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.kelas.index') }}" class='sidebar-link'>
                                <i class="bi bi-door-open-fill"></i>
                                <span>Data Kelas</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.mapel.index') }}" class='sidebar-link'>
                                <i class="bi bi-book-half"></i>
                                <span>Mata Pelajaran</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.siswa.index') }}" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Data Siswa</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.guru.index') }}" class='sidebar-link'>
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Data Guru</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.jam-pelajaran.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.jam-pelajaran.index') }}" class='sidebar-link'>
                                <i class="bi bi-clock-history"></i>
                                <span>Jam Pelajaran</span>
                            </a>
                        </li>

                        <!-- Penugasan & Akademik -->
                        <li class="sidebar-title">Penugasan & Akademik</li>

                        <li class="sidebar-item {{ request()->routeIs('admin.penugasan.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.penugasan.index') }}" class='sidebar-link'>
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Penugasan Guru Mapel</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('admin.penugasan-bk.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.penugasan-bk.index') }}" class='sidebar-link'>
                                <i class="bi bi-person-check-fill"></i>
                                <span>Penugasan Guru BK</span>
                            </a>
                        </li>

                        <!-- Bimbingan Konseling -->
                        <li class="sidebar-title">Bimbingan Konseling</li>

                        <li class="sidebar-item {{ request()->routeIs('admin.jenis-pelanggaran.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.jenis-pelanggaran.index') }}" class='sidebar-link'>
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>Jenis Pelanggaran</span>
                            </a>
                        </li>

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
                                <div class="user-menu d-flex align-items-center">
                                    <div class="user-name text-end me-3">
                                        <h6 class="mb-0 text-gray-600 fw-bold">{{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}</h6>
                                        <p class="mb-0 text-sm text-primary fw-semibold">{{ strtoupper(Auth::user()->role->nama_role ?? 'ADMIN') }}</p>
                                    </div>
                                    <div class="user-img d-flex align-items-center">
                                        <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill fs-4"></i>
                                        </div>
                                    </div>
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
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                            <div>{{ session('warning') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="page-content">
                    @yield('content')
                </div>

                <footer>
                    <div class="footer clearfix mb-0 text-muted mt-5">
                        <div class="float-start">
                            <p class="mb-0">2026 &copy; E-Absen SMK - Solusi Presensi Digital</p>
                        </div>
                        <div class="float-end">
                            <p class="mb-0">Dibuat dengan template <span class="text-primary fw-semibold">Mazer</span></p>
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
    @stack('scripts')
</body>
</html>