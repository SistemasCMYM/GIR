{{-- Modal: Datos Generales (partial) --}}
<div class="modal fade" id="modalDatosGenerales" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header custom">
        <h5 class="modal-title"><i class="fas fa-id-card me-2"></i> Datos Personales</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre:</label>
          <p class="fw-bold">{{ $empleado->nombres ?? ($empleado->nombre ?? 'N/A') }} {{ $empleado->apellidos ?? '' }}</p>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Documento:</label>
            <p class="fw-bold">{{ $empleado->documento ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Cargo:</label>
            <p class="fw-bold">{{ $empleado->cargo ?? 'N/A' }}</p>
          </div>
        </div>

        <hr>

        @php
          $respuestasDatos = $evaluacion->respuestas->where('cuestionario', 'datos_generales');
        @endphp

        @if($respuestasDatos->isEmpty())
          <div class="alert alert-warning">No se encontraron respuestas registradas para Datos Personales.</div>
        @else
          @foreach($respuestasDatos as $r)
            <div class="mb-3">
              <label class="form-label">{{ $r->pregunta->texto ?? ('Ítem ' . ($r->pregunta_id ?? '')) }}:</label>
              <p class="fw-bold">{{ $r->valor }}</p>
            </div>
          @endforeach
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
