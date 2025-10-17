@extends('layouts.dashboard')

@section('title', 'Nueva Tarjeta de Aplicación - Módulo Psicosocial')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Nueva Tarjeta de Aplicación</h1>
                        <p class="text-muted">Crear nuevo diagnóstico psicosocial</p>
                    </div>
                    <div>
                        <a href="{{ route('psicosocial.application-cards') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('psicosocial.store-application-card') }}" method="POST" id="applicationCardForm">
            @csrf

            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de la Tarjeta <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                                name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profesional_id" class="form-label">Profesional Asignado <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('profesional_id') is-invalid @enderror" id="profesional_id"
                                name="profesional_id" required>
                                <option value="">Seleccione un profesional</option>
                                <!-- This would be populated from the controller -->
                                <option value="prof1">Dr. Ana García - Psicología Ocupacional</option>
                                <option value="prof2">Lic. Carlos López - Seguridad y Salud</option>
                                <option value="prof3">Dra. María Rodríguez - Psicología Clínica</option>
                            </select>
                            @error('profesional_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                rows="3" required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Información de Ubicación</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="sede" class="form-label">Sede <span class="text-danger">*</span></label>
                            <select class="form-select @error('sede') is-invalid @enderror" id="sede" name="sede"
                                required>
                                <option value="">Seleccione una sede</option>
                                <option value="principal">Sede Principal</option>
                                <option value="norte">Sede Norte</option>
                                <option value="sur">Sede Sur</option>
                                <option value="centro">Sede Centro</option>
                            </select>
                            @error('sede')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                            <select class="form-select @error('area') is-invalid @enderror" id="area" name="area"
                                required>
                                <option value="">Seleccione un área</option>
                                <option value="administrativa">Administrativa</option>
                                <option value="operativa">Operativa</option>
                                <option value="comercial">Comercial</option>
                                <option value="financiera">Financiera</option>
                                <option value="recursos_humanos">Recursos Humanos</option>
                                <option value="tecnologia">Tecnología</option>
                            </select>
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                            <select class="form-select @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad"
                                required>
                                <option value="">Seleccione una ciudad</option>
                                <option value="bogota">Bogotá</option>
                                <option value="medellin">Medellín</option>
                                <option value="cali">Cali</option>
                                <option value="barranquilla">Barranquilla</option>
                                <option value="cartagena">Cartagena</option>
                                <option value="bucaramanga">Bucaramanga</option>
                            </select>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Selection -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Selección de Empleados</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllEmployees()">
                        <i class="fas fa-check-double me-1"></i>Seleccionar Todos
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filters for employees -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter_area" class="form-label">Filtrar por Área</label>
                            <select class="form-select" id="filter_area" onchange="filterEmployees()">
                                <option value="">Todas las áreas</option>
                                <option value="administrativa">Administrativa</option>
                                <option value="operativa">Operativa</option>
                                <option value="comercial">Comercial</option>
                                <option value="financiera">Financiera</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_cargo" class="form-label">Filtrar por Cargo</label>
                            <select class="form-select" id="filter_cargo" onchange="filterEmployees()">
                                <option value="">Todos los cargos</option>
                                <option value="analista">Analista</option>
                                <option value="coordinador">Coordinador</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="operario">Operario</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_contrato" class="form-label">Tipo de Contrato</label>
                            <select class="form-select" id="filter_contrato" onchange="filterEmployees()">
                                <option value="">Todos los contratos</option>
                                @foreach ($availableContracts as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_employee" class="form-label">Buscar Empleado</label>
                            <input type="text" class="form-control" id="search_employee"
                                placeholder="Nombre o identificación" onkeyup="filterEmployees()">
                        </div>
                    </div>

                    <!-- Employee List -->
                    <div class="row" id="employeeList">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" class="form-check-input" id="selectAll"
                                                    onchange="toggleAllEmployees()">
                                            </th>
                                            <th>Nombre</th>
                                            <th>Identificación</th>
                                            <th>Cargo</th>
                                            <th>Área</th>
                                            <th>Contrato</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeeTableBody">
                                        <!-- Sample employees - this would be populated from the database -->
                                        <tr class="employee-row" data-area="administrativa" data-cargo="analista"
                                            data-contrato="indefinido">
                                            <td>
                                                <input type="checkbox" class="form-check-input employee-checkbox"
                                                    name="empleados[]" value="emp1">
                                            </td>
                                            <td>Ana María García López</td>
                                            <td>12345678</td>
                                            <td>Analista</td>
                                            <td>Administrativa</td>
                                            <td>Indefinido</td>
                                        </tr>
                                        <tr class="employee-row" data-area="operativa" data-cargo="operario"
                                            data-contrato="temporal">
                                            <td>
                                                <input type="checkbox" class="form-check-input employee-checkbox"
                                                    name="empleados[]" value="emp2">
                                            </td>
                                            <td>Carlos Eduardo Martínez</td>
                                            <td>87654321</td>
                                            <td>Operario</td>
                                            <td>Operativa</td>
                                            <td>Temporal</td>
                                        </tr>
                                        <tr class="employee-row" data-area="comercial" data-cargo="coordinador"
                                            data-contrato="indefinido">
                                            <td>
                                                <input type="checkbox" class="form-check-input employee-checkbox"
                                                    name="empleados[]" value="emp3">
                                            </td>
                                            <td>María Fernanda Rodríguez</td>
                                            <td>11223344</td>
                                            <td>Coordinador</td>
                                            <td>Comercial</td>
                                            <td>Indefinido</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <span class="badge bg-primary">
                            <span id="selectedCount">0</span> empleado(s) seleccionado(s)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Configuración</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fecha_limite" class="form-label">Fecha Límite</label>
                            <input type="date" class="form-control" id="fecha_limite" name="fecha_limite">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="enviar_notificaciones"
                                    name="enviar_notificaciones" checked>
                                <label class="form-check-label" for="enviar_notificaciones">
                                    Enviar notificaciones por email
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('psicosocial.application-cards') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="previewCard()">
                            <i class="fas fa-eye me-2"></i>Vista Previa
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Tarjeta
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedEmployees = [];

        function filterEmployees() {
            const area = document.getElementById('filter_area').value;
            const cargo = document.getElementById('filter_cargo').value;
            const contrato = document.getElementById('filter_contrato').value;
            const search = document.getElementById('search_employee').value.toLowerCase();

            const rows = document.querySelectorAll('.employee-row');

            rows.forEach(row => {
                const rowArea = row.dataset.area;
                const rowCargo = row.dataset.cargo;
                const rowContrato = row.dataset.contrato;
                const rowText = row.textContent.toLowerCase();

                const matchArea = !area || rowArea === area;
                const matchCargo = !cargo || rowCargo === cargo;
                const matchContrato = !contrato || rowContrato === contrato;
                const matchSearch = !search || rowText.includes(search);

                if (matchArea && matchCargo && matchContrato && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function toggleAllEmployees() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.employee-checkbox');

            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('.employee-row');
                if (row.style.display !== 'none') {
                    checkbox.checked = selectAll.checked;
                }
            });

            updateSelectedCount();
        }

        function selectAllEmployees() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('.employee-row');
                if (row.style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
            document.getElementById('selectedCount').textContent = checkedBoxes.length;
        }

        function previewCard() {
            const formData = new FormData(document.getElementById('applicationCardForm'));

            // Show preview modal or new window with card details
            alert('Vista previa de la tarjeta (funcionalidad pendiente)');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to employee checkboxes
            document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Set default end date (30 days from start)
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaLimite = document.getElementById('fecha_limite');

            fechaInicio.addEventListener('change', function() {
                if (this.value) {
                    const startDate = new Date(this.value);
                    startDate.setDate(startDate.getDate() + 30);
                    fechaLimite.value = startDate.toISOString().split('T')[0];
                }
            });

            // Initial count
            updateSelectedCount();
        });

        // Form validation
        document.getElementById('applicationCardForm').addEventListener('submit', function(e) {
            const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked');

            if (selectedEmployees.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un empleado para la evaluación.');
                return false;
            }

            return true;
        });
    </script>
@endpush
