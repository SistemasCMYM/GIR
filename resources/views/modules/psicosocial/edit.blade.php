@extends('layouts.dashboard')

@section('title', 'Editar Evaluación Psicosocial')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-brain text-info"></i>
                        Editar Evaluación #{{ $evaluacion->id ?? 'N/A' }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('empleados.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('psicosocial.index') }}">Psicosocial</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('psicosocial.index') }}">Evaluaciones</a></li>
                        <li class="breadcrumb-item active">Editar #{{ $evaluacion->id ?? 'N/A' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i>
                                Editar Evaluación Psicosocial
                            </h3>
                        </div>

                        <form action="{{ route('psicosocial.update', $evaluacion->id ?? 1) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="empleado_id">Empleado <span class="text-danger">*</span></label>
                                            <select class="form-control" id="empleado_id" name="empleado_id" required>
                                                <option value="">Seleccione un empleado</option>
                                                <!-- Los empleados se cargarían dinámicamente -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_evaluacion">Tipo de Evaluación <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="tipo_evaluacion" name="tipo_evaluacion"
                                                required>
                                                <option value="">Seleccione un tipo</option>
                                                <option value="inicial"
                                                    {{ isset($evaluacion) && $evaluacion->tipo_evaluacion == 'inicial' ? 'selected' : '' }}>
                                                    Evaluación Inicial
                                                </option>
                                                <option value="periodica"
                                                    {{ isset($evaluacion) && $evaluacion->tipo_evaluacion == 'periodica' ? 'selected' : '' }}>
                                                    Evaluación Periódica
                                                </option>
                                                <option value="post_incidente"
                                                    {{ isset($evaluacion) && $evaluacion->tipo_evaluacion == 'post_incidente' ? 'selected' : '' }}>
                                                    Post Incidente
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado">Estado</label>
                                            <select class="form-control" id="estado" name="estado">
                                                <option value="pendiente"
                                                    {{ isset($evaluacion) && $evaluacion->estado == 'pendiente' ? 'selected' : '' }}>
                                                    Pendiente
                                                </option>
                                                <option value="en_progreso"
                                                    {{ isset($evaluacion) && $evaluacion->estado == 'en_progreso' ? 'selected' : '' }}>
                                                    En Progreso
                                                </option>
                                                <option value="completada"
                                                    {{ isset($evaluacion) && $evaluacion->estado == 'completada' ? 'selected' : '' }}>
                                                    Completada
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nivel_riesgo">Nivel de Riesgo</label>
                                            <select class="form-control" id="nivel_riesgo" name="nivel_riesgo">
                                                <option value="bajo"
                                                    {{ isset($evaluacion) && $evaluacion->nivel_riesgo == 'bajo' ? 'selected' : '' }}>
                                                    Bajo
                                                </option>
                                                <option value="medio"
                                                    {{ isset($evaluacion) && $evaluacion->nivel_riesgo == 'medio' ? 'selected' : '' }}>
                                                    Medio
                                                </option>
                                                <option value="alto"
                                                    {{ isset($evaluacion) && $evaluacion->nivel_riesgo == 'alto' ? 'selected' : '' }}>
                                                    Alto
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                        placeholder="Observaciones adicionales sobre la evaluación...">{{ $evaluacion->observaciones ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                                <a href="{{ route('psicosocial.show', $evaluacion->id ?? 1) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i>
                                    Ver Evaluación
                                </a>
                                <a href="{{ route('psicosocial.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
