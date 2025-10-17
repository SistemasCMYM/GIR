<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error en Instrumentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error en el Instrumento Psicosocial
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h6>Mensaje:</h6>
                            <p class="mb-0">{{ $mensaje ?? 'Error desconocido' }}</p>
                        </div>
                        
                        @if(isset($detalle))
                        <div class="alert alert-warning">
                            <h6>Detalle:</h6>
                            <p class="mb-0">{{ $detalle }}</p>
                        </div>
                        @endif
                        
                        <div class="mt-4">
                            <h6>Posibles soluciones:</h6>
                            <ul>
                                <li>Verificar que las preguntas estén cargadas en la base de datos</li>
                                <li>Comprobar la conexión a MongoDB</li>
                                <li>Revisar los logs de Laravel para más detalles</li>
                                <li>Contactar al administrador del sistema</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('psicosocial.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Módulo Psicosocial
                            </a>
                            <a href="{{ route('test.instrumentos') }}" class="btn btn-info">
                                <i class="fas fa-vial me-2"></i>Página de Prueba
                            </a>
                            <button onclick="window.location.reload()" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Recargar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>
