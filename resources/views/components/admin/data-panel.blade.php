<div class="data-panel">
  <div class="data-panel-header">
    <div class="title"><i class="{{ $icon ?? 'fas fa-box' }}"></i> {{ $title ?? 'Título' }}</div>
    @if(!empty($actions))
      <div class="actions">
        @if(is_array($actions))
          @foreach($actions as $action)
            <button type="button" class="btn btn-sm btn-outline-primary" 
                    @if(isset($action['id'])) id="{{ $action['id'] }}" @endif
                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
              @if(isset($action['icon']))
                <i class="fas {{ $action['icon'] }} me-1"></i>
              @endif
              {{ $action['label'] ?? 'Acción' }}
            </button>
          @endforeach
        @else
          {!! $actions !!}
        @endif
      </div>
    @endif
  </div>
  <div class="data-panel-body">
    {{ $slot }}
  </div>
</div>
