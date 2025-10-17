@extends('layouts.dashboard')

@section('title', 'Crear Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auth.index') }}">Autenticación</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auth.profiles.index') }}">Perfiles</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Información del Perfil</h3>
                    </div>
                    <form id="createPerfilForm" action="{{ route('auth.profiles.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Perfil *</label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                            id="nombre" name="nombre" value="{{ old('nombre') }}" required
                                            placeholder="Ej: Administrador, Supervisor, etc.">
                                        @error('nombre')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa_id">Empresa</label>
                                        <select class="form-control select2 @error('empresa_id') is-invalid @enderror"
                                            id="empresa_id" name="empresa_id">
                                            <option value="">Perfil General (Todas las empresas)</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->_id }}"
                                                    {{ old('empresa_id') == $empresa->_id ? 'selected' : '' }}>
                                                    {{ $empresa->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('empresa_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Deje vacío para crear un perfil general aplicable a todas las empresas
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                    rows="3" placeholder="Descripción detallada del perfil y sus responsabilidades...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="estado">Estado *</label>
                                <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                                    name="estado" required>
                                    <option value="activo" {{ old('estado', 'activo') == 'activo' ? 'selected' : '' }}>
                                        Activo</option>
                                    <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo
                                    </option>
                                </select>
                                @error('estado')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Sobre los Perfiles</h5>
                            <p>Los perfiles definen los roles y permisos que tendrán los usuarios en el sistema.</p>
                            <ul>
                                <li><strong>Perfiles Generales:</strong> Aplicables a todas las empresas</li>
                                <li><strong>Perfiles Específicos:</strong> Solo para una empresa particular</li>
                                <li><strong>Permisos:</strong> Se configuran en el siguiente paso</li>
                            </ul>
                        </div>

                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Perfiles Activos</span>
                                <span class="info-box-number">{{ $stats['total_activos'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Empresas Registradas</span>
                                <span class="info-box-number">{{ $stats['total_empresas'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Configuración de Permisos</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleAllPermisos(true)">
                                <i class="fas fa-check-square"></i> Seleccionar Todos
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleAllPermisos(false)">
                                <i class="fas fa-square"></i> Deseleccionar Todos
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $permisosDisponibles = [
                                    'Autenticación' => [
                                        'auth.users.view' => 'Ver usuarios',
                                        'auth.users.create' => 'Crear usuarios',
                                        'auth.users.edit' => 'Editar usuarios',
                                        'auth.users.delete' => 'Eliminar usuarios',
                                        'auth.profiles.view' => 'Ver perfiles',
                                        'auth.profiles.create' => 'Crear perfiles',
                                        'auth.profiles.edit' => 'Editar perfiles',
                                        'auth.profiles.delete' => 'Eliminar perfiles',
                                    ],
                                    'Empresas' => [
                                        'empresas.view' => 'Ver empresas',
                                        'empresas.create' => 'Crear empresas',
                                        'empresas.edit' => 'Editar empresas',
                                        'empresas.delete' => 'Eliminar empresas',
                                        'empresas.empleados.view' => 'Ver empleados',
                                        'empresas.empleados.create' => 'Crear empleados',
                                        'empresas.empleados.edit' => 'Editar empleados',
                                        'empresas.empleados.delete' => 'Eliminar empleados',
                                    ],
                                    'Reportes' => [
                                        'reportes.view' => 'Ver reportes',
                                        'reportes.create' => 'Crear reportes',
                                        'reportes.edit' => 'Editar reportes',
                                        'reportes.delete' => 'Eliminar reportes',
                                        'reportes.export' => 'Exportar reportes',
                                        'reportes.stats' => 'Ver estadísticas',
                                    ],
                                    'Hallazgos' => [
                                        'hallazgos.view' => 'Ver hallazgos',
                                        'hallazgos.create' => 'Crear hallazgos',
                                        'hallazgos.edit' => 'Editar hallazgos',
                                        'hallazgos.delete' => 'Eliminar hallazgos',
                                        'hallazgos.approve' => 'Aprobar hallazgos',
                                    ],
                                    'Planes' => [
                                        'planes.view' => 'Ver planes',
                                        'planes.create' => 'Crear planes',
                                        'planes.edit' => 'Editar planes',
                                        'planes.delete' => 'Eliminar planes',
                                        'planes.execute' => 'Ejecutar planes',
                                    ],
                                    'Psicosocial' => [
                                        'psicosocial.view' => 'Ver módulo psicosocial',
                                        'psicosocial.create' => 'Crear evaluaciones',
                                        'psicosocial.edit' => 'Editar evaluaciones',
                                        'psicosocial.delete' => 'Eliminar evaluaciones',
                                        'psicosocial.reports' => 'Reportes psicosociales',
                                    ],
                                    'Configuración' => [
                                        'config.view' => 'Ver configuración',
                                        'config.edit' => 'Editar configuración',
                                        'config.system' => 'Configuración del sistema',
                                    ],
                                ];
                            @endphp

                            @foreach ($permisosDisponibles as $categoria => $permisos)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <input type="checkbox" class="category-checkbox mr-2"
                                                    data-category="{{ strtolower($categoria) }}">
                                                {{ $categoria }}
                                            </h5>
                                        </div>
                                        <div class="card-body p-2">
                                            @foreach ($permisos as $permiso => $descripcion)
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input permiso-checkbox {{ strtolower($categoria) }}-permiso"
                                                        type="checkbox" name="permisos[]" value="{{ $permiso }}"
                                                        id="permiso_{{ $permiso }}"
                                                        {{ in_array($permiso, old('permisos', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permiso_{{ $permiso }}">
                                                        {{ $descripcion }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" onclick="submitForm()">
                            <i class="fas fa-save"></i> Crear Perfil
                        </button>
                        <a href="{{ route('auth.profiles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccionar empresa...'
            });

            // Category checkbox handler
            $('.category-checkbox').change(function() {
                const category = $(this).data('category');
                const isChecked = $(this).is(':checked');
                $(`.${category}-permiso`).prop('checked', isChecked);
            });

            // Individual permission checkbox handler
            $('.permiso-checkbox').change(function() {
                const category = $(this).attr('class').match(/(\w+)-permiso/)[1];
                const totalInCategory = $(`.${category}-permiso`).length;
                const checkedInCategory = $(`.${category}-permiso:checked`).length;

                const categoryCheckbox = $(`.category-checkbox[data-category="${category}"]`);

                if (checkedInCategory === 0) {
                    categoryCheckbox.prop('indeterminate', false).prop('checked', false);
                } else if (checkedInCategory === totalInCategory) {
                    categoryCheckbox.prop('indeterminate', false).prop('checked', true);
                } else {
                    categoryCheckbox.prop('indeterminate', true);
                }
            });

            // Initialize category checkboxes state
            $('.category-checkbox').each(function() {
                const category = $(this).data('category');
                const totalInCategory = $(`.${category}-permiso`).length;
                const checkedInCategory = $(`.${category}-permiso:checked`).length;

                if (checkedInCategory === 0) {
                    $(this).prop('indeterminate', false).prop('checked', false);
                } else if (checkedInCategory === totalInCategory) {
                    $(this).prop('indeterminate', false).prop('checked', true);
                } else {
                    $(this).prop('indeterminate', true);
                }
            });
        });

        function toggleAllPermisos(check) {
            $('.permiso-checkbox').prop('checked', check);
            $('.category-checkbox').prop('checked', check).prop('indeterminate', false);
        }

        function submitForm() {
            const form = $('#createPerfilForm');
            const submitBtn = $('button[onclick="submitForm()"]');
            const originalText = submitBtn.html();

            // Validate required fields
            if (!$('#nombre').val().trim()) {
                toastr.error('El nombre del perfil es requerido');
                $('#nombre').focus();
                return;
            }

            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creando...').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Perfil creado correctamente');
                        setTimeout(() => {
                            window.location.href = '{{ route('auth.profiles.index') }}';
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Error al crear el perfil');
                    }
                },
                error: function(xhr) {
                    let message = 'Error al crear el perfil';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join('<br>');
                    }
                    toastr.error(message);
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    </script>
@endpush
