<form id="editarEmpleadoForm" method="POST" action="{{ route('empresa.empleados.update', $empleado['_id']) }}">
    @csrf
    @method('PUT')

    <input type="hidden" name="empleado_id" value="{{ $empleado['_id'] }}">

    @include('admin.gestion-administrativa.empresa.empleados._form', [
        'empleado' => $empleado,
        'areas' => $areas,
        'centros' => $centros,
        'procesos' => $procesos,
        'fieldIdPrefix' => 'modal_edit_',
    ])
</form>
