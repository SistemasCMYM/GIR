{{-- Modal: Extralaboral (partial, lectura) --}}
<div class="modal fade" id="modalExtralaboral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-people-arrows me-2"></i> Extralaboral</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        @php
          $res = $evaluacion->respuestas->where('cuestionario', 'extralaboral');
        @endphp

        @if($res->isEmpty())
          <div class="alert alert-warning">No hay respuestas registradas para Extralaboral.</div>
        @else
          @foreach($res as $r)
            <div class="mb-3">
              <label class="form-label">{{ $r->pregunta->texto ?? 'Ítem' }}:</label>
              <p class="fw-bold">{{ $r->valor }}</p>
            </div>
          @endforeach
        @endif
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
