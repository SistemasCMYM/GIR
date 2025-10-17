@extends('layouts.dashboard')

@section('title', 'Configuración - Fecha y Hora')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-clock text-warning"></i> Configuración de Fecha y Hora
                        </h1>
                        <p class="text-muted mb-0">Configuración de zona horaria y formatos de fecha del sistema</p>
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Configuración Regional</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('configuracion.fechahora.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="zona_horaria" class="form-label">Zona Horaria</label>
                                        <select class="form-control" name="zona_horaria" id="zona_horaria" required>
                                            @if (isset($zonasHorarias) && is_array($zonasHorarias))
                                                @foreach ($zonasHorarias as $zona)
                                                    <option value="{{ $zona }}"
                                                        {{ isset($configuracion['zona_horaria']) && $configuracion['zona_horaria'] == $zona ? 'selected' : '' }}>
                                                        {{ $zona }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="America/Bogota"
                                                    {{ isset($configuracion['zona_horaria']) && $configuracion['zona_horaria'] == 'America/Bogota' ? 'selected' : '' }}>
                                                    America/Bogota
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="idioma" class="form-label">Idioma</label>
                                        <select class="form-control" name="idioma" id="idioma" required>
                                            <option value="es"
                                                {{ isset($configuracion['idioma']) && $configuracion['idioma'] == 'es' ? 'selected' : '' }}>
                                                Español
                                            </option>
                                            <option value="en"
                                                {{ isset($configuracion['idioma']) && $configuracion['idioma'] == 'en' ? 'selected' : '' }}>
                                                English
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="formato_fecha" class="form-label">Formato de Fecha</label>
                                        <select class="form-control" name="formato_fecha" id="formato_fecha" required>
                                            <option value="d/m/Y"
                                                {{ isset($configuracion['formato_fecha']) && $configuracion['formato_fecha'] == 'd/m/Y' ? 'selected' : '' }}>
                                                DD/MM/AAAA ({{ date('d/m/Y') }})
                                            </option>
                                            <option value="m/d/Y"
                                                {{ isset($configuracion['formato_fecha']) && $configuracion['formato_fecha'] == 'm/d/Y' ? 'selected' : '' }}>
                                                MM/DD/AAAA ({{ date('m/d/Y') }})
                                            </option>
                                            <option value="Y-m-d"
                                                {{ isset($configuracion['formato_fecha']) && $configuracion['formato_fecha'] == 'Y-m-d' ? 'selected' : '' }}>
                                                AAAA-MM-DD ({{ date('Y-m-d') }})
                                            </option>
                                            <option value="d-m-Y"
                                                {{ isset($configuracion['formato_fecha']) && $configuracion['formato_fecha'] == 'd-m-Y' ? 'selected' : '' }}>
                                                DD-MM-AAAA ({{ date('d-m-Y') }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="formato_hora" class="form-label">Formato de Hora</label>
                                        <select class="form-control" name="formato_hora" id="formato_hora" required>
                                            <option value="H:i"
                                                {{ isset($configuracion['formato_hora']) && $configuracion['formato_hora'] == 'H:i' ? 'selected' : '' }}>
                                                24 horas ({{ date('H:i') }})
                                            </option>
                                            <option value="g:i A"
                                                {{ isset($configuracion['formato_hora']) && $configuracion['formato_hora'] == 'g:i A' ? 'selected' : '' }}>
                                                12 horas ({{ date('g:i A') }})
                                            </option>
                                            <option value="H:i:s"
                                                {{ isset($configuracion['formato_hora']) && $configuracion['formato_hora'] == 'H:i:s' ? 'selected' : '' }}>
                                                24 horas con segundos ({{ date('H:i:s') }})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="primer_dia_semana" class="form-label">Primer día de la semana</label>
                                        <select class="form-control" name="primer_dia_semana" id="primer_dia_semana"
                                            required>
                                            <option value="1"
                                                {{ isset($configuracion['primer_dia_semana']) && $configuracion['primer_dia_semana'] == 1 ? 'selected' : '' }}>
                                                Lunes
                                            </option>
                                            <option value="0"
                                                {{ isset($configuracion['primer_dia_semana']) && $configuracion['primer_dia_semana'] == 0 ? 'selected' : '' }}>
                                                Domingo
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="calendario" class="form-label">Tipo de calendario</label>
                                        <select class="form-control" name="calendario" id="calendario" required>
                                            <option value="gregoriano"
                                                {{ isset($configuracion['calendario']) && $configuracion['calendario'] == 'gregoriano' ? 'selected' : '' }}>
                                                Gregoriano
                                            </option>
                                            <option value="julian"
                                                {{ isset($configuracion['calendario']) && $configuracion['calendario'] == 'julian' ? 'selected' : '' }}>
                                                Juliano
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Vista Previa</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Fecha Actual</label>
                            <div class="border rounded p-2 bg-light">
                                <span id="preview-date">
                                    @if (isset($configuracion['formato_fecha']))
                                        {{ date($configuracion['formato_fecha']) }}
                                    @else
                                        {{ date('d/m/Y') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Hora Actual</label>
                            <div class="border rounded p-2 bg-light">
                                <span id="preview-time">
                                    @if (isset($configuracion['formato_hora']))
                                        {{ date($configuracion['formato_hora']) }}
                                    @else
                                        {{ date('H:i') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Zona Horaria</label>
                            <div class="border rounded p-2 bg-light">
                                <span>{{ $configuracion['zona_horaria'] ?? 'America/Bogota' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Información</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Esta configuración afectará:
                        </small>
                        <ul class="small mt-2">
                            <li>Reportes de Hallazgos</li>
                            <li>Evaluaciones Psicosociales</li>
                            <li>Logs del sistema</li>
                            <li>Fechas de instrumentos</li>
                            <li>Notificaciones</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Actualizar vista previa cuando cambien los formatos
                function updatePreview() {
                    const now = new Date();
                    const dateFormat = document.getElementById('formato_fecha').value;
                    const timeFormat = document.getElementById('formato_hora').value;

                    // Simular formatos básicos (en producción usar una librería como moment.js)
                    let datePreview = now.toLocaleDateString('es-CO');
                    let timePreview = now.toLocaleTimeString('es-CO', {
                        hour12: false
                    });

                    if (timeFormat === 'g:i A') {
                        timePreview = now.toLocaleTimeString('es-CO', {
                            hour12: true
                        });
                    }

                    document.getElementById('preview-date').textContent = datePreview;
                    document.getElementById('preview-time').textContent = timePreview;
                }

                // Actualizar vista previa cada segundo
                updatePreview();
                setInterval(updatePreview, 1000);

                // Actualizar cuando cambien los selectores
                document.getElementById('formato_fecha').addEventListener('change', updatePreview);
                document.getElementById('formato_hora').addEventListener('change', updatePreview);
            });
        </script>
    @endpush
@endsection
