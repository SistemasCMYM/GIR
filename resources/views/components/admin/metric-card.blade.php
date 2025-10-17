<div class="metric-card {{ $variant ?? 'primary' }}">
  <div class="metric-icon">
    <i class="{{ $icon ?? 'fas fa-circle' }}"></i>
  </div>
  <div class="metric-content">
    <div class="metric-value" data-target="{{ $value ?? 0 }}">{{ $value ?? 0 }}</div>
    <div class="metric-label">{{ $label ?? 'Label' }}</div>
  </div>
</div>
