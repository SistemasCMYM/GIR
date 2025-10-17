@extends('layouts.dashboard')

@section('title', 'Configuración - Estructura Organizacional')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-sitemap text-success"></i> Estructura Organizacional
                        </h1>
                        <p class="text-muted mb-0">Gestión de áreas, departamentos y cargos</p>
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

        <!-- Tabs de Estructura -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="estructuraTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="areas-tab" data-bs-toggle="tab" data-bs-target="#areas"
                                    type="button">
                                    <i class="fas fa-building"></i> Áreas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="departamentos-tab" data-bs-toggle="tab"
                                    data-bs-target="#departamentos" type="button">
                                    <i class="fas fa-sitemap"></i> Departamentos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cargos-tab" data-bs-toggle="tab" data-bs-target="#cargos"
                                    type="button">
                                    <i class="fas fa-user-tie"></i> Cargos
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="estructuraTabsContent">
                            <!-- Áreas -->
                            <div class="tab-pane fade show active" id="areas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Gestión de Áreas</h6>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalArea">
                                        <i class="fas fa-plus"></i> Nueva Área
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($estructura['areas']) && count($estructura['areas']) > 0)
                                                @foreach ($estructura['areas'] as $area)
                                                    <tr>
                                                        <td>{{ $area['nombre'] ?? 'Sin nombre' }}</td>
                                                        <td>{{ $area['descripcion'] ?? 'Sin descripción' }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ isset($area['activo']) && $area['activo'] ? 'success' : 'secondary' }}">
                                                                {{ isset($area['activo']) && $area['activo'] ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        <i class="fas fa-building fa-2x mb-2"></i><br>
                                                        No hay áreas registradas
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Departamentos -->
                            <div class="tab-pane fade" id="departamentos" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Gestión de Departamentos</h6>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalDepartamento">
                                        <i class="fas fa-plus"></i> Nuevo Departamento
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Área</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($estructura['departamentos']) && count($estructura['departamentos']) > 0)
                                                @foreach ($estructura['departamentos'] as $departamento)
                                                    <tr>
                                                        <td>{{ $departamento['nombre'] ?? 'Sin nombre' }}</td>
                                                        <td>{{ $departamento['area_nombre'] ?? 'Sin área asignada' }}</td>
                                                        <td>{{ $departamento['descripcion'] ?? 'Sin descripción' }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ isset($departamento['activo']) && $departamento['activo'] ? 'success' : 'secondary' }}">
                                                                {{ isset($departamento['activo']) && $departamento['activo'] ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fas fa-sitemap fa-2x mb-2"></i><br>
                                                        No hay departamentos registrados
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Cargos -->
                            <div class="tab-pane fade" id="cargos" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Gestión de Cargos</h6>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalCargo">
                                        <i class="fas fa-plus"></i> Nuevo Cargo
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Departamento</th>
                                                <th>Nivel</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($estructura['cargos']) && count($estructura['cargos']) > 0)
                                                @foreach ($estructura['cargos'] as $cargo)
                                                    <tr>
                                                        <td>{{ $cargo['nombre'] ?? 'Sin nombre' }}</td>
                                                        <td>{{ $cargo['departamento_nombre'] ?? 'Sin departamento' }}</td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                {{ ucfirst($cargo['nivel'] ?? 'No definido') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ isset($cargo['activo']) && $cargo['activo'] ? 'success' : 'secondary' }}">
                                                                {{ isset($cargo['activo']) && $cargo['activo'] ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fas fa-user-tie fa-2x mb-2"></i><br>
                                                        No hay cargos registrados
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Área -->
    <div class="modal fade" id="modalArea" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('configuracion.estructura.area.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Área</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreArea" class="form-label">Nombre del Área</label>
                            <input type="text" class="form-control" name="nombre" id="nombreArea" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionArea" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcionArea" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" value="1"
                                    id="activoArea" checked>
                                <label class="form-check-label" for="activoArea">
                                    Área activa
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Área</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Departamento -->
    <div class="modal fade" id="modalDepartamento" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('configuracion.estructura.departamento.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Departamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreDepartamento" class="form-label">Nombre del Departamento</label>
                            <input type="text" class="form-control" name="nombre" id="nombreDepartamento" required>
                        </div>
                        <div class="mb-3">
                            <label for="areaDepartamento" class="form-label">Área</label>
                            <select class="form-control" name="area_id" id="areaDepartamento" required>
                                <option value="">Seleccione un área</option>
                                @if (isset($estructura['areas']) && count($estructura['areas']) > 0)
                                    @foreach ($estructura['areas'] as $area)
                                        <option value="{{ $area['id'] ?? '' }}">{{ $area['nombre'] ?? 'Sin nombre' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionDepartamento" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcionDepartamento" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" value="1"
                                    id="activoDepartamento" checked>
                                <label class="form-check-label" for="activoDepartamento">
                                    Departamento activo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Departamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Cargo -->
    <div class="modal fade" id="modalCargo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('configuracion.estructura.cargo.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Cargo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreCargo" class="form-label">Nombre del Cargo</label>
                            <input type="text" class="form-control" name="nombre" id="nombreCargo" required>
                        </div>
                        <div class="mb-3">
                            <label for="departamentoCargo" class="form-label">Departamento</label>
                            <select class="form-control" name="departamento_id" id="departamentoCargo" required>
                                <option value="">Seleccione un departamento</option>
                                @if (isset($estructura['departamentos']) && count($estructura['departamentos']) > 0)
                                    @foreach ($estructura['departamentos'] as $departamento)
                                        <option value="{{ $departamento['id'] ?? '' }}">
                                            {{ $departamento['nombre'] ?? 'Sin nombre' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nivelCargo" class="form-label">Nivel</label>
                            <select class="form-control" name="nivel" id="nivelCargo" required>
                                <option value="">Seleccione un nivel</option>
                                <option value="operativo">Operativo</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="coordinador">Coordinador</option>
                                <option value="jefe">Jefe</option>
                                <option value="gerente">Gerente</option>
                                <option value="director">Director</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activo" value="1"
                                    id="activoCargo" checked>
                                <label class="form-check-label" for="activoCargo">
                                    Cargo activo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Cargo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
