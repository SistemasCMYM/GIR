@extends('layouts.dashboard')
@section('title', 'Configuración del Sistema')
@section('page-title', 'Configuración del Sistema')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">
                        <i class="fas fa-cog me-2"></i>
                        Configuración del Sistema
                    </h4>
                    <p class="text-muted mb-0">Administre todas las configuraciones y parámetros del sistema</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Configuración
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        @php
            // Fallbacks simples para evitar errores si no llegan variables
            $securityConfig = $securityConfig ?? [
                'force_https' => false,
                'two_step_auth' => false,
                'rate_limiting' => 60,
                'session_timeout' => 30,
            ];
            $modulesConfig = $modulesConfig ?? [];
            $maintenanceInfo = $maintenanceInfo ?? [];
            $databaseConfig = $databaseConfig ?? [
                'mongodb_connected' => false,
                'mongodb_status' => 'Desconocido',
                'sqlite_size' => '0 MB',
                'active_connections' => 0,
            ];
            $backupsDisponibles = $backupsDisponibles ?? [];
        @endphp

        <!-- Statistics Cards Silva Dashboard -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="fas fa-server fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ config('app.env', 'prod') }}</h4>
                                <p class="text-muted mb-0">Entorno</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="fas fa-bug fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ config('app.debug') ? 'ON' : 'OFF' }}</h4>
                                <p class="text-muted mb-0">Debug</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="fas fa-database fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $databaseConfig['active_connections'] }}</h4>
                                <p class="text-muted mb-0">Conexiones</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-secondary">
                                    <span class="avatar-title rounded-circle bg-secondary">
                                        <i class="fas fa-clock fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $securityConfig['session_timeout'] }}</h4>
                                <p class="text-muted mb-0">Sesión (min)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Sections Silva Dashboard -->
        <div class="row">
            <!-- Configuración General -->
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-cog me-2"></i>
                                Configuración General
                            </h4>
                            <button class="btn btn-sm btn-outline-primary" onclick="editarConfiguracion('general')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Configuración general del sistema</h6>
                        <ul class="list-unstyled small mb-3">
                            <li class="py-1 d-flex justify-content-between"><span>Nombre del Sistema</span><span
                                    class="text-muted">{{ config('app.name', 'Laravel365') }}</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Entorno</span><span
                                    class="badge bg-{{ config('app.env') === 'production' ? 'success' : 'warning' }}">{{ config('app.env') }}</span>
                            </li>
                            <li class="py-1 d-flex justify-content-between"><span>Debug</span><span
                                    class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">{{ config('app.debug') ? 'Activo' : 'Off' }}</span>
                            </li>
                            <li class="py-1 d-flex justify-content-between"><span>Timezone</span><span
                                    class="text-muted">{{ config('app.timezone', 'UTC') }}</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Laravel</span><span
                                    class="text-muted">{{ app()->version() }}</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>PHP</span><span
                                    class="text-muted">{{ phpversion() }}</span></li>
                        </ul>
                        <h6 class="small text-uppercase fw-bold text-muted mb-2">Idioma y localización</h6>
                        <ul class="small list-unstyled mb-3">
                            <li class="py-1 d-flex justify-content-between"><span>Idioma</span><span
                                    class="text-muted">es-CO</span>
                            </li>
                            <li class="py-1 d-flex justify-content-between"><span>Zona horaria</span><span
                                    class="text-muted">{{ config('app.timezone', 'UTC') }}</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Moneda</span><span class="text-muted">COP
                                    (₱)</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Formato Fecha</span><span
                                    class="text-muted">d/m/Y</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Formato Hora</span><span
                                    class="text-muted">24h</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Separador Decimal</span><span
                                    class="text-muted">,</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Separador Miles</span><span
                                    class="text-muted">.</span></li>
                        </ul>
                        <h6 class="small text-uppercase fw-bold text-muted mb-2">Branding / Logo</h6>
                        <ul class="small list-unstyled mb-3">
                            <li class="py-1 d-flex justify-content-between"><span>Logo Principal</span><span
                                    class="badge bg-secondary">Defecto</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Color Primario</span><span
                                    class="badge bg-dark">#1a1a1a</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Color Secundario</span><span
                                    class="badge bg-warning text-dark">#D1A854</span></li>
                        </ul>
                        <h6 class="small text-uppercase fw-bold text-muted mb-2">Plantillas de correo</h6>
                        <ul class="small list-unstyled mb-0">
                            <li class="py-1 d-flex justify-content-between"><span>Bienvenida</span><span
                                    class="badge bg-success">Activa</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Recuperación</span><span
                                    class="badge bg-success">Activa</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Notificación Alerta</span><span
                                    class="badge bg-warning text-dark">Pendiente</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Parámetros Funcionales -->
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-sliders-h me-2"></i>
                                Parámetros Funcionales
                            </h4>
                            <button class="btn btn-sm btn-outline-primary" onclick="editarConfiguracion('parametros')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Parámetros funcionales del sistema</h6>
                        <div class="row g-3 small">
                            <div class="col-6">
                                <div class="p-2 rounded bg-glass border border-opacity-25">
                                    <strong>HTTPS Forzado</strong><br>
                                    <span
                                        class="badge bg-{{ $securityConfig['force_https'] ? 'success' : 'secondary' }}">{{ $securityConfig['force_https'] ? 'Sí' : 'No' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-glass border border-opacity-25">
                                    <strong>2FA</strong><br>
                                    <span
                                        class="badge bg-{{ $securityConfig['two_step_auth'] ? 'success' : 'secondary' }}">{{ $securityConfig['two_step_auth'] ? 'Activo' : 'No' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-glass border border-opacity-25">
                                    <strong>Rate Limit</strong><br>
                                    <span class="text-muted">{{ $securityConfig['rate_limiting'] }} req/min</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-glass border border-opacity-25">
                                    <strong>Sesión</strong><br>
                                    <span class="text-muted">{{ $securityConfig['session_timeout'] }} min</span>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <h6 class="text-uppercase small fw-bold mb-2">Opciones / Reglas por módulo</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @forelse($modulesConfig as $key=>$module)
                                <span class="badge rounded-pill bg-{{ $module['enabled'] ? 'success' : 'dark' }}"
                                    style="font-size:.7rem">{{ $module['name'] }}</span>
                            @empty
                                <span class="text-muted small">Sin módulos definidos</span>
                            @endforelse
                        </div>
                        <h6 class="text-uppercase small fw-bold mb-2">Activación / Funcionalidades</h6>
                        <ul class="small list-unstyled mb-3">
                            <li class="py-1 d-flex justify-content-between"><span>Módulo Hallazgos</span><span
                                    class="badge bg-success">Activo</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Módulo Psicosocial</span><span
                                    class="badge bg-success">Activo</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Informes Avanzados</span><span
                                    class="badge bg-warning text-dark">Beta</span></li>
                        </ul>
                        <h6 class="text-uppercase small fw-bold mb-2">Impuestos / Tasas</h6>
                        <ul class="small list-unstyled mb-0">
                            <li class="py-1 d-flex justify-content-between"><span>IVA</span><span
                                    class="text-muted">19%</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Descuento Global</span><span
                                    class="text-muted">0%</span></li>
                            <li class="py-1 d-flex justify-content-between"><span>Tasa Cambio</span><span
                                    class="text-muted">{{ number_format(4000, 0, ',', '.') }} COP/USD</span></li>
                        </ul>
                        </x-admin.data-panel>
                    </div>

                    <!-- Bloque 3: Notificaciones y Alertas -->
                    <div class="col-xl-6">
                        <x-admin.data-panel icon="fas fa-bell" title="Notificaciones y Alertas" :actions="'<button class=\'btn btn-sm btn-outline-primary\' onclick=\'editarConfiguracion(\'notificaciones\')\'><i class=\'fas fa-edit\'></i></button>'">
                            <h6 class="small text-uppercase fw-bold text-muted mb-2">3. Notificaciones y alertas</h6>
                            <ul class="list-unstyled small mb-3">
                                <li class="d-flex justify-content-between py-1"><span>Correo Saliente</span><span
                                        class="badge bg-secondary">SMTP</span></li>
                                <li class="d-flex justify-content-between py-1"><span>Alertas Backup</span><span
                                        class="badge bg-success">Activas</span></li>
                                <li class="d-flex justify-content-between py-1"><span>Alertas Seguridad</span><span
                                        class="badge bg-warning text-dark">Pendiente</span></li>
                            </ul>
                            <h6 class="small text-uppercase fw-bold text-muted mb-2">Eventos que generan alertas</h6>
                            <ul class="small list-unstyled mb-3">
                                <li class="py-1">Fallo de backup</li>
                                <li class="py-1">Intentos fallidos de acceso</li>
                                <li class="py-1">Actualizaciones pendientes</li>
                            </ul>
                            <h6 class="small text-uppercase fw-bold text-muted mb-2">Canales</h6>
                            <ul class="small list-unstyled mb-3">
                                <li class="py-1 d-flex justify-content-between"><span>Email</span><span
                                        class="badge bg-success">Activo</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>In-App</span><span
                                        class="badge bg-success">Activo</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>SMS</span><span
                                        class="badge bg-secondary">No</span></li>
                            </ul>
                            <h6 class="small text-uppercase fw-bold text-muted mb-2">Frecuencia</h6>
                            <ul class="small list-unstyled mb-3">
                                <li class="py-1 d-flex justify-content-between"><span>Resumen Diario</span><span
                                        class="text-muted">08:00</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>Resumen Semanal</span><span
                                        class="text-muted">Lunes</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>Críticas</span><span
                                        class="badge bg-danger">Inmediatas</span></li>
                            </ul>
                            <div class="small text-muted">Configura canales y severidades para eventos críticos.
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" onclick="testNotificacion()"><i
                                        class="fas fa-paper-plane me-1"></i> Test</button>
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="editarConfiguracion('canales')"><i class="fas fa-bell me-1"></i>
                                    Canales</button>
                            </div>
                        </x-admin.data-panel>
                    </div>

                    <!-- Bloque 4: Interfaz de Usuario -->
                    <div class="col-xl-6">
                        <x-admin.data-panel icon="fas fa-palette" title="Interfaz de Usuario" :actions="'<button class=\'btn btn-sm btn-outline-primary\' onclick=\'editarConfiguracion(\'ui\')\'><i class=\'fas fa-edit\'></i></button>'">
                            <h6 class="small text-uppercase fw-bold text-muted mb-2">4. Interfaz de usuario</h6>
                            <div class="row g-3 small">
                                <div class="col-6">
                                    <div class="p-2 bg-glass rounded border"><strong>Tema</strong><br><span
                                            class="text-muted">Moderno</span></div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-glass rounded border"><strong>Accesibilidad</strong><br><span
                                            class="badge bg-success">Base</span></div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 bg-glass rounded border"><strong>Paleta</strong><br><span
                                            class="text-muted">Paleta corporativa original</span></div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <h6 class="small text-uppercase fw-bold mb-2">Layouts / Paneles</h6>
                            <ul class="small list-unstyled mb-3">
                                <li class="py-1 d-flex justify-content-between"><span>Inicio Moderno</span><span
                                        class="badge bg-success">Activo</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>Panel Compacto</span><span
                                        class="badge bg-secondary">No</span></li>
                            </ul>
                            <h6 class="small text-uppercase fw-bold mb-2">Preferencias del usuario</h6>
                            <ul class="small list-unstyled mb-0">
                                <li class="py-1 d-flex justify-content-between"><span>Animaciones</span><span
                                        class="badge bg-success">Activas</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>Glass Depth</span><span
                                        class="text-muted">Nivel
                                        2</span></li>
                                <li class="py-1 d-flex justify-content-between"><span>Compact Tables</span><span
                                        class="badge bg-info">On</span></li>
                            </ul>
                        </x-admin.data-panel>
                    </div>
                </div>
            @endsection

            @push('scripts')
                <script>
                    function editarConfiguracion(seccion) {
                        console.log('Editar sección', seccion);
                    }

                    function testNotificacion() {
                        console.log('Test notificación');
                    }
                </script>
            @endpush
