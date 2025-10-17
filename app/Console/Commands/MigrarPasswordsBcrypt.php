<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrarPasswordsBcrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:migrate-passwords {--dry-run : Solo mostrar qué se migraría sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar contraseñas legacy (MD5/SHA1/texto plano) a Bcrypt con factor 11';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 MODO DRY-RUN: Solo analizando, no se harán cambios');
        } else {
            $this->info('🔄 INICIANDO MIGRACIÓN DE CONTRASEÑAS A BCRYPT');
        }
        
        try {
            // Obtener todas las cuentas
            $cuentas = DB::connection('mongodb_cmym')
                        ->collection('cuentas')
                        ->where('estado', '!=', 'inactiva')
                        ->get();
            
            $total = $cuentas->count();
            $migraciones = 0;
            $errores = 0;
            $yaBcrypt = 0;
            
            $this->info("📊 Total de cuentas encontradas: {$total}");
            
            if ($total > 0) {
                $bar = $this->output->createProgressBar($total);
                $bar->start();
                
                foreach ($cuentas as $cuenta) {
                    $email = $cuenta['email'] ?? 'sin-email';
                    $passwordField = isset($cuenta['password']) ? 'password' : 'contrasena';
                    $hash = $cuenta[$passwordField] ?? '';
                    
                    if (empty($hash)) {
                        $this->newLine();
                        $this->warn("⚠️  Cuenta sin contraseña: {$email}");
                        $errores++;
                        $bar->advance();
                        continue;
                    }
                    
                    $cuentaModel = new Cuenta();
                    
                    // Verificar si ya es Bcrypt
                    if (!$cuentaModel->necesitaMigracionBcrypt($hash)) {
                        $yaBcrypt++;
                        $bar->advance();
                        continue;
                    }
                    
                    // Detectar algoritmo actual
                    $algoritmo = $this->detectarAlgoritmo($hash);
                    
                    if ($isDryRun) {
                        $this->newLine();
                        $this->line("🔍 {$email}: {$algoritmo} -> Bcrypt (DRY-RUN)");
                    } else {
                        // Solo podemos migrar si conocemos el algoritmo
                        if ($algoritmo === 'Texto plano') {
                            // Para texto plano, usar la contraseña como está
                            $nuevoHash = $cuentaModel->generarHash($hash);
                            
                            try {
                                DB::connection('mongodb_cmym')
                                  ->collection('cuentas')
                                  ->where('id', $cuenta['id'])
                                  ->update([
                                      'contrasena' => $nuevoHash,
                                      'password' => $nuevoHash,
                                      'migrated_to_bcrypt' => true,
                                      'migration_date' => now()->toISOString(),
                                      'original_algorithm' => $algoritmo
                                  ]);
                                  
                                $migraciones++;
                                $this->newLine();
                                $this->info("✅ {$email}: {$algoritmo} -> Bcrypt");
                            } catch (\Exception $e) {
                                $this->newLine();
                                $this->error("❌ Error migrando {$email}: " . $e->getMessage());
                                $errores++;
                            }
                        } else {
                            // Para hashes (MD5, SHA1, etc.) no podemos migrar sin la contraseña original
                            $this->newLine();
                            $this->warn("⚠️  {$email}: {$algoritmo} - Requiere re-autenticación para migrar");
                        }
                    }
                    
                    $bar->advance();
                }
                
                $bar->finish();
            }
            
            $this->newLine(2);
            $this->info('📈 RESUMEN DE MIGRACIÓN:');
            $this->line("   Total cuentas: {$total}");
            $this->line("   Ya en Bcrypt: {$yaBcrypt}");
            
            if ($isDryRun) {
                $necesitanMigracion = $total - $yaBcrypt - $errores;
                $this->line("   Necesitan migración: {$necesitanMigracion}");
            } else {
                $this->line("   Migradas ahora: {$migraciones}");
                $this->line("   Errores: {$errores}");
                
                if ($migraciones > 0) {
                    $this->info("✅ Se migraron {$migraciones} contraseñas a Bcrypt con factor 11");
                }
            }
            
            $this->newLine();
            $this->info('ℹ️  Las contraseñas hasheadas (MD5/SHA1) se migrarán automáticamente en el próximo login de cada usuario.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la migración: ' . $e->getMessage());
            Log::error('Error en migración de contraseñas: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Detectar algoritmo de hash usado
     */
    private function detectarAlgoritmo($hash)
    {
        if (substr($hash, 0, 3) === '$2a' || substr($hash, 0, 3) === '$2y' || substr($hash, 0, 3) === '$2x') {
            return 'Bcrypt';
        }
        
        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            return 'MD5';
        }
        
        if (strlen($hash) === 40 && ctype_xdigit($hash)) {
            return 'SHA1';
        }
        
        if (strlen($hash) === 64 && ctype_xdigit($hash)) {
            return 'SHA256';
        }
        
        return 'Texto plano';
    }
}
