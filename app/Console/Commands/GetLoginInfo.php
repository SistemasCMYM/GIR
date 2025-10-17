<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MongoCuentaService;
use MongoDB\Client;

class GetLoginInfo extends Command
{
    protected $signature = 'login:info';
    protected $description = 'Obtener información de login para las cuentas disponibles';

    public function handle()
    {
        $this->info('=== INFORMACIÓN DE LOGIN DISPONIBLE ===');
        
        try {
            // Conectar directamente a MongoDB
            $client = new Client('mongodb://127.0.0.1:27017');
            $db = $client->cmym;
            $cuentasCollection = $db->cuentas;
            
            // Obtener todas las cuentas activas
            $cuentas = $cuentasCollection->find(['estado' => 'activa'])->toArray();
            
            $this->info("\nCuentas disponibles para login:");
            $this->info("==============================");
            
            foreach ($cuentas as $cuenta) {
                $this->info("\n📧 Email: " . $cuenta['email']);
                $this->info("👤 Rol: " . ($cuenta['rol'] ?? 'N/A'));
                $this->info("🏢 Empresas: " . json_encode($cuenta['empresas'] ?? []));
                $this->info("🔑 Hash Password: " . substr($cuenta['contrasena'] ?? 'N/A', 0, 20) . "...");
                
                // Intentar algunas contraseñas comunes
                $passwordsToTry = ['admin123', 'password', '123456', 'admin', 'test123'];
                
                foreach ($passwordsToTry as $password) {
                    if (isset($cuenta['contrasena'])) {
                        // Probar con hash SHA256
                        if ($cuenta['contrasena'] === hash('sha256', $password)) {
                            $this->info("✅ Password encontrada: " . $password);
                            break;
                        }
                        // Probar con password_verify
                        if (password_verify($password, $cuenta['contrasena'])) {
                            $this->info("✅ Password encontrada: " . $password);
                            break;
                        }
                    }
                }
                $this->info("─────────────────────────────────────");
            }
            
            $this->info("\n🔗 URL de Login: http://localhost:8000/");
            $this->info("📝 Pasos:");
            $this->info("   1. Ingresa NIT: 11111");
            $this->info("   2. Ingresa email y password de arriba");
            
        } catch (\Exception $e) {
            $this->error('Error obteniendo información: ' . $e->getMessage());
        }
    }
}
