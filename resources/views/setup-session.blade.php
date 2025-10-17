<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Sesión - GIR365</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Configurar Sesión de Prueba - GIR365</h4>
                    </div>
                    <div class="card-body">
                        <p>Para acceder al módulo psicosocial, necesita configurar una sesión de prueba.</p>
                        <button id="configButton" class="btn btn-primary">Configurar Sesión</button>
                        <div id="message" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('configButton').addEventListener('click', function() {
            // Configurar sesión usando API
            fetch('/api/setup-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('message').innerHTML = 
                        '<div class="alert alert-success">Sesión configurada exitosamente. <a href="/psicosocial">Ir al Módulo Psicosocial</a></div>';
                } else {
                    document.getElementById('message').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + (data.message || 'Error desconocido') + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('message').innerHTML = 
                    '<div class="alert alert-danger">Error de conexión: ' + error + '</div>';
            });
        });
    </script>
</body>
</html>
