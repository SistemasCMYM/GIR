<div class="empty-state">
  <div class="icon"><i class="{{ $icon ?? 'fas fa-box-open' }}"></i></div>
  <h5>{{ $title ?? 'Sin datos' }}</h5>
  <p class="text-muted mb-2">{{ $message ?? 'No hay información disponible.' }}</p>
  {{ $slot }}
</div>
