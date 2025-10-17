@extends('layouts.dashboard')

@section('title', 'Nueva Evaluación Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Nueva Evaluación Psicosocial</h1>
                        <p class="text-muted">Crear nueva evaluación de riesgo psicosocial</p>
                        <p class="text-muted small">A continuación describa el objetivo de esta evaluación y elija un
                            Profesional de su lista para que esté a cargo de este.</p>
                    </div>
                    <div>
                        <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-clipboard-list me-2"></i>Información del Diagnóstico
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('psicosocial.store') }}" id="formDiagnostico">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-edit me-1"></i>Descripción del Diagnóstico <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                                        value="{{ old('descripcion') }}" required
                                        placeholder="Ej: Evaluación Psicosocial 2025">
                                </div>

                                <div class="col-md-6">
                                    <label for="fecha_evaluacion" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Fecha de Evaluación <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="fecha_evaluacion" name="fecha_evaluacion"
                                        value="{{ old('fecha_evaluacion', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="evaluador_id" class="form-label">
                                        <i class="fas fa-user-md me-1"></i>Evaluador/Profesional <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="evaluador_id" name="evaluador_id" required>
                                        <option value="">Seleccionar profesional...</option>
                                        @forelse($profesionales as $profesional)
                                            <option value="{{ $profesional->id }}"
                                                {{ old('evaluador_id') == $profesional->id ? 'selected' : '' }}>
                                                {{ $profesional->nombre }}
                                                @if ($profesional->email)
                                                    ({{ $profesional->email }})
                                                @endif
                                            </option>
                                        @empty
                                            <option value="" disabled>No hay profesionales disponibles</option>
                                        @endforelse
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Solo se muestran usuarios con tipo_cuenta: 'profesional' activos para esta empresa.
                                        @if ($profesionales->isEmpty())
                                            <br><span class="text-warning">No hay profesionales habilitados. Contacte al
                                                administrador.</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-6"> <label for="filtro_empleados" class="form-label">
                                        <i class="fas fa-filter me-1"></i>Filtro de Empleados (por Contrato)
                                    </label>
                                    <select class="form-control" id="filtro_empleados" name="filtro_empleados">
                                        <option value="">No usar filtro (selección manual)</option>
                                        @forelse($filtrosEmpleados as $filtro)
                                            <option
                                                value="{{ json_encode(['tipo' => $filtro['tipo'], 'valor' => $filtro['valor']]) }}"
                                                {{ old('filtro_empleados') == json_encode(['tipo' => $filtro['tipo'], 'valor' => $filtro['valor']]) ? 'selected' : '' }}>
                                                {{ $filtro['descripcion'] }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No hay empleados con contrato disponibles
                                            </option>
                                        @endforelse
                                    </select> <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Seleccione empleados basados en su <strong>contrato_key</strong> de la base de datos
                                        de empresas.
                                        <br>Al seleccionar un filtro, se cambiará <strong>filtro_key: null</strong> y
                                        <strong>filtro: false → true</strong>.
                                        <br>Los empleados seleccionados aparecerán como <strong>pendientes por
                                            asignar</strong> en la tarjeta.
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="observaciones" class="form-label">
                                        <i class="fas fa-comment me-1"></i>Observaciones
                                    </label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="4" maxlength="255"
                                        placeholder="Describa el objetivo de esta evaluación y cualquier observación relevante...">{{ old('observaciones') }}</textarea>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="form-text text-muted">Describa el objetivo y detalles importantes de
                                            la evaluación.</small>
                                        <small class="text-muted">
                                            <span id="charCount">0</span>/255 caracteres
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary me-md-2">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Crear Diagnóstico
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Contador de caracteres para observaciones
            const observacionesTextarea = document.getElementById('observaciones');
            const charCountSpan = document.getElementById('charCount');

            function updateCharCount() {
                const currentLength = observacionesTextarea.value.length;
                charCountSpan.textContent = currentLength;

                // Cambiar color según la proximidad al límite
                if (currentLength > 230) {
                    charCountSpan.className = 'text-danger fw-bold';
                } else if (currentLength > 200) {
                    charCountSpan.className = 'text-warning fw-bold';
                } else {
                    charCountSpan.className = 'text-muted';
                }
            }

            // Actualizar contador al escribir
            observacionesTextarea.addEventListener('input', updateCharCount);

            // Inicializar contador
            updateCharCount();
        });
    </script>
@endpush
