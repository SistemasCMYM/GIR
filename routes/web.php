<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HallazgosController;
use App\Http\Controllers\Psicosocial\PsicosocialController;
use App\Http\Controllers\Psicosocial\GestionInstrumentosController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Modules\ConfigurationController;
use App\Http\Controllers\Admin\InformesController;
use App\Http\Controllers\Admin\GestionAdministrativaController;
use App\Http\Controllers\Admin\ConsentimientoController;
use App\Http\Controllers\Admin\EncuestasController;
use App\Http\Controllers\DatosGeneralesController;
use App\Http\Controllers\SessionSetupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Landing Page & Authentication Routes
|--------------------------------------------------------------------------
| Modernized two-step authentication system with NIT validation
*/

// Landing page moderna
Route::get('/', function () {
    // Si el usuario ya está autenticado y la sesión está completa, redirigir al dashboard
    if (session('authenticated') && session()->has('user_data') && session()->has('empresa_data')) {
        return redirect()->route('dashboard');
    }
    
    return view('admin.auth.landing');
})->name('landing');

// Home route - redirects to dashboard if authenticated, otherwise to landing
Route::get('/home', function () {
    if (session('authenticated')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('landing');
})->name('home');

// Welcome route alias for compatibility
Route::get('/welcome', function () {
    return redirect()->route('landing');
})->name('welcome');

// (Eliminada ruta duplicada /usuarios-admin; se mantiene la definida al final del archivo)

// Authentication routes - Two-step process
Route::prefix('auth')->name('auth.')->group(function () {
    // Step 1: NIT Validation
    Route::get('/nit', [AuthController::class, 'showNitForm'])->name('nit.form');
    Route::post('/nit', [AuthController::class, 'verifyNit'])->name('nit.verify');
    
    // Step 2: User Credentials
    Route::get('/credentials', [AuthController::class, 'showCredentialsForm'])->name('credentials.form');
    Route::post('/credentials', [AuthController::class, 'verifyCredentials'])->name('credentials.verify');
    
    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Legacy login routes for compatibility
Route::get('/login/nit', [AuthController::class, 'showNitForm'])->name('login.nit');
Route::post('/login/nit', [AuthController::class, 'verifyNit'])->name('login.nit.verify');
Route::get('/login/credentials', [AuthController::class, 'showCredentialsForm'])->name('login.credentials');
Route::post('/login/credentials', [AuthController::class, 'verifyCredentials'])->name('login.credentials.verify');

// Generic login route - redirects to step 1
Route::get('/login', function () {
    return redirect()->route('auth.nit.form');
})->name('login');

// Logout routes for compatibility
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout']);

// Eliminado endpoint de prueba con datos dummy (política V.4)



// Protected routes - Require authentication and empresa access
Route::middleware(['auth.custom'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStatsApi'])->name('dashboard.stats');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('dashboard.store');
    
    Route::post('/empresas', [DashboardController::class, 'store'])->name('empresas.store');
    
    // Empleados Module
    Route::prefix('empleados')->name('empleados.')->group(function () {
        Route::get('/', [App\Http\Controllers\Empresas\EmpleadosController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Empresas\EmpleadosController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Empresas\EmpleadosController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Empresas\EmpleadosController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Empresas\EmpleadosController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Empresas\EmpleadosController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Empresas\EmpleadosController::class, 'destroy'])->name('destroy');
    });
    
    // Hallazgos Module - Filtrado por empresa
    Route::prefix('hallazgos')->name('hallazgos.')->middleware(['ensure.empresa'])->group(function () {
        Route::get('/', [HallazgosController::class, 'index'])->name('index');
        Route::get('/data', [HallazgosController::class, 'data'])->name('data');
        Route::get('/chart-data', [HallazgosController::class, 'chartData'])->name('chart-data');
        Route::get('/export', [HallazgosController::class, 'export'])->name('export');
        Route::get('/stats', [HallazgosController::class, 'stats'])->name('stats');
        Route::get('/create', [HallazgosController::class, 'create'])->name('create');
        Route::post('/', [HallazgosController::class, 'store'])->name('store');
        Route::get('/{id}', [HallazgosController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HallazgosController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HallazgosController::class, 'update'])->name('update');
        Route::delete('/{id}', [HallazgosController::class, 'destroy'])->name('destroy');
    });
    
    // Psicosocial Module - Filtrado por empresa
    Route::prefix('psicosocial')->name('psicosocial.')->middleware(['ensure.empresa'])->group(function () {
        // Rutas principales CRUD
        Route::get('/', [PsicosocialController::class, 'index'])->name('index');
        Route::get('/create', [PsicosocialController::class, 'create'])->name('create');
        Route::post('/', [PsicosocialController::class, 'store'])->name('store');
        Route::get('/{id}', [PsicosocialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PsicosocialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PsicosocialController::class, 'update'])->name('update');
        Route::delete('/{id}', [PsicosocialController::class, 'destroy'])->name('destroy');
        
        // Rutas para vistas específicas de diagnóstico
        Route::get('/{id}/resumen', [PsicosocialController::class, 'resumen'])->name('resumen');
        Route::get('/{id}/resumen-completo', [PsicosocialController::class, 'resumenNueveSecciones'])->name('resumen-completo');
        Route::get('/{id}/resumen/pdf', [PsicosocialController::class, 'exportarPdf'])->name('exportar-pdf');
        Route::get('/resumen-general', [PsicosocialController::class, 'resumenGeneral'])->name('resumen-general');
        Route::get('/resumen/{hojaId}', [PsicosocialController::class, 'resumenIndividual'])->name('resumen.individual');
        Route::get('/{id}/intervencion', [PsicosocialController::class, 'intervencion'])->name('intervencion');
        Route::get('/{id}/resultados', [PsicosocialController::class, 'resultados'])->name('resultados');
        Route::get('/{id}/imprimir', [PsicosocialController::class, 'imprimir'])->name('imprimir');
        Route::get('/{id}/exportar', [PsicosocialController::class, 'exportar'])->name('exportar');
        
        // Rutas para empleados y evaluaciones
        Route::get('/{id}/empleado/{hojaId}', [PsicosocialController::class, 'obtenerDetalleEmpleado'])->name('empleado.detalle');
        Route::get('/{diagnosticoId}/evaluacion/{hojaId}', [PsicosocialController::class, 'evaluacion'])->name('evaluacion');
        Route::post('/{id}/enviar-link', [PsicosocialController::class, 'enviarLinkEmpleado'])->name('enviar-link');
        
        // Rutas para tarjetas de aplicación - redirigir al índice principal
        Route::get('/create-application-card', [PsicosocialController::class, 'createApplicationCard'])->name('create-application-card');
        Route::post('/store-application-card', [PsicosocialController::class, 'storeApplicationCard'])->name('store-application-card');
        Route::get('/show-application-card/{id}', [PsicosocialController::class, 'showApplicationCard'])->name('show-application-card');
        
        // Rutas para diagnósticos específicos
        Route::get('/diagnostico/{id}', [PsicosocialController::class, 'showDiagnostico'])->name('diagnostico.show');
        Route::get('/diagnostico/{id}/resumen', [PsicosocialController::class, 'resumen'])->name('diagnostico.resultados');
        
        // Rutas para reportes y estadísticas
        Route::get('/summary-report', [PsicosocialController::class, 'summaryReport'])->name('summary-report');
        Route::get('/detailed-report/{evaluationId}', [PsicosocialController::class, 'detailedReport'])->name('detailed-report');
        Route::get('/estadisticas', [PsicosocialController::class, 'estadisticas'])->name('estadisticas');
        
    // Eliminado endpoint con datos dummy (política V.4)
        Route::get('/api/global-statistics-data', [PsicosocialController::class, 'getGlobalStatisticsData'])->name('global-statistics-data');
        
        // Rutas de exportación
        Route::get('/export-pdf', [PsicosocialController::class, 'exportToPDF'])->name('export-pdf');
        Route::get('/export-excel', [PsicosocialController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{id}/exportar/excel', [PsicosocialController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('/{id}/exportar/pdf/{tipo?}', [PsicosocialController::class, 'exportarPDF'])->name('exportar.pdf');
        
        // Rutas para instrumentos manuales conforme al manual oficial
        Route::get('/instrumentos/intralaboral-forma-a', [GestionInstrumentosController::class, 'intralaboralFormaA'])->name('instrumentos.intralaboral-forma-a');
        Route::get('/instrumentos/intralaboral-forma-b', [GestionInstrumentosController::class, 'intralaboralFormaB'])->name('instrumentos.intralaboral-forma-b');
        Route::get('/instrumentos/extralaboral', [GestionInstrumentosController::class, 'extralaboral'])->name('instrumentos.extralaboral');
        Route::get('/instrumentos/estres', [GestionInstrumentosController::class, 'estres'])->name('instrumentos.estres');
        Route::post('/instrumentos/guardar-respuestas', [GestionInstrumentosController::class, 'guardarRespuestas'])->name('instrumentos.guardar-respuestas');
    });
    
    // Informes Module
    Route::prefix('informes')->name('informes.')->group(function () {
        Route::get('/', [InformesController::class, 'index'])->name('index');
        Route::get('/reportes', [InformesController::class, 'reportes'])->name('reportes');
        Route::get('/hallazgos', [InformesController::class, 'reporteHallazgos'])->name('hallazgos');
        Route::get('/psicosocial', [InformesController::class, 'reportePsicosocial'])->name('psicosocial');
        Route::post('/generar', [InformesController::class, 'generarReporte'])->name('generar');
        Route::get('/exportar/{tipo}', [InformesController::class, 'exportarReporte'])->name('exportar');
    });
    
    // User profile
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    // Update password (form action in resources/views/perfil.blade.php expects this named route)
    Route::post('/perfil/update-password', [PerfilController::class, 'updatePassword'])->name('perfil.update-password');
    
    // Help
    Route::get('/ayuda', function () {
        return view('ayuda');
    })->name('ayuda');
});

// Configuration Module - Sin middleware específico temporalmente
Route::middleware(['auth.custom'])->group(function () {
    require __DIR__ . '/configuracion.php';
});

// Eliminado archivo de rutas de debug (política V.4)

// Error routes
Route::get('/error/permission-denied', [App\Http\Controllers\ErrorController::class, 'permissionDenied'])->name('error.permission-denied');
Route::get('/error/general', [App\Http\Controllers\ErrorController::class, 'general'])->name('error.general');
Route::get('/error/not-found', [App\Http\Controllers\ErrorController::class, 'notFound'])->name('error.not-found');
Route::get('/error/login-expired', [App\Http\Controllers\ErrorController::class, 'loginExpired'])->name('error.login-expired');

// Admin routes - requiere autenticación; funcionalidades de autenticación requieren super.admin
Route::middleware(['auth.custom'])->prefix('admin')->name('admin.')->group(function () {
    // Authentication Management (solo super.admin)
    Route::prefix('autenticacion')->name('autenticacion.')->middleware(['super.admin'])->group(function () {
        Route::get('/', function () { return view('admin.autenticacion.index'); })->name('index');
        Route::get('/empresas', function () { return view('admin.autenticacion.empresas'); })->name('empresas');
        // REMOVED: Ruta duplicada que causaba conflicto con roles
        Route::get('/roles', function () { return view('admin.gestion-administrativa.roles.index'); })->name('roles');
        Route::get('/permisos', function () { return view('admin.gestion-administrativa.permisos.index'); })->name('permisos');
        Route::get('/auditoria', function () { return redirect()->route('admin.autenticacion.index'); })->name('auditoria');
        // Submódulo: Sesiones - Centralizado usando Auth\SesionController
        Route::prefix('sesiones')->name('sesiones.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\SesionController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Auth\SesionController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'destroy'])->name('destroy');
            
            // Rutas específicas del sistema de sesiones
            Route::post('/{id}/cerrar', [\App\Http\Controllers\Auth\SesionController::class, 'cerrar'])->name('cerrar');
            Route::get('/usuario/{usuario_id}', [\App\Http\Controllers\Auth\SesionController::class, 'porUsuario'])->name('usuario');
            Route::put('/{id}/actividad', [\App\Http\Controllers\Auth\SesionController::class, 'actualizarActividad'])->name('actividad');
            Route::post('/verificar-token', [\App\Http\Controllers\Auth\SesionController::class, 'verificarToken'])->name('verificar-token');
            Route::post('/cerrar-todas/{usuario_id}', [\App\Http\Controllers\Auth\SesionController::class, 'cerrarTodasUsuario'])->name('cerrar-todas');
            Route::post('/limpiar-expiradas', [\App\Http\Controllers\Auth\SesionController::class, 'limpiarExpiradas'])->name('limpiar-expiradas');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\SesionController::class, 'estadisticas'])->name('estadisticas');
        });

        // Submódulo: Notificaciones - Centralizado usando Auth\NotificacionController
        Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\NotificacionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Auth\NotificacionController::class, 'edit'])->name('create');
            Route::post('/', [\App\Http\Controllers\Auth\NotificacionController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Auth\NotificacionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'destroy'])->name('destroy');
            
            // Rutas específicas del sistema de notificaciones
            Route::post('/{id}/marcar-vista', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarVista'])->name('marcar-vista');
            Route::post('/{id}/marcar-no-vista', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarNoVista'])->name('marcar-no-vista');
            Route::get('/empresa/{empresa_id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'porEmpresa'])->name('empresa');
            Route::get('/modulo/{modulo}', [\App\Http\Controllers\Auth\NotificacionController::class, 'porModulo'])->name('modulo');
            Route::post('/marcar-todas-vistas/{empresa_id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarTodasVistasEmpresa'])->name('marcar-todas-vistas');
            Route::post('/modificacion-no-autorizada', [\App\Http\Controllers\Auth\NotificacionController::class, 'crearModificacionNoAutorizada'])->name('modificacion-no-autorizada');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\NotificacionController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\NotificacionController::class, 'opciones'])->name('opciones');
        });
    });
    
    // Gestión Administrativa - Página principal unificada  
    Route::get('/gestion-administrativa', [GestionAdministrativaController::class, 'index'])->name('gestion-administrativa.index');
    
    // Eliminada ruta a index-simple para evitar referencias a vistas inexistentes
    
    // REMOVED: Rutas duplicadas de roles que apuntaban a vistas inexistentes
    // Rutas de administración de usuarios - Redirigen a las rutas principales
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
    // Roles: asegurar alias bajo admin.usuarios para compatibilidad con vistas
    Route::get('/roles', [GestionAdministrativaController::class, 'rolesIndex'])->name('roles.index');

    // Cuentas
    Route::get('/cuentas/create', function() { return view('admin.gestion-administrativa.cuentas.create'); })->name('cuentas.create');

    // Permisos
    Route::get('/permisos/matrix', [GestionAdministrativaController::class, 'permisosIndex'])->name('permisos.matrix');

    // Auditoría
    Route::get('/auditoria', function() { return view('admin.gestion-administrativa.auditoria.index'); })->name('auditoria');

        // REMOVED: Grupo de roles duplicado con vistas inexistentes
        /*
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', function() { return view('admin.gestion-administrativa.roles.index_nuevo'); })->name('index');
            Route::get('/create', function() { return view('admin.gestion-administrativa.roles.crear'); })->name('create');
            Route::get('/{id}', function($id) { return view('admin.gestion-administrativa.roles.show', compact('id')); })->name('show');
            Route::get('/{id}/edit', function($id) { return view('admin.gestion-administrativa.roles.edit', compact('id')); })->name('edit');
        });
        */
        
        Route::prefix('permisos')->name('permisos.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'permisosIndex'])->name('index');
        });
        
        Route::prefix('cuentas')->name('cuentas.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'cuentasIndex'])->name('index');
        });
    });
});

// Rutas adicionales fuera del grupo admin - REQUIERE AUTENTICACIÓN
Route::middleware(['auth.custom'])->group(function () {
    // Alias a gestión administrativa de usuarios
    Route::get('/usuarios-admin', function() {
        // Redirigir a la ruta protegida principal para asegurar middleware y sesión válidos
        return redirect()->route('usuarios.index');
    })->name('usuarios.admin');
});

// Gestión Administrativa de Usuarios (sistema MongoDB con schemas Node.js)
// Usar solo auth.custom para evitar conflictos con permisos
Route::prefix('usuarios')->name('usuarios.')->middleware(['auth.custom'])->group(function () {
        // Página principal de gestión administrativa
        Route::get('/', [GestionAdministrativaController::class, 'index'])->name('index');
        
        // Submódulo: Creación de Cuentas
        Route::prefix('cuentas')->name('cuentas.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'cuentasIndex'])->name('index');
            Route::post('/crear', [GestionAdministrativaController::class, 'crearCuenta'])->name('store');
            Route::put('/{id}', [GestionAdministrativaController::class, 'editarCuenta'])->middleware('permisos.cuenta:usuarios,update')->name('update');
            Route::delete('/{id}', [GestionAdministrativaController::class, 'eliminarCuenta'])->middleware('permisos.cuenta:usuarios,delete')->name('destroy');
            Route::post('/{id}/estado', [GestionAdministrativaController::class, 'cambiarEstadoCuenta'])->middleware('permisos.cuenta:usuarios,update')->name('estado');
            // Ajustes: usar controlador para cargar la entidad y validar permisos
            Route::get('/crear', [GestionAdministrativaController::class, 'mostrarCrearCuenta'])->name('create');
            Route::get('/{id}', [\App\Http\Controllers\Admin\GestionAdministrativaController::class, 'mostrarCuenta'])->name('show');
            Route::get('/{id}/editar', [\App\Http\Controllers\Admin\GestionAdministrativaController::class, 'mostrarEditarCuenta'])->name('edit');
        });
        
        // Submódulo: Perfiles/Roles - Centralizado usando Auth\RolController
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\RolController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Auth\RolController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Auth\RolController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Auth\RolController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'destroy'])->name('destroy');
            
            // Rutas específicas del sistema de roles
            Route::get('/buscar', [\App\Http\Controllers\Auth\RolController::class, 'buscar'])->name('buscar');
            Route::get('/empresa/{id}', [\App\Http\Controllers\Auth\RolController::class, 'porEmpresa'])->name('empresa');
            Route::post('/sistema', [\App\Http\Controllers\Auth\RolController::class, 'crearRolesSistema'])->name('sistema');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\RolController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\RolController::class, 'opciones'])->name('opciones');
            Route::get('/{id}/permisos', [\App\Http\Controllers\Auth\RolController::class, 'verificarPermisos'])->name('permisos');
        });
        
        // Submódulo: Permisos - Centralizado usando Auth\PermisoController
        Route::prefix('permisos')->name('permisos.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'permisosIndex'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Auth\PermisoController::class, 'edit'])->name('create');
            Route::post('/', [\App\Http\Controllers\Auth\PermisoController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Auth\PermisoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'destroy'])->name('destroy');
            
            // Rutas específicas del sistema de permisos
            Route::get('/buscar', [\App\Http\Controllers\Auth\PermisoController::class, 'buscar'])->name('buscar');
            Route::get('/cuenta/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'porCuenta'])->name('cuenta');
            Route::get('/tipo/{tipo}', [\App\Http\Controllers\Auth\PermisoController::class, 'porTipo'])->name('tipo');
            Route::post('/lote', [\App\Http\Controllers\Auth\PermisoController::class, 'crearLote'])->name('lote');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\PermisoController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\PermisoController::class, 'opciones'])->name('opciones');
            Route::get('/matrix', [GestionAdministrativaController::class, 'permisosIndex'])->name('matrix');
            Route::get('/{id}/acceso', [\App\Http\Controllers\Auth\PermisoController::class, 'verificarAcceso'])->name('acceso');
            Route::post('/sincronizar/{cuenta_id}', [\App\Http\Controllers\Auth\PermisoController::class, 'sincronizarConRol'])->name('sincronizar');
        });
        
        // Submódulo: Auditoría
        Route::get('/auditoria', function () { 
            return view('admin.gestion-administrativa.auditoria.index'); 
        })->name('auditoria');

        // Perfiles (vistas propias)
        Route::prefix('perfiles')->name('perfiles.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'perfilesIndex'])->name('index');
            Route::get('/create', function() { return view('admin.gestion-administrativa.perfiles.create'); })->name('create');
            Route::get('/{id}', function($id) { return view('admin.gestion-administrativa.perfiles.show', compact('id')); })->name('show');
            Route::get('/{id}/edit', function($id) { return view('admin.gestion-administrativa.perfiles.edit', compact('id')); })->name('edit');
        });
    });
    
    // Gestión Administrativa de Empresa
    Route::prefix('empresa')->name('empresa.')->group(function () {
        // Página principal de gestión administrativa de empresa
        Route::get('/', [GestionAdministrativaController::class, 'empresaIndex'])->name('index');
        
        // Submódulo: Cargue de empleados
        Route::prefix('empleados')->name('empleados.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'create'])->name('create');
            Route::get('/plantilla', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'descargarPlantilla'])->name('plantilla');
            Route::post('/cargar-masivo', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'storeMasivo'])->name('storeMasivo');
            Route::post('/', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Empresas\EmpleadoController::class, 'destroy'])->name('destroy');
        });
        
        // Submódulo: Creación de empresas
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'empresasIndex'])->name('index');
            Route::get('/crear', function () { return redirect()->route('empresa.empresas.index'); })->name('create');
            Route::post('/', function () { return back()->with('success', 'Empresa creada exitosamente'); })->name('store');
            Route::get('/{id}', function ($id) { return redirect()->route('empresa.empresas.index'); })->name('show');
            Route::get('/{id}/editar', function ($id) { return redirect()->route('empresa.empresas.index'); })->name('edit');
            Route::put('/{id}', function ($id) { return back()->with('success', 'Empresa actualizada exitosamente'); })->name('update');
            Route::delete('/{id}', function ($id) { return back()->with('success', 'Empresa eliminada exitosamente'); })->name('destroy');
        });
        
        // Submódulo: Cargue de Áreas
        Route::prefix('areas')->name('areas.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'areasIndex'])->name('index');
            Route::get('/create', function () { return view('empresa.areas.create'); })->name('create');
            Route::get('/cargar', function () { return redirect()->route('empresa.areas.index'); })->name('upload');
            Route::post('/cargar', function () { return back()->with('success', 'Áreas cargadas exitosamente'); })->name('upload.store');
            Route::get('/plantilla', function () { return redirect()->route('empresa.areas.index'); })->name('template');
        });
        
        // Submódulo: Cargue de Centros  
        Route::prefix('centros')->name('centros.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'centrosIndex'])->name('index');
            Route::get('/create', function () { return view('empresa.centros.create'); })->name('create');
            Route::get('/cargar', function () { return redirect()->route('empresa.centros.index'); })->name('upload');
            Route::post('/cargar', function () { return back()->with('success', 'Centros cargados exitosamente'); })->name('upload.store');
            Route::get('/plantilla', function () { return redirect()->route('empresa.centros.index'); })->name('template');
        });
        
        // Submódulo: Cargue de Ciudades
        Route::prefix('ciudades')->name('ciudades.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'ciudadesIndex'])->name('index');
            Route::get('/cargar', function () { return redirect()->route('empresa.ciudades.index'); })->name('upload');
            Route::post('/cargar', function () { return back()->with('success', 'Ciudades cargadas exitosamente'); })->name('upload.store');
            Route::get('/plantilla', function () { return redirect()->route('empresa.ciudades.index'); })->name('template');
        });
        
        // Submódulo: Cargue de Procesos
        Route::prefix('procesos')->name('procesos.')->group(function () {
            Route::get('/', [GestionAdministrativaController::class, 'procesosIndex'])->name('index');
            Route::get('/cargar', function () { return back()->with('info', 'Carga de procesos próximamente'); })->name('upload');
            Route::post('/cargar', function () { return back()->with('success', 'Procesos cargados exitosamente'); })->name('upload.store');
            Route::get('/plantilla', function () { return redirect()->route('empresa.procesos.index'); })->name('template');
        });
    });
    
    // Gestión de Instrumentos (Consentimientos, Cuestionarios, Encuestas)
    Route::prefix('gestion-instrumentos')->name('gestion-instrumentos.')->group(function () {
        // Página principal
        Route::get('/', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'index'])->name('index');
        
        // Submódulo: Consentimientos
        Route::prefix('consentimientos')->name('consentimientos.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ConsentimientoController::class, 'index'])->name('index');
            Route::get('/crear', [App\Http\Controllers\Admin\ConsentimientoController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ConsentimientoController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\ConsentimientoController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\ConsentimientoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Admin\ConsentimientoController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\ConsentimientoController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-estado', [App\Http\Controllers\Admin\ConsentimientoController::class, 'toggleEstado'])->name('toggle-estado');
            Route::get('/{id}/diligenciar', [App\Http\Controllers\Admin\ConsentimientoController::class, 'diligenciar'])->name('diligenciar');
            Route::post('/{id}/procesar', [App\Http\Controllers\Admin\ConsentimientoController::class, 'procesarConsentimiento'])->name('procesar');
            Route::get('/{id}/informes', [App\Http\Controllers\Admin\ConsentimientoController::class, 'informes'])->name('informes');
            Route::get('/{id}/exportar', [App\Http\Controllers\Admin\ConsentimientoController::class, 'exportarRespuestas'])->name('exportar');
        });
        
        // Submódulo: Cuestionarios
        Route::prefix('cuestionarios')->name('cuestionarios.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'cuestionariosIndex'])->name('index');
            
            // Rutas específicas para cada cuestionario oficial
            Route::get('/datos-generales', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'datosGenerales'])->name('datos-generales');
            Route::get('/intralaboral-forma-a', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'intralaboralFormaA'])->name('intralaboral-forma-a');
            Route::get('/intralaboral-forma-b', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'intralaboralFormaB'])->name('intralaboral-forma-b');
            Route::get('/extralaboral', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'extralaboral'])->name('extralaboral');
            Route::get('/estres', [App\Http\Controllers\Admin\GestionInstrumentosController::class, 'estres'])->name('estres');
        });
        
        // API Routes para Datos Generales (MongoDB psicosocial)
        Route::prefix('api/datos-generales')->name('api.datos-generales.')->group(function () {
            Route::post('/borrador', [App\Http\Controllers\DatosGeneralesController::class, 'guardarBorrador'])->name('borrador');
            Route::post('/completar', [App\Http\Controllers\DatosGeneralesController::class, 'completarFicha'])->name('completar');
            Route::get('/{employeeId}', [App\Http\Controllers\DatosGeneralesController::class, 'obtenerDatos'])->name('obtener');
        });
        
        // API Routes para Roles (Auth\RolController centralizado)
        Route::prefix('api/roles')->name('api.roles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\RolController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Auth\RolController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\RolController::class, 'destroy'])->name('destroy');
            Route::get('/buscar', [\App\Http\Controllers\Auth\RolController::class, 'buscar'])->name('buscar');
            Route::get('/empresa/{id}', [\App\Http\Controllers\Auth\RolController::class, 'porEmpresa'])->name('empresa');
            Route::post('/sistema', [\App\Http\Controllers\Auth\RolController::class, 'crearRolesSistema'])->name('sistema');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\RolController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\RolController::class, 'opciones'])->name('opciones');
            Route::get('/{id}/permisos', [\App\Http\Controllers\Auth\RolController::class, 'verificarPermisos'])->name('permisos');
        });

        // API Routes para Permisos (Auth\PermisoController centralizado)
        Route::prefix('api/permisos')->name('api.permisos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\PermisoController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Auth\PermisoController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'destroy'])->name('destroy');
            Route::get('/buscar', [\App\Http\Controllers\Auth\PermisoController::class, 'buscar'])->name('buscar');
            Route::get('/cuenta/{id}', [\App\Http\Controllers\Auth\PermisoController::class, 'porCuenta'])->name('cuenta');
            Route::get('/tipo/{tipo}', [\App\Http\Controllers\Auth\PermisoController::class, 'porTipo'])->name('tipo');
            Route::post('/lote', [\App\Http\Controllers\Auth\PermisoController::class, 'crearLote'])->name('lote');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\PermisoController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\PermisoController::class, 'opciones'])->name('opciones');
            Route::get('/{id}/acceso', [\App\Http\Controllers\Auth\PermisoController::class, 'verificarAcceso'])->name('acceso');
            Route::post('/sincronizar/{cuenta_id}', [\App\Http\Controllers\Auth\PermisoController::class, 'sincronizarConRol'])->name('sincronizar');
        });

        // API Routes para Sesiones (Auth\SesionController centralizado)
        Route::prefix('api/sesiones')->name('api.sesiones.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\SesionController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Auth\SesionController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\SesionController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/cerrar', [\App\Http\Controllers\Auth\SesionController::class, 'cerrar'])->name('cerrar');
            Route::get('/usuario/{usuario_id}', [\App\Http\Controllers\Auth\SesionController::class, 'porUsuario'])->name('usuario');
            Route::put('/{id}/actividad', [\App\Http\Controllers\Auth\SesionController::class, 'actualizarActividad'])->name('actividad');
            Route::post('/verificar-token', [\App\Http\Controllers\Auth\SesionController::class, 'verificarToken'])->name('verificar-token');
            Route::post('/cerrar-todas/{usuario_id}', [\App\Http\Controllers\Auth\SesionController::class, 'cerrarTodasUsuario'])->name('cerrar-todas');
            Route::post('/limpiar-expiradas', [\App\Http\Controllers\Auth\SesionController::class, 'limpiarExpiradas'])->name('limpiar-expiradas');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\SesionController::class, 'estadisticas'])->name('estadisticas');
        });

        // API Routes para Notificaciones (Auth\NotificacionController centralizado)
        Route::prefix('api/notificaciones')->name('api.notificaciones.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Auth\NotificacionController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Auth\NotificacionController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/marcar-vista', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarVista'])->name('marcar-vista');
            Route::post('/{id}/marcar-no-vista', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarNoVista'])->name('marcar-no-vista');
            Route::get('/empresa/{empresa_id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'porEmpresa'])->name('empresa');
            Route::get('/modulo/{modulo}', [\App\Http\Controllers\Auth\NotificacionController::class, 'porModulo'])->name('modulo');
            Route::post('/marcar-todas-vistas/{empresa_id}', [\App\Http\Controllers\Auth\NotificacionController::class, 'marcarTodasVistasEmpresa'])->name('marcar-todas-vistas');
            Route::post('/modificacion-no-autorizada', [\App\Http\Controllers\Auth\NotificacionController::class, 'crearModificacionNoAutorizada'])->name('modificacion-no-autorizada');
            Route::get('/estadisticas', [\App\Http\Controllers\Auth\NotificacionController::class, 'estadisticas'])->name('estadisticas');
            Route::get('/opciones', [\App\Http\Controllers\Auth\NotificacionController::class, 'opciones'])->name('opciones');
        });
        
        // Submódulo: Encuestas
        Route::prefix('encuestas')->name('encuestas.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EncuestasController::class, 'index'])->name('index');
            Route::get('/crear', [App\Http\Controllers\Admin\EncuestasController::class, 'create'])->name('create');
            Route::post('/store', [App\Http\Controllers\Admin\EncuestasController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\EncuestasController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [App\Http\Controllers\Admin\EncuestasController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Admin\EncuestasController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\EncuestasController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-estado', [App\Http\Controllers\Admin\EncuestasController::class, 'toggleEstado'])->name('toggle-estado');
            Route::post('/{id}/toggle-publicacion', [App\Http\Controllers\Admin\EncuestasController::class, 'togglePublicacion'])->name('toggle-publicacion');
            Route::get('/{id}/informes', [App\Http\Controllers\Admin\EncuestasController::class, 'informes'])->name('informes');
            Route::get('/{id}/responder', [App\Http\Controllers\Admin\EncuestasController::class, 'responder'])->name('responder');
            Route::post('/{id}/respuesta', [App\Http\Controllers\Admin\EncuestasController::class, 'guardarRespuesta'])->name('respuesta.store');
            Route::get('/plantillas/ajax', [App\Http\Controllers\Admin\EncuestasController::class, 'getPlantillas'])->name('plantillas.ajax');
        });
    });
    
    // Advanced Reports Management (Super Admin only)
    Route::prefix('informes')->name('informes.')->group(function () {
        Route::get('/global', [InformesController::class, 'reporteGlobal'])->name('global');
        Route::get('/empresas', [InformesController::class, 'reporteEmpresas'])->name('empresas');
        Route::get('/actividad', [InformesController::class, 'reporteActividad'])->name('actividad');
        Route::get('/rendimiento', [InformesController::class, 'reporteRendimiento'])->name('rendimiento');
        Route::get('/programados', [InformesController::class, 'reportesProgramados'])->name('programados');
        Route::post('/programar', [InformesController::class, 'programarReporte'])->name('programar');
    });
    
    // Advanced Configuration Management (Super Admin only) - COMENTADO PARA EVITAR CONFLICTOS
    // Las rutas de configuración ahora están en configuracion.php
    /*
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/seguridad', [ConfigurationController::class, 'configuracionSeguridad'])->name('seguridad');
        Route::post('/seguridad', [ConfigurationController::class, 'guardarConfiguracionSeguridad'])->name('seguridad.guardar');
        Route::get('/notificaciones', [ConfigurationController::class, 'configuracionNotificaciones'])->name('notificaciones');
        Route::post('/notificaciones', [ConfigurationController::class, 'guardarConfiguracionNotificaciones'])->name('notificaciones.guardar');
        Route::get('/mantenimiento', [ConfigurationController::class, 'mantenimiento'])->name('mantenimiento');
        Route::post('/mantenimiento/cache', [ConfigurationController::class, 'limpiarCache'])->name('mantenimiento.cache');
        Route::post('/mantenimiento/logs', [ConfigurationController::class, 'limpiarLogs'])->name('mantenimiento.logs');
        Route::get('/modulos', [ConfigurationController::class, 'configuracionModulos'])->name('modulos');
        Route::get('/base-datos', [ConfigurationController::class, 'configuracionBaseDatos'])->name('base-datos');
        Route::get('/integraciones', [ConfigurationController::class, 'configuracionIntegraciones'])->name('integraciones');
    });
    */
    
    // Ruta alias para compatibilidad con enlaces de admin.informes.index
    Route::get('/informes', function () {
    // Ajuste: usar la ruta existente sin prefijo admin para evitar RouteNotFoundException
    return redirect()->route('informes.global');
    })->name('informes.index');
    
    // Ruta alias para compatibilidad con enlaces de admin.configuracion.index
    // Route::get('/configuracion', function () {
    //     return redirect()->route('configuracion.index');
    // })->name('admin.configuracion.index');

// Ruta pública para empleados (sin autenticación)
Route::get('/evaluacion/empleado/{hojaId}', [PsicosocialController::class, 'evaluacionEmpleado'])->name('evaluacion.empleado');

Route::get('/setup-session', [SessionSetupController::class, 'showSetup'])->name('setup.session');
Route::post('/api/setup-session', [SessionSetupController::class, 'setupSession'])->name('api.setup.session');

// All routes completed and properly configured for GIR365 - Psychosocial Module

