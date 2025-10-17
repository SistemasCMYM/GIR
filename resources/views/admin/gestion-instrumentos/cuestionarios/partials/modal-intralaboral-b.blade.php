{{-- Modal: Intralaboral Forma B (partial, lectura) --}}
<div class="modal fade" id="modalIntralaboralB" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-shield me-2"></i> Intralaboral - Forma B</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        @php
          $res = $evaluacion->respuestas->where('cuestionario', 'intralaboral_b');
        @endphp
        @if($res->isEmpty())
          <div class="alert alert-warning">No hay respuestas registradas para Intralaboral - Forma B.</div>
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
