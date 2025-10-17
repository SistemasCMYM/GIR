<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evaluación Psicosocial - {{ $hoja->primerNombre ?? 'Empleado' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .evaluation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="evaluation-card p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                        <h2 class="h3 mb-2">Evaluación Psicosocial</h2>
                        <p class="text-muted">
                            Bienvenido/a {{ $hoja->primerNombre ?? 'Empleado' }} {{ $hoja->primerApellido ?? '' }}
                        </p>
                    </div>

                    <!-- Progreso general -->
                    @php
                        $completados = 0;
                        if($hoja->datos === 'completado') $completados++;
                        if($hoja->intralaboral === 'completado') $completados++;
                        if($hoja->extralaboral === 'completado') $completados++;
                        if($hoja->estres === 'completado') $completados++;
                        $porcentaje = round(($completados / 4) * 100);
                    @endphp

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Progreso general</span>
                            <span class="badge badge-{{ $porcentaje == 100 ? 'success' : 'info' }}">{{ $porcentaje }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-gradient-primary" 
                                 style="width: {{ $porcentaje }}%;" 
                                 aria-valuenow="{{ $porcentaje }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Tests disponibles -->
                    <div class="row g-4">
                        <!-- Datos Personales -->
                        <div class="col-md-6">
                            <div class="card h-100 {{ $hoja->datos === 'completado' ? 'border-success' : '' }}">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-circle fa-2x mb-3 {{ $hoja->datos === 'completado' ? 'text-success' : 'text-primary' }}"></i>
                                    <h5 class="card-title">Datos Personales</h5>
                                    <p class="card-text text-muted">Cuestionario de información personal y sociodemográfica</p>
                                    @if($hoja->datos === 'completado')
                                        <span class="badge badge-success mb-3">
                                            <i class="fas fa-check me-1"></i>Completado
                                        </span>
                                    @else
                                        <button class="btn btn-primary" onclick="iniciarCuestionario('datos')">
                                            <i class="fas fa-play me-1"></i>
                                            {{ $hoja->datos === 'en_progreso' ? 'Continuar' : 'Iniciar' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Intralaboral -->
                        <div class="col-md-6">
                            <div class="card h-100 {{ $hoja->intralaboral === 'completado' ? 'border-success' : '' }}">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-2x mb-3 {{ $hoja->intralaboral === 'completado' ? 'text-success' : 'text-primary' }}"></i>
                                    <h5 class="card-title">Cuestionario Intralaboral</h5>
                                    <p class="card-text text-muted">Factores de riesgo psicosocial en el trabajo</p>
                                    @if($hoja->intralaboral === 'completado')
                                        <span class="badge badge-success mb-3">
                                            <i class="fas fa-check me-1"></i>Completado
                                        </span>
                                    @else
                                        <button class="btn btn-primary" onclick="iniciarCuestionario('intralaboral')">
                                            <i class="fas fa-play me-1"></i>
                                            {{ $hoja->intralaboral === 'en_progreso' ? 'Continuar' : 'Iniciar' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Extralaboral -->
                        <div class="col-md-6">
                            <div class="card h-100 {{ $hoja->extralaboral === 'completado' ? 'border-success' : '' }}">
                                <div class="card-body text-center">
                                    <i class="fas fa-home fa-2x mb-3 {{ $hoja->extralaboral === 'completado' ? 'text-success' : 'text-primary' }}"></i>
                                    <h5 class="card-title">Cuestionario Extralaboral</h5>
                                    <p class="card-text text-muted">Factores de riesgo fuera del ámbito laboral</p>
                                    @if($hoja->extralaboral === 'completado')
                                        <span class="badge badge-success mb-3">
                                            <i class="fas fa-check me-1"></i>Completado
                                        </span>
                                    @else
                                        <button class="btn btn-primary" onclick="iniciarCuestionario('extralaboral')">
                                            <i class="fas fa-play me-1"></i>
                                            {{ $hoja->extralaboral === 'en_progreso' ? 'Continuar' : 'Iniciar' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Estrés -->
                        <div class="col-md-6">
                            <div class="card h-100 {{ $hoja->estres === 'completado' ? 'border-success' : '' }}">
                                <div class="card-body text-center">
                                    <i class="fas fa-heartbeat fa-2x mb-3 {{ $hoja->estres === 'completado' ? 'text-success' : 'text-primary' }}"></i>
                                    <h5 class="card-title">Cuestionario de Estrés</h5>
                                    <p class="card-text text-muted">Síntomas relacionados con el estrés</p>
                                    @if($hoja->estres === 'completado')
                                        <span class="badge badge-success mb-3">
                                            <i class="fas fa-check me-1"></i>Completado
                                        </span>
                                    @else
                                        <button class="btn btn-primary" onclick="iniciarCuestionario('estres')">
                                            <i class="fas fa-play me-1"></i>
                                            {{ $hoja->estres === 'en_progreso' ? 'Continuar' : 'Iniciar' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($porcentaje == 100)
                    <div class="alert alert-success mt-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>¡Felicitaciones!</strong> Has completado todos los cuestionarios. Pronto recibirás los resultados.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function iniciarCuestionario(tipo) {
            // Aquí implementarías la lógica para iniciar cada cuestionario
            // Por ahora solo mostramos un mensaje
            alert(`Iniciando cuestionario: ${tipo}`);
            
            // Ejemplo de redirección a la URL del cuestionario específico
            // window.location.href = `/evaluacion/{{ $hoja->id }}/${tipo}`;
        }
    </script>
</body>
</html>
