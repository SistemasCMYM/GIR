@if (!isset($isModal) || !$isModal)
    @section('title', 'Editar Empleado')

    @section('content')
    @endif

    @if (!isset($isModal) || !$isModal)
        <div class="container-fluid py-4 gir-override">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Editar Empleado
                            </h5>
                        </div>
                        <div class="card-body">
    @endif

    <form id="editarEmpleadoForm{{ isset($isModal) && $isModal ? '_modal' : '' }}" method="POST"
        action="{{ route('empresa.empleados.update', $empleado['_id']) }}">
        @csrf
        @method('PUT')

        @include('admin.gestion-administrativa.empresa.empleados._form', [
            'empleado' => $empleado,
            'areas' => $areas,
            'centros' => $centros,
            'procesos' => $procesos,
            'fieldIdPrefix' => isset($isModal) && $isModal ? 'modal_edit_' : 'page_edit_',
        ])

        @if (!isset($isModal) || !$isModal)
            <div class="mt-4">
                <a href="{{ route('empresa.empleados.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver
                </a>
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fas fa-save me-2"></i>
                    Actualizar Empleado
                </button>
            </div>
        @endif
    </form>

    @if (!isset($isModal) || !$isModal)
        </div>
        </div>
        </div>
        </div>
        </div>
    @endsection
@endif
