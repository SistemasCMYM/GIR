@extends('layouts.dashboard')
@section('title', 'Permisos')
@section('page-title', 'Permisos - Gestión Administrativa')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                <i class="fas fa-home me-1"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.gestion-administrativa.index') }}" class="text-decoration-none">
                                <i class="fas fa-users-cog me-1"></i> Administración de Usuarios
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-key me-1"></i> Permisos
                        </li>
                    </ol>
                </nav>

                <!-- Header -->
                <div class="card border-0 shadow-sm bg-gradient-primary text-white mb-4">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-2">
                            <i class="fas fa-key me-2"></i> Gestión de Permisos
                        </h1>
                        <p class="mb-0 opacity-75">Control de acceso y asignación de permisos por roles</p>
                    </div>
                </div>

                @php
                    $roles = $roles ?? collect();
                    $permissions = $permissions ?? collect();
                    $totalPerms = $permissions->count();
                    $totalRoles = $roles->count();
                    $asignadas = 0;
                    foreach ($roles as $r) {
                        $asignadas += $r->permissions->count() ?? 0;
                    }
                    $pendientes = max($totalPerms * $totalRoles - $asignadas, 0);
                    $grouped = $permissions->groupBy(function ($p) {
                        if (isset($p->module)) {
                            return $p->module;
                        }
                        $name = $p->name ?? '';
                        return strpos($name, '.') !== false ? explode('.', $name)[0] : 'general';
                    });
                    $moduleColors = [
                        'psicosocial' => 'info',
                        'configuracion' => 'danger',
                        'informes' => 'success',
                        'admin' => 'warning',
                        'general' => 'secondary',
                    ];
                @endphp

                <!-- Métricas con estilo de cuentas -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        {{ $totalPerms }}
                                    </div>
                                    <div class="gir-metric-label small text-muted">TOTAL PERMISOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #D1A554 0%, #B8943C 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-key text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        {{ $totalRoles }}
                                    </div>
                                    <div class="gir-metric-label small text-muted">ROLES ACTIVOS</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tag text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        {{ $asignadas }}
                                    </div>
                                    <div class="gir-metric-label small text-muted">ASIGNACIONES</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="gir-metric-card h-100">
                            <div class="d-flex align-items-center justify-content-between p-3">
                                <div class="flex-grow-1">
                                    <div class="gir-metric-number h4 mb-1 fw-bold text-dark">
                                        {{ $pendientes }}
                                    </div>
                                    <div class="gir-metric-label small text-muted">PENDIENTES</div>
                                </div>
                                <div class="gir-metric-icon"
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-exclamation-triangle text-white" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- Asignación Rápida -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt text-primary me-2"></i>
                            Asignación Rápida de Permisos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="quickRole" class="form-label">Seleccionar Rol</label>
                                <select class="form-select" id="quickRole">
                                    <option value="">Todos los roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="quickModule" class="form-label">Módulo</label>
                                <select class="form-select" id="quickModule">
                                    <option value="">Todos los módulos</option>
                                    @foreach ($grouped as $module => $perms)
                                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-success" id="btnGrantAll">
                                        <i class="fas fa-check me-1"></i> Otorgar
                                    </button>
                                    <button type="button" class="btn btn-danger" id="btnRevokeAll">
                                        <i class="fas fa-times me-1"></i> Revocar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matriz de Permisos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table text-primary me-2"></i>
                            Matriz de Permisos
                        </h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" id="btnSaveMatrix">
                                <i class="fas fa-save me-1"></i> Guardar
                            </button>
                            <button type="button" class="btn btn-info btn-sm" id="btnExportMatrix">
                                <i class="fas fa-download me-1"></i> Exportar
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="btnResetMatrix">
                                <i class="fas fa-undo me-1"></i> Restablecer
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($totalPerms && $totalRoles)
                            <form id="permissionMatrixForm" method="POST"
                                action="{{ route('usuarios.permisos.matrix') }}">
                                @csrf
                                <div class="table-responsive" style="max-height: 600px;">
                                    <table class="table table-sm mb-0" id="permissionMatrix">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="border-end" style="min-width: 250px;">Permiso / Módulo</th>
                                                @foreach ($roles as $role)
                                                    <th class="text-center" style="min-width: 100px;">{{ $role->name }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grouped as $module => $perms)
                                                <tr class="table-{{ $moduleColors[$module] ?? 'secondary' }}">
                                                    <td colspan="{{ 1 + $roles->count() }}" class="fw-bold">
                                                        <i class="fas fa-layer-group me-2"></i>
                                                        {{ strtoupper($module) }}
                                                    </td>
                                                </tr>
                                                @foreach ($perms as $perm)
                                                    <tr data-module="{{ $module }}" class="table-hover">
                                                        <td class="border-end">
                                                            <small>{{ $perm->label ?? $perm->name }}</small>
                                                        </td>
                                                        @foreach ($roles as $role)
                                                            @php
                                                                $checked = $role->permissions->contains(
                                                                    'id',
                                                                    $perm->id,
                                                                );
                                                            @endphp
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    class="form-check-input perm-toggle"
                                                                    name="matrix[{{ $perm->id }}][{{ $role->id }}]"
                                                                    value="1" @checked($checked)
                                                                    data-role="{{ $role->id }}"
                                                                    data-perm="{{ $perm->id }}">
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-key fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Sin datos disponibles</h5>
                                <p class="text-muted">Debe existir al menos un rol y un permiso para mostrar la matriz.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* FORCE METRICS CARDS IN SINGLE LINE - PERMISOS MODULE */
        .row.mb-4 {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 16px !important;
            align-items: stretch !important;
            margin-bottom: 24px !important;
        }

        .row.mb-4 .col {
            flex: 1 !important;
            min-width: 0 !important;
            max-width: none !important;
            padding: 0 !important;
        }

        .gir-metric-card {
            height: 100px !important;
            min-height: 100px !important;
            max-height: 100px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 16px !important;
            background: white !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .gir-metric-card:hover {
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px) !important;
        }

        .gir-metric-card .flex-grow-1 {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .gir-metric-card .gir-metric-number {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin-bottom: 4px !important;
            color: #1f2937 !important;
            line-height: 1.2 !important;
        }

        .gir-metric-card .gir-metric-label {
            font-size: 0.75rem !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-weight: 600 !important;
            margin: 0 !important;
            line-height: 1.1 !important;
        }

        .gir-metric-card .gir-metric-icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .gir-metric-card .gir-metric-icon i {
            font-size: 20px !important;
            color: white !important;
        }

        /* RESPONSIVE - MAINTAIN SINGLE LINE ON SMALLER SCREENS */
        @media (max-width: 768px) {
            .row.mb-4 {
                gap: 8px !important;
            }

            .gir-metric-card {
                height: 90px !important;
                min-height: 90px !important;
                max-height: 90px !important;
                padding: 12px !important;
            }

            .gir-metric-card .gir-metric-number {
                font-size: 1.5rem !important;
            }

            .gir-metric-card .gir-metric-label {
                font-size: 0.65rem !important;
            }

            .gir-metric-card .gir-metric-icon {
                width: 40px !important;
                height: 40px !important;
            }

            .gir-metric-card .gir-metric-icon i {
                font-size: 18px !important;
            }
        }

        /* EXTRA SMALL SCREENS - STILL MAINTAIN SINGLE LINE */
        @media (max-width: 480px) {
            .row.mb-4 {
                gap: 4px !important;
            }

            .gir-metric-card {
                height: 80px !important;
                min-height: 80px !important;
                max-height: 80px !important;
                padding: 8px !important;
            }

            .gir-metric-card .gir-metric-number {
                font-size: 1.25rem !important;
            }

            .gir-metric-card .gir-metric-label {
                font-size: 0.6rem !important;
            }

            .gir-metric-card .gir-metric-icon {
                width: 32px !important;
                height: 32px !important;
            }

            .gir-metric-card .gir-metric-icon i {
                font-size: 16px !important;
            }
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .perm-toggle {
            cursor: pointer;
            transform: scale(1.2);
        }

        .perm-toggle:hover {
            transform: scale(1.3);
        }

        .btn-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #permissionMatrix .table-hover:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('permissionMatrixForm');

            if (!form) return;

            const originalState = () => Array.from(document.querySelectorAll('input.perm-toggle')).map(i => i
                .checked);
            let baseline = originalState();

            function diffChanged() {
                const current = originalState();
                return current.some((v, i) => v !== baseline[i]);
            }

            function notifyChanged() {
                const saveBtn = document.getElementById('btnSaveMatrix');
                if (diffChanged()) {
                    saveBtn.classList.add('btn-glow');
                } else {
                    saveBtn.classList.remove('btn-glow');
                }
            }

            // Detectar cambios en checkboxes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('perm-toggle')) {
                    notifyChanged();
                }
            });

            // Guardar matriz
            document.getElementById('btnSaveMatrix')?.addEventListener('click', function() {
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': formData.get('_token')
                        },
                        body: formData
                    })
                    .then(response => response.json().catch(() => ({
                        success: false
                    })))
                    .then(data => {
                        baseline = originalState();
                        notifyChanged();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Guardado', 'Permisos actualizados correctamente', 'success');
                        } else {
                            alert('Permisos actualizados correctamente');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'No se pudieron guardar los cambios', 'error');
                        } else {
                            alert('Error: No se pudieron guardar los cambios');
                        }
                    });
            });

            // Restablecer matriz
            document.getElementById('btnResetMatrix')?.addEventListener('click', function() {
                if (!baseline.length) return;

                const doReset = () => {
                    const inputs = document.querySelectorAll('input.perm-toggle');
                    inputs.forEach((input, index) => {
                        input.checked = baseline[index];
                    });
                    notifyChanged();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Restablecer cambios?',
                        text: 'Se perderán todos los cambios no guardados',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restablecer',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doReset();
                        }
                    });
                } else {
                    if (confirm('¿Está seguro de restablecer todos los cambios?')) {
                        doReset();
                    }
                }
            });

            // Exportar matriz
            document.getElementById('btnExportMatrix')?.addEventListener('click', function() {
                const rows = [];
                const headers = ['Permiso', ...Array.from(document.querySelectorAll(
                    '#permissionMatrix thead th')).slice(1).map(th => th.textContent.trim())];
                rows.push(headers.join(','));

                document.querySelectorAll('#permissionMatrix tbody tr').forEach(tr => {
                    if (tr.querySelector('td[colspan]')) return; // Skip module headers

                    const permissionName = tr.querySelector('td').textContent.trim();
                    const checkboxes = tr.querySelectorAll('input.perm-toggle');
                    const row = [permissionName, ...Array.from(checkboxes).map(cb => cb.checked ?
                        '1' : '0')];
                    rows.push(row.join(','));
                });

                const csvContent = rows.join('\n');
                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'matriz_permisos.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            });

            // Asignación rápida
            function bulkApply(grant) {
                const roleId = document.getElementById('quickRole').value;
                const module = document.getElementById('quickModule').value;

                if (!roleId) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Debe seleccionar un rol', 'warning');
                    } else {
                        alert('Debe seleccionar un rol');
                    }
                    return;
                }

                const selector = module ?
                    `#permissionMatrix tbody tr[data-module="${module}"]` :
                    '#permissionMatrix tbody tr[data-module]';

                const rows = document.querySelectorAll(selector);

                rows.forEach(row => {
                    const input = row.querySelector(`input.perm-toggle[data-role="${roleId}"]`);
                    if (input) {
                        input.checked = grant;
                    }
                });

                notifyChanged();
            }

            document.getElementById('btnGrantAll')?.addEventListener('click', () => bulkApply(true));
            document.getElementById('btnRevokeAll')?.addEventListener('click', () => bulkApply(false));
        });
    </script>
@endpush
