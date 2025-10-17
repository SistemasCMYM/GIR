<?php

namespace App\Console\Commands;

use App\Models\Rol;
use App\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CrearRolesSistema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:crear-sistema {--limpiar : Eliminar roles existentes antes de crear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear los 6 roles predefinidos únicos del sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limpiar = $this->option('limpiar');

        try {
            if ($limpiar) {
                $this->info('🧹 Limpiando roles existentes...');
                Rol::truncate();
                $this->line('   Roles eliminados');
            }

            $this->info('📋 Creando los 6 roles únicos del sistema...');
            $this->crearRolesSistema();

            $this->info('✅ Roles del sistema creados exitosamente!');
            $this->mostrarResumen();
            return 0;

        } catch (\Exception $e) {
            $this->error('Error creando roles: ' . $e->getMessage());
            Log::error('Error en CrearRolesSistema: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Crear los 6 roles únicos del sistema
     */
    private function crearRolesSistema()
    {
        foreach (Rol::ROLES_SISTEMA as $codigo => $definicion) {
            $rol = Rol::firstOrCreate(
                [
                    'nombre' => $codigo
                ],
                [
                    'id' => generateBase64UrlId(),
                    'descripcion' => $definicion['descripcion'],
                    'tipo' => $definicion['tipo'],
                    'modulos' => $definicion['modulos'],
                    'permisos' => $definicion['permisos'],
                    'activo' => true,
                    'empresa_id' => null // Roles del sistema no pertenecen a ninguna empresa
                ]
            );

            if ($rol->wasRecentlyCreated) {
                $this->line("  ✅ Creado: {$definicion['nombre']} ({$codigo})");
            } else {
                $this->line("  ⚡ Existe: {$definicion['nombre']} ({$codigo})");
            }
        }
    }

    /**
     * Mostrar resumen de roles creados
     */
    private function mostrarResumen()
    {
        $this->newLine();
        $this->info('📊 RESUMEN DE ROLES DEL SISTEMA:');
        
        $roles = Rol::where('empresa_id', null)->get();
        
        foreach ($roles as $rol) {
            $usuarios = \App\Models\Perfil::where('rol_id', $rol->id)->count();
            $this->line("   🔑 {$rol->nombre} - {$rol->descripcion} ({$usuarios} usuarios)");
        }
        
        $this->newLine();
        $this->info("Total: " . $roles->count() . " roles únicos del sistema");
    }
}
