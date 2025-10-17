@extends('layouts.dashboard')

@section('title', 'Configuración de Integraciones')

@push('styles')
    <style>
        .integrations-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 123, 255, 0.3);
        }

        .integration-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .integration-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }

        .tab-content {
            padding: 2rem;
            background: white;
            border-radius: 0 15px 15px 15px;
        }

        .config-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: white;
            font-size: 14px;
        }

        .api-icon {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .webhook-icon {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .sso-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .external-icon {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 123, 255, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(220, 53, 69, 0.4);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .table thead th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.4);
        }

        .status-inactive {
            background: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 107, 107, 0.1));
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="integrations-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fas fa-plug me-3"></i>Configuración de Integraciones
                    </h1>
                    <p class="mb-0 opacity-90">Gestiona las integraciones externas, APIs y conexiones del sistema</p>
                </div>
                <a href="{{ route('configuracion.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
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

        <!-- Navegación por pestañas -->
        <div class="integration-card">
            <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button"
                        role="tab">
                        <i class="fas fa-code me-2"></i>API y Webhooks
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sso-tab" data-bs-toggle="tab" data-bs-target="#sso" type="button"
                        role="tab">
                        <i class="fas fa-shield-alt me-2"></i>SSO y Autenticación
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="external-tab" data-bs-toggle="tab" data-bs-target="#external"
                        type="button" role="tab">
                        <i class="fas fa-external-link-alt me-2"></i>Servicios Externos
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="integrationTabContent">
                <!-- Tab API y Webhooks -->
                <div class="tab-pane fade show active" id="api" role="tabpanel">
                    <div class="row">
                        <!-- Configuración de API -->
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon api-icon">
                                        <i class="fas fa-code"></i>
                                    </span>
                                    Configuración de API
                                </h5>

                                <form id="api-form" action="{{ route('configuracion.integraciones.update') }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="api">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="api_habilitada"
                                            id="api_habilitada"
                                            {{ $configuracion['apis_habilitadas'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="api_habilitada">
                                            Habilitar API REST
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="api_version" class="form-label">Versión de API</label>
                                        <select class="form-select" name="api_version" id="api_version">
                                            <option value="v1"
                                                {{ ($configuracion['api_version'] ?? 'v1') == 'v1' ? 'selected' : '' }}>v1
                                            </option>
                                            <option value="v2"
                                                {{ ($configuracion['api_version'] ?? 'v1') == 'v2' ? 'selected' : '' }}>v2
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="rate_limit" class="form-label">Límite de Requests (por minuto)</label>
                                        <input type="number" class="form-control" name="rate_limit" id="rate_limit"
                                            value="{{ $configuracion['rate_limit'] ?? 60 }}" min="10" max="1000">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración
                                    </button>
                                </form>
                            </div>

                            <!-- Claves API -->
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon api-icon">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    Gestión de Claves API
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Claves activas:
                                        <strong>{{ count($configuracion['claves_api'] ?? []) }}</strong></span>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#newApiKeyModal">
                                        <i class="fas fa-plus me-2"></i>Nueva Clave
                                    </button>
                                </div>

                                @if (!empty($configuracion['claves_api']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Estado</th>
                                                    <th>Último Uso</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['claves_api'] as $clave)
                                                    <tr>
                                                        <td class="fw-bold">{{ $clave['nombre'] ?? 'Sin nombre' }}</td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $clave['activa'] ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $clave['activa'] ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $clave['activa'] ? 'Activa' : 'Inactiva' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $clave['ultimo_uso'] ?? 'Nunca' }}</td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="revocarClave('{{ $clave['id'] ?? '' }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-key"></i>
                                        <p>No hay claves API configuradas</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Webhooks -->
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon webhook-icon">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    Configuración de Webhooks
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Webhooks configurados:
                                        <strong>{{ count($configuracion['webhooks'] ?? []) }}</strong></span>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#newWebhookModal">
                                        <i class="fas fa-plus me-2"></i>Nuevo Webhook
                                    </button>
                                </div>

                                @if (!empty($configuracion['webhooks']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Evento</th>
                                                    <th>URL</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['webhooks'] as $webhook)
                                                    <tr>
                                                        <td class="fw-bold">{{ $webhook['evento'] ?? 'Sin evento' }}</td>
                                                        <td>
                                                            <small
                                                                class="text-muted">{{ Str::limit($webhook['url'] ?? '', 30) }}</small>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $webhook['activo'] ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $webhook['activo'] ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $webhook['activo'] ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="eliminarWebhook('{{ $webhook['id'] ?? '' }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-link"></i>
                                        <p>No hay webhooks configurados</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab SSO y Autenticación -->
                <div class="tab-pane fade" id="sso" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon sso-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    Configuración SSO
                                </h5>

                                <form action="{{ route('configuracion.integraciones.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="sso">

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="sso_habilitado"
                                            id="sso_habilitado"
                                            {{ $configuracion['sso_habilitado'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="sso_habilitado">
                                            Habilitar Single Sign-On (SSO)
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sso_protocolo" class="form-label">Protocolo SSO</label>
                                        <select class="form-select" name="sso_protocolo" id="sso_protocolo">
                                            <option value="saml2"
                                                {{ ($configuracion['sso_protocolo'] ?? 'saml2') == 'saml2' ? 'selected' : '' }}>
                                                SAML 2.0</option>
                                            <option value="oauth2"
                                                {{ ($configuracion['sso_protocolo'] ?? 'saml2') == 'oauth2' ? 'selected' : '' }}>
                                                OAuth 2.0</option>
                                            <option value="openid"
                                                {{ ($configuracion['sso_protocolo'] ?? 'saml2') == 'openid' ? 'selected' : '' }}>
                                                OpenID Connect</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="dominio_automatico" class="form-label">Dominio para
                                            auto-registro</label>
                                        <input type="text" class="form-control" name="dominio_automatico"
                                            id="dominio_automatico"
                                            value="{{ $configuracion['dominio_automatico'] ?? '' }}"
                                            placeholder="ejemplo.com">
                                        <small class="text-muted">Usuarios con este dominio se registrarán
                                            automáticamente</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración SSO
                                    </button>
                                </form>
                            </div>

                            <!-- Proveedores SSO -->
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon sso-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    Proveedores SSO
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Proveedores configurados:
                                        <strong>{{ count($configuracion['sso_proveedores'] ?? []) }}</strong></span>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#newProviderModal">
                                        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
                                    </button>
                                </div>

                                @if (!empty($configuracion['sso_proveedores']))
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Proveedor</th>
                                                    <th>Tipo</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($configuracion['sso_proveedores'] as $proveedor)
                                                    <tr>
                                                        <td class="fw-bold">{{ $proveedor['nombre'] ?? 'Sin nombre' }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-info">{{ strtoupper($proveedor['tipo'] ?? '') }}</span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="status-indicator {{ $proveedor['activo'] ? 'status-active' : 'status-inactive' }}"></span>
                                                            <span
                                                                class="badge {{ $proveedor['activo'] ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $proveedor['activo'] ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="eliminarProveedor('{{ $proveedor['id'] ?? '' }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>No hay proveedores SSO configurados</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Servicios Externos -->
                <div class="tab-pane fade" id="external" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon external-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    Servicios de Email
                                </h5>

                                <form action="{{ route('configuracion.integraciones.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="email">

                                    <div class="mb-3">
                                        <label for="email_proveedor" class="form-label">Proveedor de Email</label>
                                        <select class="form-select" name="email_proveedor" id="email_proveedor">
                                            <option value="smtp"
                                                {{ ($configuracion['email_proveedor'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>
                                                SMTP</option>
                                            <option value="sendgrid"
                                                {{ ($configuracion['email_proveedor'] ?? 'smtp') == 'sendgrid' ? 'selected' : '' }}>
                                                SendGrid</option>
                                            <option value="mailgun"
                                                {{ ($configuracion['email_proveedor'] ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>
                                                Mailgun</option>
                                            <option value="ses"
                                                {{ ($configuracion['email_proveedor'] ?? 'smtp') == 'ses' ? 'selected' : '' }}>
                                                Amazon SES</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email_api_key" class="form-label">API Key</label>
                                        <input type="password" class="form-control" name="email_api_key"
                                            id="email_api_key" value="{{ $configuracion['email_api_key'] ?? '' }}"
                                            placeholder="Ingresa tu API key">
                                    </div>

                                    <div class="mb-3">
                                        <label for="email_from" class="form-label">Email remitente</label>
                                        <input type="email" class="form-control" name="email_from" id="email_from"
                                            value="{{ $configuracion['email_from'] ?? '' }}"
                                            placeholder="noreply@tuempresa.com">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Config. Email
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="config-section">
                                <h5 class="section-title">
                                    <span class="section-icon external-icon">
                                        <i class="fas fa-cloud"></i>
                                    </span>
                                    Almacenamiento en la Nube
                                </h5>

                                <form action="{{ route('configuracion.integraciones.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seccion" value="storage">

                                    <div class="mb-3">
                                        <label for="storage_proveedor" class="form-label">Proveedor de
                                            Almacenamiento</label>
                                        <select class="form-select" name="storage_proveedor" id="storage_proveedor">
                                            <option value="local"
                                                {{ ($configuracion['storage_proveedor'] ?? 'local') == 'local' ? 'selected' : '' }}>
                                                Local</option>
                                            <option value="s3"
                                                {{ ($configuracion['storage_proveedor'] ?? 'local') == 's3' ? 'selected' : '' }}>
                                                Amazon S3</option>
                                            <option value="azure"
                                                {{ ($configuracion['storage_proveedor'] ?? 'local') == 'azure' ? 'selected' : '' }}>
                                                Azure Blob</option>
                                            <option value="gcs"
                                                {{ ($configuracion['storage_proveedor'] ?? 'local') == 'gcs' ? 'selected' : '' }}>
                                                Google Cloud Storage</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="storage_bucket" class="form-label">Bucket/Container</label>
                                        <input type="text" class="form-control" name="storage_bucket"
                                            id="storage_bucket" value="{{ $configuracion['storage_bucket'] ?? '' }}"
                                            placeholder="nombre-del-bucket">
                                    </div>

                                    <div class="mb-3">
                                        <label for="storage_region" class="form-label">Región</label>
                                        <input type="text" class="form-control" name="storage_region"
                                            id="storage_region" value="{{ $configuracion['storage_region'] ?? '' }}"
                                            placeholder="us-east-1">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Config. Storage
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal Nueva Clave API -->
    <div class="modal fade" id="newApiKeyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Clave API</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('configuracion.integraciones.claves-api.generar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_clave" class="form-label">Nombre de la clave</label>
                            <input type="text" class="form-control" name="nombre" id="nombre_clave" required>
                        </div>
                        <div class="mb-3">
                            <label for="permisos" class="form-label">Permisos</label>
                            <select class="form-select" name="permisos" id="permisos" required>
                                <option value="read">Solo lectura</option>
                                <option value="write">Lectura y escritura</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Generar Clave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Webhook -->
    <div class="modal fade" id="newWebhookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Webhook</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('configuracion.integraciones.webhooks.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="evento_webhook" class="form-label">Evento</label>
                            <select class="form-select" name="evento" id="evento_webhook" required>
                                <option value="user.created">Usuario creado</option>
                                <option value="user.updated">Usuario actualizado</option>
                                <option value="evaluation.completed">Evaluación completada</option>
                                <option value="report.generated">Reporte generado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="url_webhook" class="form-label">URL del Webhook</label>
                            <input type="url" class="form-control" name="url" id="url_webhook" required>
                        </div>
                        <div class="mb-3">
                            <label for="secreto_webhook" class="form-label">Secreto (opcional)</label>
                            <input type="text" class="form-control" name="secreto" id="secreto_webhook">
                            <small class="text-muted">Para verificar la autenticidad de las llamadas</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Webhook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Proveedor SSO -->
    <div class="modal fade" id="newProviderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Proveedor SSO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('configuracion.integraciones.sso.proveedor.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_proveedor" class="form-label">Nombre del proveedor</label>
                            <input type="text" class="form-control" name="nombre" id="nombre_proveedor" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_proveedor" class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" id="tipo_proveedor" required>
                                <option value="saml2">SAML 2.0</option>
                                <option value="oauth2">OAuth 2.0</option>
                                <option value="openid">OpenID Connect</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="endpoint_proveedor" class="form-label">Endpoint de autenticación</label>
                            <input type="url" class="form-control" name="endpoint" id="endpoint_proveedor" required>
                        </div>
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="client_id" id="client_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="client_secret" class="form-label">Client Secret</label>
                            <input type="password" class="form-control" name="client_secret" id="client_secret"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function revocarClave(claveId) {
            if (confirm('¿Estás seguro de que deseas revocar esta clave API?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.integraciones.claves-api.revocar') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const claveInput = document.createElement('input');
                claveInput.type = 'hidden';
                claveInput.name = 'clave_id';
                claveInput.value = claveId;

                form.appendChild(csrfToken);
                form.appendChild(claveInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function eliminarWebhook(webhookId) {
            if (confirm('¿Estás seguro de que deseas eliminar este webhook?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.integraciones.webhooks.eliminar') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const webhookInput = document.createElement('input');
                webhookInput.type = 'hidden';
                webhookInput.name = 'webhook_id';
                webhookInput.value = webhookId;

                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                form.appendChild(webhookInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function eliminarProveedor(proveedorId) {
            if (confirm('¿Estás seguro de que deseas eliminar este proveedor SSO?')) {
                // Crear form dinámico para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('configuracion.integraciones.sso.proveedor.eliminar') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const proveedorInput = document.createElement('input');
                proveedorInput.type = 'hidden';
                proveedorInput.name = 'proveedor_id';
                proveedorInput.value = proveedorId;

                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                form.appendChild(proveedorInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
