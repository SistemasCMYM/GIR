@extends('layouts.dashboard')

@section('title', 'Configuración de Notificaciones')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Configuración de Notificaciones</h1>
            <p class="text-gray-600 mt-2">Gestiona los tipos de notificaciones y preferencias del sistema</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Configuración de Tipos de Notificaciones -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Tipos de Notificaciones</h2>
                    <p class="text-gray-600 text-sm mt-1">Configurar qué tipos de notificaciones están habilitadas</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('configuracion.notificaciones.tipos') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="notif_evaluaciones" name="tipos[]" value="evaluaciones"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('evaluaciones', $tiposHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="notif_evaluaciones" class="ml-3 text-sm text-gray-700">
                                    Notificaciones de Evaluaciones
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="notif_vencimientos" name="tipos[]" value="vencimientos"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('vencimientos', $tiposHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="notif_vencimientos" class="ml-3 text-sm text-gray-700">
                                    Notificaciones de Vencimientos
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="notif_sistema" name="tipos[]" value="sistema"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('sistema', $tiposHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="notif_sistema" class="ml-3 text-sm text-gray-700">
                                    Notificaciones del Sistema
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="notif_recordatorios" name="tipos[]" value="recordatorios"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('recordatorios', $tiposHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="notif_recordatorios" class="ml-3 text-sm text-gray-700">
                                    Recordatorios
                                </label>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Actualizar Tipos
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configuración de Frecuencias -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Frecuencias de Notificación</h2>
                    <p class="text-gray-600 text-sm mt-1">Configurar cada cuánto tiempo se envían las notificaciones</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('configuracion.notificaciones.frecuencias') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="freq_evaluaciones" class="block text-sm font-medium text-gray-700">
                                    Evaluaciones (días)
                                </label>
                                <select name="freq_evaluaciones" id="freq_evaluaciones"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1" {{ ($frecuencias['evaluaciones'] ?? 7) == 1 ? 'selected' : '' }}>
                                        Diario</option>
                                    <option value="3" {{ ($frecuencias['evaluaciones'] ?? 7) == 3 ? 'selected' : '' }}>
                                        Cada 3 días</option>
                                    <option value="7"
                                        {{ ($frecuencias['evaluaciones'] ?? 7) == 7 ? 'selected' : '' }}>Semanal</option>
                                    <option value="15"
                                        {{ ($frecuencias['evaluaciones'] ?? 7) == 15 ? 'selected' : '' }}>Quincenal
                                    </option>
                                    <option value="30"
                                        {{ ($frecuencias['evaluaciones'] ?? 7) == 30 ? 'selected' : '' }}>Mensual</option>
                                </select>
                            </div>

                            <div>
                                <label for="freq_vencimientos" class="block text-sm font-medium text-gray-700">
                                    Vencimientos (días)
                                </label>
                                <select name="freq_vencimientos" id="freq_vencimientos"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1"
                                        {{ ($frecuencias['vencimientos'] ?? 3) == 1 ? 'selected' : '' }}>Diario</option>
                                    <option value="3"
                                        {{ ($frecuencias['vencimientos'] ?? 3) == 3 ? 'selected' : '' }}>Cada 3 días
                                    </option>
                                    <option value="7"
                                        {{ ($frecuencias['vencimientos'] ?? 3) == 7 ? 'selected' : '' }}>Semanal</option>
                                </select>
                            </div>

                            <div>
                                <label for="freq_recordatorios" class="block text-sm font-medium text-gray-700">
                                    Recordatorios (días)
                                </label>
                                <select name="freq_recordatorios" id="freq_recordatorios"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1"
                                        {{ ($frecuencias['recordatorios'] ?? 1) == 1 ? 'selected' : '' }}>Diario</option>
                                    <option value="3"
                                        {{ ($frecuencias['recordatorios'] ?? 1) == 3 ? 'selected' : '' }}>Cada 3 días
                                    </option>
                                    <option value="7"
                                        {{ ($frecuencias['recordatorios'] ?? 1) == 7 ? 'selected' : '' }}>Semanal</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                Actualizar Frecuencias
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configuración de Canales -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Canales de Notificación</h2>
                    <p class="text-gray-600 text-sm mt-1">Configurar por qué medios se envían las notificaciones</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('configuracion.notificaciones.canales') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="canal_email" name="canales[]" value="email"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('email', $canalesHabilitados ?? ['email']) ? 'checked' : '' }}>
                                <label for="canal_email" class="ml-3 text-sm text-gray-700">
                                    Email
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="canal_push" name="canales[]" value="push"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('push', $canalesHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="canal_push" class="ml-3 text-sm text-gray-700">
                                    Notificaciones Push
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="canal_sms" name="canales[]" value="sms"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('sms', $canalesHabilitados ?? []) ? 'checked' : '' }}>
                                <label for="canal_sms" class="ml-3 text-sm text-gray-700">
                                    SMS
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="canal_dashboard" name="canales[]" value="dashboard"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('dashboard', $canalesHabilitados ?? ['dashboard']) ? 'checked' : '' }}>
                                <label for="canal_dashboard" class="ml-3 text-sm text-gray-700">
                                    Dashboard (Sistema)
                                </label>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                                Actualizar Canales
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notificaciones Recientes -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Notificaciones Recientes</h2>
                    <p class="text-gray-600 text-sm mt-1">Últimas notificaciones enviadas</p>
                </div>
                <div class="p-6">
                    @if ($notificacionesRecientes && count($notificacionesRecientes) > 0)
                        <div class="space-y-3">
                            @foreach ($notificacionesRecientes as $notificacion)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @switch($notificacion['tipo'] ?? 'sistema')
                                            @case('evaluaciones')
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                            @break

                                            @case('vencimientos')
                                                <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                                            @break

                                            @case('recordatorios')
                                                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                            @break

                                            @default
                                                <div class="w-2 h-2 bg-gray-500 rounded-full mt-2"></div>
                                        @endswitch
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $notificacion['titulo'] ?? 'Sin título' }}
                                        </p>
                                        <p class="text-sm text-gray-600 truncate">
                                            {{ $notificacion['mensaje'] ?? 'Sin mensaje' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ isset($notificacion['fecha_creacion'])
                                                ? \Carbon\Carbon::parse($notificacion['fecha_creacion'])->diffForHumans()
                                                : 'Fecha no disponible' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-lg mb-2">📬</div>
                            <p class="text-gray-500">No hay notificaciones recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-submit para mejorar UX (opcional)
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        // Opcional: auto-guardar cambios
                        console.log('Configuración de notificación cambiada:', this.name, this.checked);
                    });
                });
            });
        </script>
    @endpush
@endsection
