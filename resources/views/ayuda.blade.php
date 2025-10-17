@extends('layouts.dashboard')

@section('title', 'Ayuda - Laravel365')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Centro de Ayuda</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Ayuda</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Búsqueda de Ayuda -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-search mr-2"></i>
                                ¿En qué podemos ayudarte?
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" placeholder="Buscar en la ayuda..."
                                    id="searchHelp">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Preguntas Frecuentes -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-question-circle mr-2"></i>
                                Preguntas Frecuentes
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="accordion">
                                <!-- FAQ 1 -->
                                <div class="card">
                                    <div class="card-header" id="heading1">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse1"
                                                aria-expanded="true" aria-controls="collapse1">
                                                ¿Cómo cambio mi contraseña?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse1" class="collapse show" aria-labelledby="heading1"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            Para cambiar tu contraseña, ve a <strong>Configuración > Seguridad</strong> y
                                            haz clic en "Cambiar Contraseña". Deberás ingresar tu contraseña actual y la
                                            nueva contraseña dos veces para confirmar.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="card">
                                    <div class="card-header" id="heading2">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                ¿Cómo accedo a los módulos?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse2" class="collapse" aria-labelledby="heading2"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            Los módulos están disponibles en el Inicio principal. Puedes acceder a ellos
                                            haciendo clic en las tarjetas correspondientes o a través del menú lateral
                                            izquierdo.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="card">
                                    <div class="card-header" id="heading3">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                                ¿Cómo exporto mis datos?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse3" class="collapse" aria-labelledby="heading3"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            Puedes exportar tus datos desde <strong>Configuración > Backup y
                                                Exportación</strong>. Allí encontrarás opciones para exportar en diferentes
                                            formatos (Excel, PDF, CSV).
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="card">
                                    <div class="card-header" id="heading4">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                ¿Qué hacer si no puedo iniciar sesión?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse4" class="collapse" aria-labelledby="heading4"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            Verifica que estés usando el NIT correcto de tu empresa y las credenciales
                                            correctas. Si el problema persiste, contacta al administrador del sistema o al
                                            soporte técnico.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="card">
                                    <div class="card-header" id="heading5">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                                ¿Cómo configuro las notificaciones?
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse5" class="collapse" aria-labelledby="heading5"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            Las notificaciones se pueden configurar en <strong>Configuración >
                                                Sistema</strong>. Puedes activar o desactivar diferentes tipos de
                                            notificaciones según tus preferencias.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enlaces Rápidos -->
                <div class="col-lg-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-link mr-2"></i>
                                Enlaces Rápidos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Video Tutorial: Primeros Pasos
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-book mr-2"></i>
                                    Manual de Usuario
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-keyboard mr-2"></i>
                                    Atajos de Teclado
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="fas fa-bug mr-2"></i>
                                    Reportar un Problema
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-headset mr-2"></i>
                                Soporte Técnico
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Email:</strong> soporte@laravel365.com</p>
                            <p><strong>Teléfono:</strong> +57 (1) 123-4567</p>
                            <p><strong>Horario:</strong> Lun-Vie 8:00-18:00</p>
                            <hr>
                            <button class="btn btn-success btn-block">
                                <i class="fas fa-comments mr-2"></i>
                                Chat en Vivo
                            </button>
                        </div>
                    </div>

                    <!-- Versión del Sistema -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información del Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Versión:</strong> Laravel365 v2.0.1</p>
                            <p><strong>Última actualización:</strong> Enero 2024</p>
                            <p><strong>Estado:</strong> <span class="badge badge-success">Activo</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Funcionalidad de búsqueda en la ayuda
            $('#searchHelp').on('keyup', function() {
                let searchValue = $(this).val().toLowerCase();

                if (searchValue.length > 2) {
                    $('#accordion .card').each(function() {
                        let cardText = $(this).text().toLowerCase();
                        if (cardText.includes(searchValue)) {
                            $(this).show();
                            $(this).find('.collapse').addClass('show');
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $('#accordion .card').show();
                    $('#accordion .collapse').removeClass('show');
                    $('#collapse1').addClass('show');
                }
            });

            // Buscar al presionar Enter
            $('#searchHelp').on('keypress', function(e) {
                if (e.which === 13) {
                    let searchValue = $(this).val();
                    if (searchValue.trim() !== '') {
                        toastr.info('Buscando: ' + searchValue);
                    }
                }
            });

            // Chat en vivo (simulado)
            $('.btn-success[data-contains="Chat en Vivo"]').on('click', function() {
                toastr.success('Iniciando chat en vivo...', 'Soporte Técnico');
            });
        });
    </script>
@endsection
