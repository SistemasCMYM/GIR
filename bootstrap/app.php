<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware globales para seguridad - TEMPORALMENTE DESHABILITADO
        // $middleware->append([
        //     \App\Http\Middleware\ForceHttps::class,
        //     \App\Http\Middleware\SecurityHeaders::class,
        // ]);
        
        // Middleware CORS para resolver problemas de assets
        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);
        
        // Habilitar CSRF protection
        $middleware->validateCsrfTokens();
        
        // Middleware con alias
        $middleware->alias([
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            'auth.custom' => \App\Http\Middleware\CustomAuth::class,
            'session.persistence' => \App\Http\Middleware\SessionPersistence::class,
            'two.step.auth' => \App\Http\Middleware\TwoStepAuthMiddleware::class,
            'force.https' => \App\Http\Middleware\ForceHttps::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'empresa.access' => \App\Http\Middleware\EmpresaAccessMiddleware::class,
            'module.permission' => \App\Http\Middleware\ModulePermissionMiddleware::class,
            'configuracion.access' => \App\Http\Middleware\ConfiguracionAccessMiddleware::class,
            'mongodb.session' => \App\Http\Middleware\ValidarSesionMongoDB::class,
            'perfil.permisos' => \App\Http\Middleware\ValidarPermisosPerfil::class,
            'sesion.activa' => \App\Http\Middleware\ValidarSesionActiva::class,
            'permisos.cuenta' => \App\Http\Middleware\ValidarPermisosCuenta::class,
            'ensure.empresa' => \App\Http\Middleware\EnsureEmpresaAccess::class,
            'empresa.context' => \App\Http\Middleware\EmpresaContextMiddleware::class,
            'session.validation' => \App\Http\Middleware\SessionValidationMiddleware::class,
        ]);
        
        // Rate limiting
        $middleware->throttleApi();
        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
