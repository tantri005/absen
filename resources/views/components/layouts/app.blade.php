<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Absensi Sekolah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1050;
            background: #0f1d36; display: flex; flex-direction: column;
            transform: translateX(-100%); transition: transform 0.3s ease;
        }
        .sidebar.show { transform: translateX(0); }

        .sidebar-backdrop {
            position: fixed; inset: 0; z-index: 1040;
            background: rgba(0,0,0,0.5); display: none;
        }
        .sidebar-backdrop.show { display: block; }

        /* ── Top Navbar ── */
        .top-navbar {
            position: sticky; top: 0; z-index: 1030;
            height: 64px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.25rem;
            flex-shrink: 0;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: 0; min-height: 100vh;
            display: flex; flex-direction: column;
            background: #e9ecef;
            transition: margin-left 0.3s ease;
        }

        /* ── Nav Links ── */
        .nav-link-sidebar {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.5rem 0.75rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500; color: #94a3b8;
            text-decoration: none; transition: all 0.15s;
        }
        .nav-link-sidebar:hover { background: rgba(255,255,255,0.08); color: #e2e8f0; }
        .nav-link-sidebar.active { background: rgba(99,102,241,0.2); color: #a5b4fc; }
        .nav-section-label {
            padding: 0 0.75rem; font-size: 0.65rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;
        }

        /* ── Toggle Button ── */
        .sidebar-toggle-btn {
            background: none; border: none; cursor: pointer;
            width: 38px; height: 38px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            color: #374151; font-size: 1.35rem; transition: background 0.15s;
        }
        .sidebar-toggle-btn:hover { background: #f3f4f6; }

        /* ── Profile Dropdown ── */
        .profile-dropdown-toggle {
            display: flex; align-items: center; gap: 0.5rem;
            background: none; border: none; cursor: pointer;
            padding: 0.35rem 0.5rem; border-radius: 0.5rem;
            transition: background 0.15s;
        }
        .profile-dropdown-toggle:hover { background: #f3f4f6; }
        .profile-dropdown-toggle::after { display: none; } /* hide default caret */

        .dropdown-menu {
            min-width: 180px; border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 0.75rem; padding: 0.35rem;
        }
        .dropdown-menu .dropdown-item {
            border-radius: 0.5rem; font-size: 0.875rem; padding: 0.5rem 0.75rem;
        }
        .dropdown-menu .dropdown-item:hover { background: #f3f4f6; }
        .dropdown-menu .dropdown-divider { margin: 0.25rem 0; }

        /* ── Today Info ── */
        .today-info { text-align: right; line-height: 1.2; }
        .today-label { font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; }
        .today-date { font-size: 0.78rem; font-weight: 500; color: #374151; }

        /* ── Desktop ── */
        @media (min-width: 992px) {
            .sidebar { transform: translateX(0) !important; }
            .sidebar-backdrop { display: none !important; }
            .main-content { margin-left: 220px; }

            /* Collapsed state */
            .sidebar.collapsed { transform: translateX(-100%) !important; }
            .main-content.expanded { margin-left: 0; }
        }
    </style>
</head>
<body>

    {{-- Mobile Sidebar Backdrop --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        {{-- Logo --}}
        <div class="d-flex flex-column align-items-center justify-content-center" style="padding:1.5rem 1rem 1rem;flex-shrink:0;border-bottom:1px solid rgba(255,255,255,0.08);">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="HadirIN" style="height:52px;">
            </a>
            <span class="fw-bold text-white mt-2" style="font-size:0.9rem;">HadirIN</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
            @if(!auth()->user()->isBendahara())
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="bi bi-file-earmark-text fs-6"></i>
        Dashboard
    </a>
@endif

            @if(auth()->user()->isWalikelas())
                <div class="mt-3 mb-2"><p class="nav-section-label mb-0">Wali Kelas</p></div>
                <a href="{{ route('walikelas.rekap') }}" class="nav-link-sidebar {{ request()->routeIs('walikelas.rekap') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text fs-6"></i> Rekap Kehadiran
                </a>
                <a href="{{ route('walikelas.users') }}" class="nav-link-sidebar {{ request()->routeIs('walikelas.users') ? 'active' : '' }}">
                    <i class="bi bi-people-fill fs-6"></i> Kelola Akun
                </a>
            @endif

            @if(auth()->user()->isSekertaris())
                <div class="mt-3 mb-2"><p class="nav-section-label mb-0">Sekretaris</p></div>
                <a href="{{ route('sekertaris.scan') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.scan') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan fs-6"></i> Scan QR
                </a>
                <a href="{{ route('sekertaris.attendance.manual') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square fs-6"></i> Input Manual
                </a>
                {{-- Laporan Submenu --}}
                <div>
                    <a href="#laporanCollapse" class="nav-link-sidebar d-flex justify-content-between {{ request()->routeIs('sekertaris.recap*') || request()->routeIs('sekertaris.report*') ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('sekertaris.recap*') || request()->routeIs('sekertaris.report*') ? 'true' : 'false' }}">
                        <span><i class="bi bi-file-earmark-bar-graph fs-6 me-2"></i>Laporan</span>
                        <i class="bi bi-chevron-down" style="font-size:0.7rem;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('sekertaris.recap*') || request()->routeIs('sekertaris.report*') ? 'show' : '' }}" id="laporanCollapse">
                        <div class="ps-4 ms-2">
                            <a href="{{ route('sekertaris.recap') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.recap') && !request()->routeIs('sekertaris.recap.*') ? 'active' : '' }}" style="font-size:0.8rem;">Ringkasan Harian</a>
                            <a href="{{ route('sekertaris.recap.masuk') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.recap.masuk') ? 'active' : '' }}" style="font-size:0.8rem;">Rekap Masuk</a>
                            <a href="{{ route('sekertaris.recap.pulang') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.recap.pulang') ? 'active' : '' }}" style="font-size:0.8rem;">Rekap Pulang</a>
                            <a href="{{ route('sekertaris.report.combined') }}" class="nav-link-sidebar {{ request()->routeIs('sekertaris.report.combined') ? 'active' : '' }}" style="font-size:0.8rem;">Laporan Gabungan</a>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->isBendahara())
                <div class="pt-4 pb-2">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bendahara</p>
                </div>
                <a href="{{ route('bendahara.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('bendahara.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
    </svg>
                    Dashboard
                </a>
                <a href="{{ route('bendahara.pemasukan') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('bendahara.pemasukan') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Pemasukan
                </a>
                <a href="{{ route('bendahara.pengeluaran') }}" class="nav-link-sidebar {{ request()->routeIs('bendahara.pengeluaran') ? 'active' : '' }}" style="{{ request()->routeIs('bendahara.pengeluaran') ? 'background:#fef2f2;color:#b91c1c;' : '' }}">
                    <i class="bi bi-dash-circle fs-6"></i> Pengeluaran
                </a>
                <a href="{{ route('bendahara.laporan') }}" class="nav-link-sidebar {{ request()->routeIs('bendahara.laporan') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text fs-6"></i> Laporan
                </a>
            @endif

            @if(auth()->user()->isSiswa())
                <a href="{{ route('siswa.qr') }}" class="nav-link-sidebar {{ request()->routeIs('siswa.qr') ? 'active' : '' }}">
                    <i class="bi bi-qr-code fs-6"></i> Kode QR
                </a>
                <a href="{{ route('siswa.riwayat') }}" class="nav-link-sidebar {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history fs-6"></i> Riwayat
                </a>
            @endif
        </nav>
    </aside>

    {{-- Main Content Area --}}
    <div class="main-content" id="mainContent">

        {{-- Top Navbar --}}
        <div class="top-navbar">
            {{-- Left side: Toggle + Page Title --}}
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle-btn" onclick="toggleSidebar()" id="sidebarToggleBtn" title="Toggle Sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="mb-0 fw-semibold" style="font-size:1.05rem;color:#1f2937;">{{ $title ?? 'Dashboard' }}</h1>
            </div>

            {{-- Right side: Today info + Profile Dropdown --}}
            <div class="d-flex align-items-center gap-3">
                {{-- Today + Date --}}
                <div class="today-info d-none d-sm-block">
                    <div class="today-label">Today</div>
                    <div class="today-date">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="dropdown">
                    <button class="profile-dropdown-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-semibold text-white" style="width:36px;height:36px;background:#4f46e5;font-size:0.8rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        @endif
                        <div class="d-none d-md-block text-start" style="line-height:1.2;">
                            <span class="d-block fw-medium" style="font-size:0.85rem;color:#1f2937;">{{ auth()->user()->name }}</span>
                            <span class="d-block text-capitalize" style="font-size:0.7rem;color:#6b7280;">{{ auth()->user()->role }}</span>
                        </div>
                        <i class="bi bi-chevron-down" style="font-size:0.65rem;color:#9ca3af;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.show') }}">
                                <i class="bi bi-person-circle" style="color:#6366f1;"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Content Scrollable --}}
        <div class="flex-grow-1 overflow-auto" style="background:#e9ecef;">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="container-fluid px-3 px-lg-4 mt-3" style="max-width:1200px;">
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 small shadow-sm">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="container-fluid px-3 px-lg-4 mt-3" style="max-width:1200px;">
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small shadow-sm">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="container-fluid px-3 px-lg-4 py-4" style="max-width:1200px;">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const mainContent = document.getElementById('mainContent');

        function isDesktop() {
            return window.innerWidth >= 992;
        }

        function toggleSidebar() {
            if (isDesktop()) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
        }

        function openSidebar() {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }

        // Close sidebar on window resize to desktop if mobile overlay is open
        window.addEventListener('resize', function() {
            if (isDesktop()) {
                backdrop.classList.remove('show');
            }
        });
    </script>
</body>
</html>
