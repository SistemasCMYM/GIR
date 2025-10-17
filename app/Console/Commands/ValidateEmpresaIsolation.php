<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Helpers\EmpresaHelper;
use App\Models\EvaluacionPsicosocial;
use App\Models\Empresas\Empleado;
use App\Models\Empresas\Area;
use App\Models\Encuesta;

class ValidateEmpresaIsolation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sistema:validar-aislamiento-empresa {--dry-run : Solo mostrar resultados sin modificar}';

    /**
     * The console command description.
     */
    protected $description = 'Valida y corrige el aislamiento de datos por empresa en todos los modelos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('=== VALIDACIÓN DEL SISTEMA DE AISLAMIENTO DE DATOS POR EMPRESA ===');
        $this->newLine();
        
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: Solo se mostrarán los resultados, no se realizarán cambios');
            $this->newLine();
        }

        // 1. Verificar que todos los modelos tienen empresa_id
        $this->validateModelsHaveEmpresaId($dryRun);
        
        // 2. Verificar que no hay datos hardcoded
        $this->validateNoHardcodedData($dryRun);
        
        // 3. Verificar integridad de las relaciones empresa
        $this->validateEmpresaRelationships($dryRun);
        
        // 4. Generar estadísticas de datos por empresa
        $this->generateEmpresaStats();
        
        $this->newLine();
        $this->info('=== VALIDACIÓN COMPLETADA ===');
    }

    /**
     * Validar que todos los registros tienen empresa_id
     */
    private function validateModelsHaveEmpresaId($dryRun)
    {
        $this->info('📋 Validando que todos los registros tienen empresa_id...');
        $this->newLine();

        $models = [
            EvaluacionPsicosocial::class => 'Evaluaciones Psicosociales',
            Empleado::class => 'Empleados',
            Encuesta::class => 'Encuestas',
            Area::class => 'Áreas'
        ];

        foreach ($models as $modelClass => $modelName) {
            try {
                $total = $modelClass::count();
                $sinEmpresa = $modelClass::whereNull('empresa_id')->count();
                
                if ($sinEmpresa > 0) {
                    $this->warn("❌ {$modelName}: {$sinEmpresa} de {$total} registros SIN empresa_id");
                    
                    if (!$dryRun) {
                        $this->warn("⚠️  Se requiere intervención manual para asignar empresa_id");
                    }
                } else {
                    $this->info("✅ {$modelName}: {$total} registros con empresa_id válido");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error validando {$modelName}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
    }

    /**
     * Validar que no hay datos hardcoded o de prueba
     */
    private function validateNoHardcodedData($dryRun)
    {
        $this->info('🔍 Buscando datos hardcoded o de prueba...');
        $this->newLine();

        $prohibitedPatterns = [
            'test', 'prueba', 'dummy', 'fake', 'example', 'demo'
        ];

        $models = [
            EvaluacionPsicosocial::class => ['codigo', 'observaciones'],
            Empleado::class => ['nombre', 'apellidos', 'email'],
            Encuesta::class => ['titulo', 'descripcion'],
            Area::class => ['nombre', 'descripcion']
        ];

        $foundProblems = false;

        foreach ($models as $modelClass => $fields) {
            $modelName = class_basename($modelClass);
            
            foreach ($fields as $field) {
                foreach ($prohibitedPatterns as $pattern) {
                    try {
                        $count = $modelClass::where($field, 'like', "%{$pattern}%")->count();
                        
                        if ($count > 0) {
                            $this->warn("❌ {$modelName}.{$field}: {$count} registros contienen '{$pattern}'");
                            $foundProblems = true;
                            
                            if (!$dryRun) {
                                $registros = $modelClass::where($field, 'like', "%{$pattern}%")->take(5)->get();
                                foreach ($registros as $registro) {
                                    $this->line("   ID: {$registro->id} - {$field}: {$registro->{$field}}");
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Error buscando patrón '{$pattern}' en {$modelName}.{$field}: " . $e->getMessage());
                    }
                }
            }
        }

        if (!$foundProblems) {
            $this->info('✅ No se encontraron datos hardcoded o de prueba');
        }
        
        $this->newLine();
    }

    /**
     * Validar integridad de relaciones empresa
     */
    private function validateEmpresaRelationships($dryRun)
    {
        $this->info('🔗 Validando integridad de relaciones empresa...');
        $this->newLine();

        try {
            // Verificar empleados con empresas válidas
            $empleadosHuerfanos = Empleado::whereNotNull('empresa_id')
                ->get()
                ->filter(function ($empleado) {
                    try {
                        return !$empleado->empresa;
                    } catch (\Exception $e) {
                        return true; // Asumir huérfano si hay error
                    }
                })
                ->count();

            if ($empleadosHuerfanos > 0) {
                $this->warn("❌ {$empleadosHuerfanos} empleados con empresa_id inválido");
            } else {
                $this->info('✅ Todas las relaciones empleado-empresa son válidas');
            }

            // Verificar evaluaciones con empleados válidos
            $evaluacionesHuerfanas = EvaluacionPsicosocial::whereNotNull('empleado_id')
                ->get()
                ->filter(function ($evaluacion) {
                    try {
                        return !$evaluacion->empleado;
                    } catch (\Exception $e) {
                        return true;
                    }
                })
                ->count();

            if ($evaluacionesHuerfanas > 0) {
                $this->warn("❌ {$evaluacionesHuerfanas} evaluaciones con empleado_id inválido");
            } else {
                $this->info('✅ Todas las relaciones evaluación-empleado son válidas');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error validando relaciones: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Generar estadísticas de datos por empresa
     */
    private function generateEmpresaStats()
    {
        $this->info('📊 Estadísticas de datos por empresa:');
        $this->newLine();

        try {
            // Obtener lista de empresas únicas
            $empresasIds = collect();
            
            $empresasIds = $empresasIds->merge(Empleado::distinct('empresa_id')->pluck('empresa_id')->filter());
            $empresasIds = $empresasIds->merge(EvaluacionPsicosocial::distinct('empresa_id')->pluck('empresa_id')->filter());
            $empresasIds = $empresasIds->merge(Encuesta::distinct('empresa_id')->pluck('empresa_id')->filter());
            $empresasIds = $empresasIds->merge(Area::distinct('empresa_id')->pluck('empresa_id')->filter());
            
            $empresasIds = $empresasIds->unique();

            $this->table(
                ['Empresa ID', 'Empleados', 'Evaluaciones', 'Encuestas', 'Áreas'],
                $empresasIds->map(function ($empresaId) {
                    return [
                        $empresaId,
                        Empleado::where('empresa_id', $empresaId)->count(),
                        EvaluacionPsicosocial::where('empresa_id', $empresaId)->count(),
                        Encuesta::where('empresa_id', $empresaId)->count(),
                        Area::where('empresa_id', $empresaId)->count(),
                    ];
                })
            );

        } catch (\Exception $e) {
            $this->error('❌ Error generando estadísticas: ' . $e->getMessage());
        }
    }
}
