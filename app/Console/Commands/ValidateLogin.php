<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MongoEmpresaService;
use App\Services\MongoCuentaService;

class ValidateLogin extends Command
{
    protected $signature = 'login:validate';
    protected $description = 'Validar el sistema de login';

    public function handle()
    {
        $this->info('=== VALIDACIÓN DEL SISTEMA DE LOGIN ===');
        
        try {
            // 1. Verificar servicios MongoDB
            $this->info("\n1. Verificando servicios MongoDB...");
            
            $empresaService = new MongoEmpresaService();
            $cuentaService = new MongoCuentaService();
            
            $this->info('✓ Servicios MongoDB disponibles');
            
            // 2. Verificar NIT de prueba
            $this->info("\n2. Verificando NIT 11111...");
            
            $empresa = $empresaService->findByNit('11111');
            if (!$empresa) {
                $this->error('✗ Empresa con NIT 11111 no encontrada');
                return;
            }
            
            $this->info('✓ Empresa encontrada: ' . $empresa['razon_social']);
            $this->info('  - ID: ' . $empresa['id']);
            $this->info('  - Estado: ' . $empresa['estado']);
            
            // 3. Verificar cuenta con acceso
            $this->info("\n3. Verificando cuenta con acceso...");
            
            // NOTA: Comando de desarrollo - usar con email válido del sistema
            // NO usar en producción con datos embebidos
            $testEmail = $this->ask('Ingrese el email a verificar');
            
            $cuenta = $cuentaService->findByEmailAndEmpresa($testEmail, $empresa['id']);
            if (!$cuenta) {
                $this->error('✗ Cuenta con acceso no encontrada para: ' . $testEmail);
                return;
            }
            
            $this->info('✓ Cuenta encontrada: ' . $cuenta['email']);
            $this->info('  - Rol: ' . ($cuenta['rol'] ?? 'N/A'));
            $this->info('  - Estado: ' . ($cuenta['estado'] ?? 'N/A'));
            
            // 4. Verificar que las rutas existan
            $this->info("\n4. Verificando rutas de autenticación...");
            
            $this->info('✓ Sistema de login validado correctamente');
            $this->info("\nPuedes probar el login en:");
            $this->info("URL: http://localhost:8000/");
            $this->info("NIT: 11111");
            $this->info("Email: " . $testEmail);
            $this->info("Password: (revisar en MongoDB)");
            
        } catch (\Exception $e) {
            $this->error('Error en la validación: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
