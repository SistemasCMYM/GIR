@extends('layouts.dashboard')

@section('title', 'Configuración - Seguridad')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-shield-alt text-danger"></i> Configuración de Seguridad
                        </h1>
                        <p class="text-muted mb-0">Políticas de seguridad y registros de actividad del sistema</p>
                    </div>
                    <a href="{{ route('configuracion.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

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

        <div class="row">
            <!-- Políticas de Contraseñas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lock text-danger"></i> Políticas de Contraseñas
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('configuracion.seguridad.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="longitud_minima" class="form-label">Longitud mínima</label>
                                <input type="number" class="form-control" name="longitud_minima" id="longitud_minima"
                                    value="{{ $configuracion['politica_contrasenas']['longitud_minima'] ?? 8 }}"
                                    min="6" max="20" required>
                                <small class="text-muted">Mínimo 6, máximo 20 caracteres</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requiere_mayusculas"
                                        value="1" id="requiere_mayusculas"
                                        {{ $configuracion['politica_contrasenas']['requiere_mayusculas'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requiere_mayusculas">
                                        Requiere mayúsculas
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requiere_numeros" value="1"
                                        id="requiere_numeros"
                                        {{ $configuracion['politica_contrasenas']['requiere_numeros'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requiere_numeros">
                                        Requiere números
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requiere_caracteres_especiales"
                                        value="1" id="requiere_caracteres_especiales"
                                        {{ $configuracion['politica_contrasenas']['requiere_caracteres_especiales'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requiere_caracteres_especiales">
                                        Requiere símbolos especiales
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="caducidad_dias" class="form-label">Expiración de contraseña (días)</label>
                                <input type="number" class="form-control" name="caducidad_dias" id="caducidad_dias"
                                    value="{{ $configuracion['politica_contrasenas']['caducidad_dias'] ?? 90 }}"
                                    min="0" max="365">
                                <small class="text-muted">0 = nunca expira</small>
                            </div>

                            <div class="mb-3">
                                <label for="intentos_fallidos" class="form-label">Intentos fallidos máximos</label>
                                <input type="number" class="form-control" name="intentos_fallidos" id="intentos_fallidos"
                                    value="{{ $configuracion['politica_contrasenas']['intentos_fallidos'] ?? 5 }}"
                                    min="1" max="20">
                                <small class="text-muted">Número de intentos antes de bloquear cuenta</small>
                            </div>

                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save"></i> Guardar Políticas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Configuración de Sesiones -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-clock text-warning"></i> Configuración de Sesiones
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('configuracion.seguridad.update.sesiones') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="tiempo_inactividad" class="form-label">Tiempo de inactividad (minutos)</label>
                                <input type="number" class="form-control" name="tiempo_inactividad"
                                    id="tiempo_inactividad"
                                    value="{{ $configuracion['sesiones']['tiempo_inactividad'] ?? 30 }}" min="5"
                                    max="480" required>
                                <small class="text-muted">Tiempo antes de cerrar sesión por inactividad</small>
                            </div>

                            <div class="mb-3">
                                <label for="bloqueo_ip_intentos" class="form-label">Intentos de IP antes de
                                    bloqueo</label>
                                <input type="number" class="form-control" name="bloqueo_ip_intentos"
                                    id="bloqueo_ip_intentos"
                                    value="{{ $configuracion['sesiones']['bloqueo_ip_intentos'] ?? 10 }}" min="1"
                                    max="50">
                                <small class="text-muted">Número de intentos fallidos por IP</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sesiones_simultaneas"
                                        value="1" id="sesiones_simultaneas"
                                        {{ $configuracion['sesiones']['sesiones_simultaneas'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sesiones_simultaneas">
                                        Permitir sesiones simultáneas
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Configuraciones Adicionales -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-cogs"></i> Configuraciones Adicionales
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('configuracion.seguridad.update.adicional') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="autenticacion_doble_factor"
                                        value="1" id="autenticacion_doble_factor"
                                        {{ $configuracion['autenticacion_doble_factor'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autenticacion_doble_factor">
                                        Habilitar autenticación de dos factores
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="registro_actividad"
                                        value="1" id="registro_actividad"
                                        {{ $configuracion['registro_actividad'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="registro_actividad">
                                        Registrar actividad del sistema
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nivel_log" class="form-label">Nivel de registro</label>
                                <select class="form-control" name="nivel_log" id="nivel_log" required>
                                    <option value="bajo"
                                        {{ ($configuracion['nivel_log'] ?? 'medio') == 'bajo' ? 'selected' : '' }}>
                                        Bajo - Solo eventos críticos
                                    </option>
                                    <option value="medio"
                                        {{ ($configuracion['nivel_log'] ?? 'medio') == 'medio' ? 'selected' : '' }}>
                                        Medio - Eventos importantes
                                    </option>
                                    <option value="alto"
                                        {{ ($configuracion['nivel_log'] ?? 'medio') == 'alto' ? 'selected' : '' }}>
                                        Alto - Todos los eventos
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Logs de Seguridad -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history text-info"></i> Registros Recientes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Evento</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($logs) && count($logs) > 0)
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>
                                                    <small>
                                                        {{ isset($log['fecha']) ? date('d/m/Y H:i', strtotime($log['fecha'])) : 'Sin fecha' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small>{{ $log['evento'] ?? 'Sin descripción' }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $log['usuario'] ?? 'Sistema' }}</small>
                                                </td>
                                                <td>
                                                    @if (isset($log['estado']))
                                                        @if ($log['estado'] == 'exitoso')
                                                            <span class="badge bg-success">Exitoso</span>
                                                        @elseif($log['estado'] == 'fallido')
                                                            <span class="badge bg-danger">Fallido</span>
                                                        @else
                                                            <span
                                                                class="badge bg-secondary">{{ ucfirst($log['estado']) }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle"></i><br>
                                                No hay registros de seguridad disponibles
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if (isset($logs) && count($logs) > 0)
                            <div class="text-center mt-2">
                                <a href="{{ route('configuracion.seguridad.logs') }}"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver todos los registros
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
