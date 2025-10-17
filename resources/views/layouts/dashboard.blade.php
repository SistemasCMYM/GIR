<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GIR-365 - Sistema Integral de Riesgos')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- GIR-365 Custom Colors CSS -->
    <link href="{{ asset('css/gir-custom-colors.css') }}" rel="stylesheet">
    <link href="{{ asset('css/gir365.css') }}" rel="stylesheet">

    <!-- GIR-365 Silva Dashboard Styles (Vite compila SCSS a CSS automáticamente) -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        /* Fixed Topbar and Footer Layout */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        #wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Content Page */
        .content-page {
            flex: 1;
            margin-left: 260px;
            margin-top: 70px;
            padding-bottom: 70px;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .content-page .container-fluid {
            flex: 1;
        }

        @media (max-width: 991.98px) {
            .content-page {
                margin-left: 0 !important;
            }
        }

        /* Fixed Footer - Completamente Responsive */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 999;
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
        }

        /* Footer se ajusta cuando hay sidebar visible en desktop */
        @media (min-width: 992px) {
            .footer {
                margin-left: 260px;
                width: calc(100% - 260px);
            }
        }

        .footer .container-fluid {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            max-width: 100%;
            padding: 0 15px;
        }

        .footer .row {
            width: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            flex-wrap: nowrap;
        }

        .footer .col-md-6 {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0 10px;
            flex: 1;
        }

        .footer .col-md-6:first-child {
            justify-content: flex-start;
        }

        .footer .col-md-6:last-child {
            justify-content: flex-end;
        }

        .footer .col-md-6 span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Footer Responsive - Tablets */
        @media (max-width: 991.98px) {
            .footer {
                margin-left: 0 !important;
                width: 100% !important;
                height: auto;
                min-height: 60px;
                padding: 12px 15px;
            }

            .footer .container-fluid {
                padding: 0 10px;
            }

            .footer .row {
                flex-direction: column;
                gap: 8px;
                align-items: center;
            }

            .footer .col-md-6 {
                width: 100%;
                justify-content: center !important;
                text-align: center;
                padding: 0 5px;
            }

            .footer .col-md-6:first-child,
            .footer .col-md-6:last-child {
                justify-content: center !important;
            }

            .footer .col-md-6 span {
                white-space: normal;
            }
        }

        /* Footer Responsive - Mobile */
        @media (max-width: 575.98px) {
            .footer {
                padding: 10px 12px;
                font-size: 0.813rem;
                min-height: 50px;
            }

            .footer .container-fluid {
                padding: 0 5px;
            }

            .footer .row {
                gap: 6px;
            }

            .footer .col-md-6 {
                padding: 0;
            }

            .footer .col-md-6 span {
                font-size: 0.75rem;
                line-height: 1.3;
            }
        }
    </style>
</head>

<body class="gir-silva-dashboard tw-bg-[#f5f7fb] tw-text-gray-900" data-sidebar="default" data-leftbar-size="default"
    data-menu-color="dark">
    {{--
      INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA
      @last_verified: 2025-06-12
      @security_level: critical
    --}}
    @includeIf('gir365.auth_logic_original')
    <div class="gir-silva-dashboard">
        <div id="wrapper" class="app-layout">
            <!-- Sidebar Silva Dashboard -->
            <div class="app-sidebar-menu">
                <div class="logo-box">
                    <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
                        <span class="logo-lg"><i class="fas fa-shield-alt me-2"></i><span
                                class="brand-text">GIR-365</span></span>
                        <span class="logo-sm"><i class="fas fa-shield-alt"></i></span>
                    </a>
                </div>
                <div id="sidebar-menu">
                    <ul>
                        <li class="menuitem-active">
                            <a href="{{ route('dashboard') }}"
                                class="tp-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-title">Módulos Plataforma</li>
                        <li class="menuitem-active">
                            <a href="{{ route('hallazgos.index') }}"
                                class="tp-link {{ request()->routeIs('hallazgos.*') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Hallazgos</span>
                            </a>
                        </li>
                        <li class="menuitem-active">
                            <a href="{{ route('psicosocial.index') }}"
                                class="tp-link {{ request()->routeIs('psicosocial.*') ? 'active' : '' }}">
                                <i class="fas fa-brain"></i>
                                <span>Psicosocial</span>
                            </a>
                        </li>
                        <li class="menu-title">Gestión Administrativa</li>
                        @php
                            $userData = session('user_data') ?: session('usuario_data');
                            $isSuper = false;
                            if ($userData) {
                                $tipo = strtolower($userData['tipo'] ?? ($userData['tipo'] ?? ''));
                                $rol = strtolower($userData['rol'] ?? ($userData['rol'] ?? ''));
                                $isSuper =
                                    in_array($tipo, ['super_admin', 'superadmin', 'root']) ||
                                    in_array($rol, ['super_admin', 'superadmin', 'root']) ||
                                    (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
                            }
                        @endphp
                        @if ($isSuper)
                            <li class="menuitem-active">
                                <a href="/usuarios-admin" class="tp-link">
                                    <i class="fas fa-users"></i>
                                    <span>Administración de Usuarios</span>
                                </a>
                            </li>
                        @endif
                        <li class="menuitem-active">
                            <a href="{{ route('empresa.index') }}"
                                class="tp-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>Administración de Empresa</span>
                            </a>
                        </li>
                        <li class="menuitem-active">
                            <a href="{{ route('gestion-instrumentos.index') }}" class="tp-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Gestión de Instrumentos</span>
                            </a>
                        </li>
                        @if (!empty($isSuper) && $isSuper)
                            <li class="menu-title">Sistema</li>
                            <li class="menuitem-active">
                                <a href="{{ route('configuracion.index') }}"
                                    class="tp-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                                    <i class="fas fa-cog"></i>
                                    <span>Configuración</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="sidebar-overlay" data-sidebar-dismiss aria-hidden="true"></div>

            <!-- Topbar Silva Dashboard -->

            <div class="topbar-custom">
                <div class="container-fluid d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center tw-gap-3">
                        <button
                            class="button-toggle-menu btn btn-sm px-3 font-size-16 header-item d-none d-lg-inline-flex"
                            aria-label="Alternar ancho del menú lateral" type="button">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                        <button
                            class="button-menu-mobile button-toggle-menu btn btn-sm px-3 font-size-16 d-lg-none header-item"
                            aria-label="Abrir menú de navegación" type="button">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                        <h4 class="page-title-main mb-0 tw-text-gray-900 tw-text-lg tw-font-semibold">
                            @yield('title', 'Dashboard')
                        </h4>
                    </div>
                    <ul class="list-unstyled topnav-menu float-end mb-0 tw-flex tw-items-center tw-gap-2">
                        <li class="dropdown nav-user tw-relative">
                            <button
                                class="nav-link user-panel dropdown-toggle tw-bg-transparent tw-border-0 tw-flex tw-items-center tw-px-3 tw-py-2 tw-rounded-lg tw-transition-all tw-duration-300 hover:tw-bg-gray-50"
                                data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown" type="button">
                                @php
                                    $userData = session('user_data') ? (object) session('user_data') : null;
                                    $empresaData = session('empresa_data') ? (object) session('empresa_data') : null;

                                    $userInitials = $userData
                                        ? strtoupper(
                                            substr($userData->nombre ?? ($userData->nick ?? 'J'), 0, 1) .
                                                substr($userData->apellido ?? 'S', 0, 1),
                                        )
                                        : 'JS';
                                    $userName = $userData
                                        ? trim(
                                            ($userData->nombre ?? ($userData->nick ?? 'John')) .
                                                ' ' .
                                                ($userData->apellido ?? 'Smith'),
                                        )
                                        : 'John Smith';
                                    $empresaName =
                                        $empresaData->razon_social ?? ($empresaData->nombre_comercial ?? 'Empresa');
                                @endphp

                                <div
                                    class="tw-w-10 tw-h-10 tw-rounded-full tw-overflow-hidden tw-mr-3 tw-ring-2 tw-ring-gir-primary-200">
                                    <div
                                        class="tw-w-full tw-h-full tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-gold-500 tw-flex tw-items-center tw-justify-center">
                                        <span
                                            class="tw-text-white tw-font-semibold tw-text-sm">{{ $userInitials }}</span>
                                    </div>
                                </div>
                                <div
                                    class="user-info tw-hidden md:tw-flex tw-flex-col tw-items-start tw-leading-tight tw-mr-2">
                                    <span
                                        class="user-name tw-font-medium tw-text-sm tw-text-gray-800">{{ $userName }}</span>
                                    <span
                                        class="user-role tw-text-xs tw-text-gray-500 tw-truncate tw-max-w-28">{{ $empresaName }}</span>
                                </div>

                            </button>

                            <!-- Dropdown Menu -->
                            <ul
                                class="dropdown-menu dropdown-menu-end profile-dropdown tw-min-w-[240px] tw-bg-white tw-rounded-xl tw-shadow-2xl tw-border-0 tw-p-3 tw-mt-2">
                                <!-- Welcome Header -->
                                <li class="tw-px-3 tw-py-3 tw-border-b tw-border-gray-100 tw-mb-2">
                                    <div class="tw-flex tw-items-center">
                                        <div
                                            class="tw-w-12 tw-h-12 tw-rounded-full tw-overflow-hidden tw-mr-3 tw-ring-2 tw-ring-gir-primary-200">
                                            <div
                                                class="tw-w-full tw-h-full tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-gold-500 tw-flex tw-items-center tw-justify-center">
                                                <span class="tw-text-white tw-font-semibold">{{ $userInitials }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <!-- Menu Items -->
                                <li>
                                    <a class="dropdown-item tw-flex tw-items-center tw-px-4 tw-py-3 tw-rounded-lg tw-text-gray-700 hover:tw-bg-gir-primary-50 hover:tw-text-gir-primary-700 tw-transition-all tw-duration-200 tw-group"
                                        href="{{ route('perfil') }}">
                                        <div
                                            class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-blue-100 tw-flex tw-items-center tw-justify-center tw-mr-3 group-hover:tw-bg-gir-primary-100">
                                            <i
                                                class="fas fa-user-circle tw-text-blue-600 group-hover:tw-text-gir-primary-600 tw-text-sm"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-sm">Mi cuenta</span>
                                    </a>
                                </li>

                                @if (!empty($isSuper) && $isSuper)
                                    <li>
                                        <a class="dropdown-item tw-flex tw-items-center tw-px-4 tw-py-3 tw-rounded-lg tw-text-gray-700 hover:tw-bg-green-50 hover:tw-text-green-700 tw-transition-all tw-duration-200 tw-group"
                                            href="{{ route('configuracion.index') }}">
                                            <div
                                                class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-green-100 tw-flex tw-items-center tw-justify-center tw-mr-3 group-hover:tw-bg-green-100">
                                                <i
                                                    class="fas fa-cog tw-text-green-600 group-hover:tw-text-green-600 tw-text-sm"></i>
                                            </div>
                                            <span class="tw-font-medium tw-text-sm">Configuración</span>
                                        </a>
                                    </li>
                                @endif

                                <!-- Divider -->
                                <li>
                                    <hr class="dropdown-divider tw-my-3 tw-border-gray-200">
                                </li>

                                <!-- Logout -->
                                <li>
                                    <a class="dropdown-item tw-flex tw-items-center tw-px-4 tw-py-3 tw-rounded-lg tw-text-gray-700 hover:tw-bg-red-50 hover:tw-text-red-700 tw-transition-all tw-duration-200 tw-group"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <div
                                            class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-red-100 tw-flex tw-items-center tw-justify-center tw-mr-3 group-hover:tw-bg-red-100">
                                            <i
                                                class="fas fa-sign-out-alt tw-text-red-600 group-hover:tw-text-red-600 tw-text-sm"></i>
                                        </div>
                                        <span class="tw-font-medium tw-text-sm">Cerrar sesión</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="tw-hidden">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Contenido principal Silva Dashboard -->
            <div class="content-page">
                <div class="container-fluid tw-space-y-6">
                    @if (!empty(trim($__env->yieldContent('breadcrumb'))))
                        <div class="page-title-box mb-3">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <h4 class="page-title tw-text-gray-900 tw-font-semibold">
                                        @yield('page-title', 'Dashboard')
                                    </h4>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-end">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('dashboard') }}">
                                                <i class="fas fa-home"></i> Inicio
                                            </a>
                                        </li>
                                        @yield('breadcrumb')
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show gir-slide-up tw-bg-white tw-border tw-border-green-200 tw-text-green-700"
                            role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show gir-slide-up tw-bg-white tw-border tw-border-red-200 tw-text-red-700"
                            role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show gir-slide-up tw-bg-white tw-border tw-border-yellow-200 tw-text-amber-700"
                            role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>

            <!-- Fixed Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                            <span class="text-dark fw-medium">
                                &copy; {{ date('Y') }} <strong>GIR-365</strong> -
                                {{ $empresaData->razon_social ?? 'Sistema' }}
                            </span>
                        </div>
                        <div class="col-12 col-md-6 text-center text-md-end">
                            <span class="text-muted small">
                                Powered by Laravel {{ app()->version() }} & MongoDB
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite('resources/js/pages/analytics-dashboard.init.js')
    @stack('scripts')

    <!-- Silva Dashboard Force Styles -->
    <script>
        // Lock Screen Functionality
        function lockScreen() {
            // Create lock screen overlay
            const lockOverlay = document.createElement('div');
            lockOverlay.className =
                'lock-screen-overlay tw-fixed tw-inset-0 tw-bg-gray-900 tw-bg-opacity-95 tw-z-50 tw-flex tw-items-center tw-justify-center';
            lockOverlay.innerHTML = `
                <div class="lock-screen-card tw-bg-white tw-rounded-xl tw-shadow-2xl tw-p-8 tw-max-w-md tw-w-full tw-mx-4">
                    <div class="tw-text-center tw-mb-6">
                        <div class="tw-w-20 tw-h-20 tw-bg-gradient-to-br tw-from-gir-primary-500 tw-to-gir-gold-500 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                            <i class="fas fa-lock tw-text-white tw-text-2xl"></i>
                        </div>
                        <h3 class="tw-text-xl tw-font-semibold tw-text-gray-800 tw-mb-2">Screen Locked</h3>
                        <p class="tw-text-gray-600 tw-text-sm">Enter your password to unlock</p>
                    </div>
                    <form onsubmit="unlockScreen(event)" class="tw-space-y-4">
                        <div>
                            <input type="password" id="unlockPassword" placeholder="Enter password" 
                                   class="tw-w-full tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-rounded-lg tw-focus:ring-2 tw-focus:ring-gir-primary-500 tw-focus:border-transparent tw-outline-none">
                        </div>
                        <button type="submit" class="tw-w-full tw-bg-gradient-to-r tw-from-gir-primary-500 tw-to-gir-gold-500 tw-text-white tw-py-3 tw-rounded-lg tw-font-medium tw-hover:shadow-lg tw-transition-all tw-duration-200">
                            Unlock
                        </button>
                    </form>
                    <div class="tw-text-center tw-mt-6">
                        <a href="{{ route('logout') }}" class="tw-text-red-600 tw-text-sm tw-hover:text-red-700 tw-transition-colors" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign out instead
                        </a>
                    </div>
                </div>
            `;

            document.body.appendChild(lockOverlay);
            setTimeout(() => document.getElementById('unlockPassword').focus(), 100);
        }

        function unlockScreen(event) {
            event.preventDefault();
            const password = document.getElementById('unlockPassword').value;
            // Simple demo - in real app, verify password
            if (password === 'admin' || password === '123456' || password.length > 3) {
                const overlay = document.querySelector('.lock-screen-overlay');
                overlay.remove();
            } else {
                const input = document.getElementById('unlockPassword');
                input.value = '';
                input.placeholder = 'Wrong password, try again';
                input.classList.add('tw-border-red-500');
                setTimeout(() => {
                    input.classList.remove('tw-border-red-500');
                    input.placeholder = 'Enter password';
                }, 2000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Force apply Silva Dashboard styles
            console.log('🎨 GIR-365 Silva Dashboard with Enhanced Dropdown Initialized');

            // Ensure all elements have proper classes
            const body = document.body;
            const sidebar = document.querySelector('.app-sidebar-menu');
            const topbar = document.querySelector('.topbar-custom');
            const content = document.querySelector('.content-page');

            if (sidebar) {
                console.log('✅ Sidebar found and styled');
                sidebar.style.display = 'block';
            }
            if (topbar) {
                console.log('✅ Topbar found and styled');
                topbar.style.display = 'block';
            }
            if (content) {
                console.log('✅ Content area found and styled');
                content.style.display = 'block';
            }

            // Initialize Bootstrap dropdowns with enhanced functionality
            const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            const dropdownList = [...dropdownElementList].map(dropdownToggleEl => {
                const dropdown = new bootstrap.Dropdown(dropdownToggleEl);

                // Add enhanced interaction
                dropdownToggleEl.addEventListener('show.bs.dropdown', function() {
                    console.log('🔽 Dropdown opening');
                    this.setAttribute('aria-expanded', 'true');
                    // Add rotation to chevron
                    const chevron = this.querySelector('.fa-chevron-down');
                    if (chevron) {
                        chevron.style.transform = 'rotate(180deg)';
                    }
                });

                dropdownToggleEl.addEventListener('hide.bs.dropdown', function() {
                    console.log('🔼 Dropdown closing');
                    this.setAttribute('aria-expanded', 'false');
                    // Reset chevron rotation
                    const chevron = this.querySelector('.fa-chevron-down');
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                });

                return dropdown;
            });

            // Add specific handling for user dropdown with Tailwind classes
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {

                // Enhanced hover effects
                userDropdown.addEventListener('mouseenter', function() {
                    this.classList.add('tw-scale-105');
                });

                userDropdown.addEventListener('mouseleave', function() {
                    this.classList.remove('tw-scale-105');
                });
            }

            // Add click outside to close dropdown functionality
            document.addEventListener('click', function(e) {
                const isDropdownButton = e.target.closest('[data-bs-toggle="dropdown"]');
                const isDropdownMenu = e.target.closest('.dropdown-menu');

                if (!isDropdownButton && !isDropdownMenu) {
                    // Close all open dropdowns
                    dropdownList.forEach(dropdown => {
                        dropdown.hide();
                    });
                }
            });
        });
    </script>
</body>

</html>
