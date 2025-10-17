<a href="{{ $href ?? '#' }}" class="quick-action {{ $variant ?? 'default' }}" @if(!empty($title)) title="{{ $title }}" @endif>
  <i class="{{ $icon ?? 'fas fa-circle' }}"></i>
  <span>{{ $label ?? $text ?? 'Acción' }}</span>
</a>
