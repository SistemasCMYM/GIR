<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Configuracion\ConfiguracionController;
use App\Http\Controllers\Modules\Configuracion\EmpresaController;
use App\Http\Controllers\Modules\Configuracion\EstructuraController;
use App\Http\Controllers\Modules\Configuracion\FechaHoraController;
use App\Http\Controllers\Modules\Configuracion\ReportesController;
use App\Http\Controllers\Modules\Configuracion\SeguridadController;
use App\Http\Controllers\Modules\Configuracion\NotificacionesController;
use App\Http\Controllers\Modules\Configuracion\IntegracionesController;
use App\Http\Controllers\Modules\Configuracion\AutenticacionController;
use App\Http\Controllers\Modules\Configuracion\ProcesosController;
use App\Http\Controllers\Modules\Configuracion\SystemConfigController;

// Rutas del módulo de configuración (solo SuperAdmin)
Route::prefix('configuracion')->name('configuracion.')->middleware('configuracion.access')->group(function () {
    
    // Página principal de configuración
    Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
    
    // Sistema de configuración modular (nueva funcionalidad)
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/', [SystemConfigController::class, 'index'])->name('index');
        Route::post('/update', [SystemConfigController::class, 'updateConfiguration'])->name('update');
        Route::get('/module/{module}', [SystemConfigController::class, 'getModuleConfig'])->name('module.config');
        Route::post('/module/{module}', [SystemConfigController::class, 'updateModuleConfig'])->name('module.update');
    });
    
    // API para configuraciones
    Route::post('/update-configuration', [ConfiguracionController::class, 'updateConfiguration'])->name('update.configuration');
    Route::get('/configurations/{componente}', [ConfiguracionController::class, 'getConfigurationsByComponent'])->name('configurations.component');
    
    // Gestión de Empresa
    Route::prefix('empresa')->name('empresa.')->group(function () {
        Route::get('/', [EmpresaController::class, 'index'])->name('index');
        Route::post('/update', [EmpresaController::class, 'update'])->name('update');
        Route::get('/avanzada', [EmpresaController::class, 'configuracionAvanzada'])->name('avanzada');
        Route::post('/avanzada', [EmpresaController::class, 'updateAvanzada'])->name('avanzada.update');
    });
    
    // Gestión de Estructura Organizacional
    Route::prefix('estructura')->name('estructura.')->group(function () {
        Route::get('/', [EstructuraController::class, 'index'])->name('index');
        Route::post('/configuration', [EstructuraController::class, 'updateConfiguration'])->name('configuration.update');
        Route::post('/area', [EstructuraController::class, 'guardarArea'])->name('area.guardar');
        Route::delete('/area', [EstructuraController::class, 'eliminarArea'])->name('area.eliminar');
        Route::post('/departamento', [EstructuraController::class, 'guardarDepartamento'])->name('departamento.guardar');
        Route::delete('/departamento', [EstructuraController::class, 'eliminarDepartamento'])->name('departamento.eliminar');
        Route::post('/cargo', [EstructuraController::class, 'guardarCargo'])->name('cargo.guardar');
        Route::delete('/cargo', [EstructuraController::class, 'eliminarCargo'])->name('cargo.eliminar');
    });
    
    // Gestión de Fecha y Hora
    Route::prefix('fechahora')->name('fechahora.')->group(function () {
        Route::get('/', [FechaHoraController::class, 'index'])->name('index');
        Route::put('/update', [FechaHoraController::class, 'update'])->name('update');
        Route::post('/previsualizar', [FechaHoraController::class, 'previsualizar'])->name('previsualizar');
    });
    
    // Gestión de Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReportesController::class, 'index'])->name('index');
        Route::post('/update', [ReportesController::class, 'update'])->name('update');
        Route::post('/plantilla', [ReportesController::class, 'guardarPlantilla'])->name('plantilla.guardar');
        Route::delete('/plantilla', [ReportesController::class, 'eliminarPlantilla'])->name('plantilla.eliminar');
        Route::get('/plantilla/previsualizar', [ReportesController::class, 'previsualizarPlantilla'])->name('plantilla.previsualizar');
    });
    
    // Gestión de Seguridad
    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('/', [SeguridadController::class, 'index'])->name('index');
        Route::put('/update', [SeguridadController::class, 'update'])->name('update');
        Route::put('/update/sesiones', [SeguridadController::class, 'updateSesiones'])->name('update.sesiones');
        Route::put('/update/adicional', [SeguridadController::class, 'updateAdicional'])->name('update.adicional');
        Route::get('/logs', [SeguridadController::class, 'logs'])->name('logs');
        Route::post('/forzar-cambio-contrasena', [SeguridadController::class, 'forzarCambioContrasena'])->name('forzar-cambio-contrasena');
        Route::post('/desbloquear-usuarios', [SeguridadController::class, 'desbloquearUsuarios'])->name('desbloquear-usuarios');
    });
    
    // Gestión de Notificaciones
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/', [NotificacionesController::class, 'index'])->name('index');
        Route::post('/update', [NotificacionesController::class, 'update'])->name('update');
        Route::post('/tipos', [NotificacionesController::class, 'updateTipos'])->name('tipos');
        Route::post('/frecuencias', [NotificacionesController::class, 'updateFrecuencias'])->name('frecuencias');
        Route::post('/canales', [NotificacionesController::class, 'updateCanales'])->name('canales');
        Route::post('/plantilla', [NotificacionesController::class, 'guardarPlantilla'])->name('plantilla.guardar');
        Route::delete('/plantilla', [NotificacionesController::class, 'eliminarPlantilla'])->name('plantilla.eliminar');
        Route::post('/enviar-prueba', [NotificacionesController::class, 'enviarPrueba'])->name('enviar-prueba');
    });
    
    // Gestión de Integraciones
    Route::prefix('integraciones')->name('integraciones.')->group(function () {
        Route::get('/', [IntegracionesController::class, 'index'])->name('index');
        Route::post('/update', [IntegracionesController::class, 'update'])->name('update');
        
        // API
        Route::get('/claves-api', [IntegracionesController::class, 'gestionarClavesApi'])->name('claves-api');
        Route::post('/claves-api/generar', [IntegracionesController::class, 'generarClaveApi'])->name('claves-api.generar');
        Route::post('/claves-api/revocar', [IntegracionesController::class, 'revocarClaveApi'])->name('claves-api.revocar');
        
        // Webhooks
        Route::get('/webhooks', [IntegracionesController::class, 'gestionarWebhooks'])->name('webhooks');
        Route::post('/webhooks/guardar', [IntegracionesController::class, 'guardarWebhook'])->name('webhooks.guardar');
        Route::delete('/webhooks', [IntegracionesController::class, 'eliminarWebhook'])->name('webhooks.eliminar');
        
        // SSO
        Route::get('/sso', [IntegracionesController::class, 'configurarSSO'])->name('sso');
        Route::post('/sso/proveedor', [IntegracionesController::class, 'guardarProveedorSSO'])->name('sso.proveedor.guardar');
        Route::delete('/sso/proveedor', [IntegracionesController::class, 'eliminarProveedorSSO'])->name('sso.proveedor.eliminar');
    });
    
    // Gestión de Autenticación
    Route::prefix('autenticacion')->name('autenticacion.')->group(function () {
        Route::get('/', [AutenticacionController::class, 'index'])->name('index');
        Route::post('/update', [AutenticacionController::class, 'update'])->name('update');
        Route::get('/usuarios', [AutenticacionController::class, 'usuarios'])->name('usuarios');
        Route::post('/usuarios/toggle-bloqueo', [AutenticacionController::class, 'toggleBloqueo'])->name('usuarios.toggle-bloqueo');
        Route::post('/usuarios/restablecer-contrasena', [AutenticacionController::class, 'restablecerContrasena'])->name('usuarios.restablecer-contrasena');
        Route::post('/usuarios/toggle-2fa', [AutenticacionController::class, 'toggle2FA'])->name('usuarios.toggle-2fa');
    });
    
    // Gestión de Procesos
    Route::prefix('procesos')->name('procesos.')->group(function () {
        Route::get('/', [ProcesosController::class, 'index'])->name('index');
        Route::post('/update', [ProcesosController::class, 'update'])->name('update');
        Route::get('/planes-accion', [ProcesosController::class, 'planesAccion'])->name('planes-accion');
        Route::post('/ejecutar', [ProcesosController::class, 'ejecutarProceso'])->name('ejecutar');
        Route::get('/historial', [ProcesosController::class, 'historialProcesos'])->name('historial');
        Route::post('/historial/limpiar', [ProcesosController::class, 'limpiarHistorial'])->name('historial.limpiar');
    });
    
    // Rutas adicionales para el sidebar de configuración (MOVIDO AQUÍ PARA ESTAR PROTEGIDO)
    // NOTA: Estas rutas están comentadas porque entran en conflicto con las rutas de los módulos específicos
    Route::get('/general', [ConfiguracionController::class, 'general'])->name('general');
    Route::get('/funcional', [ConfiguracionController::class, 'funcional'])->name('funcional');
    // Route::get('/notificaciones', [ConfiguracionController::class, 'notificaciones'])->name('notificaciones'); // CONFLICTO: usar configuracion.notificaciones.index
    Route::get('/interfaz', [ConfiguracionController::class, 'interfaz'])->name('interfaz');
});
    
    // API para integraciones con módulos
    Route::prefix('api')->name('api.')->group(function () {
        // Configuraciones para Hallazgos
        Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
            Route::get('/config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\HallazgosConfigController::class, 'getConfigurationsApi'])->name('config');
            Route::post('/apply-config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\HallazgosConfigController::class, 'applyConfigToHallazgo'])->name('apply.config');
            Route::put('/config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\HallazgosConfigController::class, 'updateHallazgosConfig'])->name('update.config');
        });
        
        // Configuraciones para Psicosocial
        Route::prefix('psicosocial')->name('psicosocial.')->group(function () {
            Route::get('/config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController::class, 'getConfigurationsApi'])->name('config');
            Route::post('/apply-config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController::class, 'applyConfigToEvaluacion'])->name('apply.config');
            Route::put('/config/{empresaId}', [App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController::class, 'updatePsicosocialConfig'])->name('update.config');
            Route::get('/instrumentos/{empresaId}', [App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController::class, 'getInstrumentosDisponibles'])->name('instrumentos');
            Route::get('/re-evaluacion/{evaluacionId}/{empresaId}', [App\Http\Controllers\Modules\Configuracion\PsicosocialConfigController::class, 'verificarReEvaluacion'])->name('re.evaluacion');
        });
    });
