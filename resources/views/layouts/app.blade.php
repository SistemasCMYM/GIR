<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

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

    <!-- GIR-365 Silva Dashboard Styles (Override Bootstrap) -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- GIR-365 Silva Variables -->
    <style>
        :root {
            --bs-primary: #D1A854;
            --bs-primary-rgb: 209, 168, 84;
            --bs-secondary: #EDC979;
        }
    </style>

    @stack('styles')
</head>

<body class="gir-silva-dashboard" data-sidebar="default">
    <!-- Silva Dashboard Layout -->
    <div id="app-layout">
        <!-- Silva Sidebar -->
        <div class="app-sidebar-menu">
            <!-- Logo Box -->
            <div class="logo-box">
                <a href="{{ route('empleados.index') }}" class="logo text-center">
                    <i class="fas fa-shield-alt me-2"></i>
                    <span class="logo-lg">GIR-365</span>
                </a>
            </div>

            <!-- Silva Sidebar Menu -->
            <div id="sidebar-menu">
                <ul class="list-unstyled">
                    <!-- Dashboard -->
                    <li class="menu-title">Inicio</li>
                    <li class="menuitem {{ request()->routeIs('dashboard') ? 'menuitem-active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="tp-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Módulos Plataforma -->
                    <li class="menu-title">Módulos Plataforma</li>
                    <li class="menuitem {{ request()->routeIs('hallazgos.*') ? 'menuitem-active' : '' }}">
                        <a href="{{ route('hallazgos.index') }}" class="tp-link">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Hallazgos</span>
                        </a>
                    </li>
                    <li class="menuitem {{ request()->routeIs('psicosocial.*') ? 'menuitem-active' : '' }}">
                        <a href="{{ route('psicosocial.index') }}" class="tp-link">
                            <i class="fas fa-brain"></i>
                            <span>Psicosocial</span>
                        </a>
                    </li>

                    <!-- Gestión Administrativa -->
                    <li class="menu-title">Gestión Administrativa</li>
                    <li class="menuitem {{ request()->routeIs('usuarios.*') ? 'menuitem-active' : '' }}">
                        <a href="#usuarios-menu" data-bs-toggle="collapse" class="tp-link">
                            <i class="fas fa-users"></i>
                            <span>Administración de Usuarios</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('usuarios.*') ? 'show' : '' }}" id="usuarios-menu">
                            <ul class="sub-menu">
                                <li
                                    class="menuitem {{ request()->routeIs('usuarios.cuentas.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('usuarios.cuentas.index') }}" class="tp-link">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Creación de cuentas</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('usuarios.roles.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('usuarios.roles.index') }}" class="tp-link">
                                        <i class="fas fa-user-tag"></i>
                                        <span>Perfiles/roles</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('usuarios.permisos.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('usuarios.permisos.index') }}" class="tp-link">
                                        <i class="fas fa-key"></i>
                                        <span>Permisos</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="menuitem {{ request()->routeIs('empresa.*') ? 'menuitem-active' : '' }}">
                        <a href="#empresa-menu" data-bs-toggle="collapse" class="tp-link">
                            <i class="fas fa-building"></i>
                            <span>Administración de Empresa</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('empresa.*') ? 'show' : '' }}" id="empresa-menu">
                            <ul class="sub-menu">
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.empleados.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.empleados.index') }}" class="tp-link">
                                        <i class="fas fa-upload"></i>
                                        <span>Cargue de empleados</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.empresas.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.empresas.index') }}" class="tp-link">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Creación de empresas</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.areas.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.areas.index') }}" class="tp-link">
                                        <i class="fas fa-sitemap"></i>
                                        <span>Cargue de Áreas</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.centros.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.centros.index') }}" class="tp-link">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Cargue de Centros</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.ciudades.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.ciudades.index') }}" class="tp-link">
                                        <i class="fas fa-city"></i>
                                        <span>Cargue de Ciudades</span>
                                    </a>
                                </li>
                                <li
                                    class="menuitem {{ request()->routeIs('empresa.procesos.*') ? 'menuitem-active' : '' }}">
                                    <a href="{{ route('empresa.procesos.index') }}" class="tp-link">
                                        <i class="fas fa-cogs"></i>
                                        <span>Cargue de Procesos</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Configuración Sistema -->
                    @php
                        $userData = session('user_data') ?: session('usuario_data');
                        $rol = strtolower($userData['rol'] ?? ($userData['role'] ?? ($userData['tipo'] ?? '')));
                        $isSuper =
                            in_array(
                                $rol,
                                ['super_admin', 'superadmin', 'super administrator', 'superadministrador', 'root'],
                                true,
                            ) ||
                            (!empty($userData['is_super_admin']) && $userData['is_super_admin'] === true);
                    @endphp
                    @if ($isSuper)
                        <li class="menu-title">Sistema</li>
                        <li class="menuitem {{ request()->routeIs('configuracion.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('configuracion.index') }}" class="tp-link">
                                <i class="fas fa-cog"></i>
                                <span>Configuración</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Silva Topbar -->
            <div class="topbar-custom">
                <div class="container-fluid">
                    <div class="topbar-nav">
                        <!-- Mobile Menu Button -->
                        <button type="button" class="btn btn-sm button-menu-mobile d-lg-none">
                            <i class="fas fa-bars"></i>
                        </button>

                        <!-- Page Title -->
                        <div class="page-title-box">
                            <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
                        </div>

                        <!-- Topbar Menu -->
                        <ul class="topnav-menu navbar-nav ms-auto">
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    @php
                                        $usuario = session('usuario_data') ? (object) session('usuario_data') : null;
                                        $empresa = session('empresa') ? (object) session('empresa') : null;
                                        $userInitials = $usuario
                                            ? strtoupper(
                                                substr($usuario->nombre ?? 'U', 0, 1) .
                                                    substr($usuario->apellido ?? 'S', 0, 1),
                                            )
                                            : 'US';
                                    @endphp

                                    <div class="user-avatar-sm">
                                        {{ $userInitials }}
                                    </div>
                                    <span class="ms-2">{{ $usuario->nombre ?? 'Usuario' }}</span>
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('perfil') }}">
                                            <i class="fas fa-user-circle me-2"></i>
                                            Mi Perfil
                                        </a>
                                    </li>
                                    @if ($isSuper)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('configuracion.index') }}">
                                                <i class="fas fa-cog me-2"></i>
                                                Configuración
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Cerrar Sesión
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Silva Content Page -->
            <div class="content-page">
                <div class="content">
                    <!-- Breadcrumb -->


                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/Inicio-enhanced.js') }}"></script>
        <script>
            // Mobile sidebar toggle
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebar = document.querySelector('.main-sidebar');
                const overlay = document.getElementById('sidebar-overlay');

                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('show');
                        overlay.classList.toggle('show');
                    });
                }

                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    });
                }

                // Auto-hide flash messages
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert-dismissible');
                    alerts.forEach(function(alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);

                // Initialize all Bootstrap dropdowns explicitly (safety across views)
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
                    try {
                        new bootstrap.Dropdown(el);
                    } catch (e) {}
                });

                // Anti-conflict: ensure dropdown toggles even if other handlers hijack clicks
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        // Run early, prevent default navigation to '#', and toggle programmatically
                        const href = this.getAttribute('href') || '';
                        if (href === '#' || href === '#!') e.preventDefault();
                        try {
                            const inst = bootstrap.Dropdown.getOrCreateInstance(this);
                            inst.toggle();
                        } catch (_) {}
                        // Stop propagation so generic document handlers don’t immediately close it
                        e.stopPropagation();
                    }, true);
                });
            }

            // Responsive handler
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    body.classList.remove('sidebar-enable');
                    if (body.getAttribute('data-sidebar') === 'hidden') {
                        body.setAttribute('data-sidebar', 'default');
                    }
                }
            });

            // Click outside to close sidebar (mobile)
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992 && body.classList.contains('sidebar-enable')) {
                    if (sidebarMenu && !sidebarMenu.contains(e.target) && menuToggle && !menuToggle.contains(e
                            .target)) {
                        body.classList.remove('sidebar-enable');
                        body.setAttribute('data-sidebar', 'hidden');
                    }
                }
            });

            // Initialize everything
            initSilvaDashboard(); console.log('✅ Silva Dashboard completamente inicializado');
            });
        </script>

        @stack('scripts')

</body>

</html>
