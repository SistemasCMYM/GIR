@extends('layouts.dashboard')

@section('title', 'Configuración de Empresa')

@push('styles')
    <style>
        .empresa-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.3);
        }

        .config-tab {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .config-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .tab-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tab-content {
            padding: 2rem;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            margin-right: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .color-picker-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
        }

        .empresa-logo {
            max-width: 150px;
            max-height: 80px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .info-card {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('configuracion.index') }}">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-building"></i> Empresa
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="empresa-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-building me-3"></i>
                        Configuración de Empresa
                    </h2>
                    <p class="mb-0 opacity-75">
                        Configure la información básica, identidad corporativa y configuraciones específicas de su empresa
                    </p>
                    @if (isset($empresa) && $empresa->id)
                        <small class="opacity-75 d-block mt-2">
                            <strong>{{ $empresa->razon_social }}</strong> - NIT: {{ $empresa->nit }}
                        </small>
                    @endif
                </div>
                <div class="col-md-4 text-md-end">
                    @if (isset($empresa->logo) && $empresa->logo)
                        <img src="{{ asset('storage/logos/' . $empresa->logo) }}" alt="Logo de {{ $empresa->nombre }}"
                            class="empresa-logo">
                    @else
                        <div class="d-flex flex-column gap-2">
                            <small class="opacity-75">
                                <i class="fas fa-user me-2"></i>
                                Usuario: {{ $usuario['nombre'] ?? 'N/A' }}
                            </small>
                            <small class="opacity-75">
                                <i class="fas fa-clock me-2"></i>
                                {{ now()->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    @endif
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

        <!-- Configuración por Tabs -->
        @if (isset($tabs) && count($tabs) > 0)
            <div class="row">
                <div class="col-12">
                    @foreach ($tabs as $tab)
                        <div class="config-tab" data-tab="{{ $tab['id'] }}">
                            <div class="tab-header" onclick="toggleTab('{{ $tab['id'] }}')">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <i class="{{ $tab['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $tab['name'] }}</h5>
                                        <p class="text-muted mb-0 small">{{ $tab['description'] }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down tab-chevron"></i>
                            </div>
                            <div class="tab-content" id="content-{{ $tab['id'] }}">
                                @include('modules.configuracion.empresa.tabs.' . $tab['id'])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Configuración Básica sin Tabs -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>
                                Información de Empresa
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('configuracion.empresa.update') }}" method="POST"
                                enctype="multipart/form-data" id="empresaForm">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Errores de validación:</h6>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre" class="form-label">Nombre de la Empresa</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="{{ $empresa->nombre ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="razon_social" class="form-label">Razón Social</label>
                                            <input type="text" class="form-control" id="razon_social" name="razon_social"
                                                value="{{ $empresa->razon_social ?? '' }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nit" class="form-label">NIT</label>
                                            <input type="text" class="form-control" id="nit" name="nit"
                                                value="{{ $empresa->nit ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $empresa->email ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono"
                                                value="{{ $empresa->telefono ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sitio_web" class="form-label">Sitio Web</label>
                                            <input type="url" class="form-control" id="sitio_web" name="sitio_web"
                                                value="{{ $empresa->sitio_web ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="2">{{ $empresa->direccion ?? '' }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="colorPrimario" class="form-label">Color Primario</label>
                                            <div class="color-picker-container">
                                                <input type="color" class="form-control" id="colorPrimario"
                                                    name="colorPrimario"
                                                    value="{{ $empresa->colorPrimario ?? '#007bff' }}">
                                                <div class="color-preview"
                                                    style="background-color: {{ $empresa->colorPrimario ?? '#007bff' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="colorSecundario" class="form-label">Color Secundario</label>
                                            <div class="color-picker-container">
                                                <input type="color" class="form-control" id="colorSecundario"
                                                    name="colorSecundario"
                                                    value="{{ $empresa->colorSecundario ?? '#6c757d' }}">
                                                <div class="color-preview"
                                                    style="background-color: {{ $empresa->colorSecundario ?? '#6c757d' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="logo" class="form-label">Logo de la Empresa</label>
                                            <input type="file" class="form-control" id="logo" name="logo"
                                                accept="image/*">
                                            <small class="text-muted">Formatos permitidos: JPG, PNG, SVG. Tamaño máximo:
                                                2MB</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="favicon" class="form-label">Favicon</label>
                                            <input type="file" class="form-control" id="favicon" name="favicon"
                                                accept="image/*">
                                            <small class="text-muted">Formatos permitidos: ICO, PNG. Tamaño máximo:
                                                512KB</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración
                                    </button>
                                </div>
                            </form>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const form = document.getElementById('empresaForm');
                                    const submitBtn = document.getElementById('submitBtn');

                                    form.addEventListener('submit', function(e) {
                                        // Validar campos requeridos
                                        const nombre = document.getElementById('nombre').value.trim();
                                        const razonSocial = document.getElementById('razon_social').value.trim();
                                        const nit = document.getElementById('nit').value.trim();

                                        if (!nombre || !razonSocial || !nit) {
                                            e.preventDefault();
                                            alert(
                                                'Por favor complete todos los campos requeridos:\n- Nombre de la Empresa\n- Razón Social\n- NIT'
                                            );
                                            return false;
                                        }

                                        // Mostrar loading
                                        submitBtn.disabled = true;
                                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Estado de Configuración -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-card">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Estado de Configuración
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Información Básica:</span>
                        <span
                            class="badge-status {{ $empresa->nombre && $empresa->nit ? 'badge-success' : 'badge-warning' }}">
                            {{ $empresa->nombre && $empresa->nit ? 'Completa' : 'Incompleta' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Logo Corporativo:</span>
                        <span class="badge-status {{ $empresa->logo ? 'badge-success' : 'badge-warning' }}">
                            {{ $empresa->logo ? 'Configurado' : 'Sin configurar' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Colores Corporativos:</span>
                        <span class="badge-status badge-success">Configurados</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <h6 class="mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        Estadísticas
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Configuraciones de Hallazgos:</span>
                        <span class="text-info">{{ $configuraciones_hallazgos->count() ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Configuraciones de Psicosocial:</span>
                        <span class="text-info">{{ $configuraciones_psicosocial->count() ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Última Actualización:</span>
                        <span class="text-muted small">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function toggleTab(tabId) {
            const content = document.getElementById('content-' + tabId);
            const chevron = document.querySelector(`[data-tab="${tabId}"] .tab-chevron`);

            if (content.classList.contains('active')) {
                content.classList.remove('active');
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            } else {
                // Cerrar otros tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-chevron').forEach(icon => {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                });

                // Abrir tab seleccionado
                content.classList.add('active');
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            }
        }

        // Color picker preview update
        document.addEventListener('DOMContentLoaded', function() {
            const colorInputs = document.querySelectorAll('input[type="color"]');
            colorInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const preview = this.parentElement.querySelector('.color-preview');
                    if (preview) {
                        preview.style.backgroundColor = this.value;
                    }
                });
            });
        });
    </script>
@endpush
