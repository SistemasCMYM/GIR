<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\ConfiguracionService;
use App\Services\BateriaPsicosocialService;
use App\Models\EvaluacionPsicosocial;
use App\Policies\EvaluacionPsicosocialPolicy;
use App\Policies\EmpresaPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar ConfiguracionService
        $this->app->singleton(ConfiguracionService::class, function ($app) {
            return new ConfiguracionService();
        });
        
        // Registrar BateriaPsicosocialService
        $this->app->singleton(BateriaPsicosocialService::class, function ($app) {
            return new BateriaPsicosocialService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar políticas de autorización
        Gate::policy(EvaluacionPsicosocial::class, EvaluacionPsicosocialPolicy::class);
        
        // Registrar política general de empresa para otros modelos
        Gate::define('empresa.access', [EmpresaPolicy::class, 'viewAny']);
        Gate::define('empresa.view', [EmpresaPolicy::class, 'view']);
        Gate::define('empresa.create', [EmpresaPolicy::class, 'create']);
        Gate::define('empresa.update', [EmpresaPolicy::class, 'update']);
        Gate::define('empresa.delete', [EmpresaPolicy::class, 'delete']);
    }
}
