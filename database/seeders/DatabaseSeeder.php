<?php

namespace Database\Seeders;

use App\Models\Auth\Usuario;
use App\Models\Auth\Perfil;
use App\Models\Empresa;
use App\Models\Empresas\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\ObjectId;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeding completo de GIR-365...');

        // Create basic test data
        $this->seedBasicData();

        // Create platform configuration
        $this->call(ConfiguracionPlataformaSeeder::class);

        $this->command->info('✅ Seeding completo de GIR-365 terminado exitosamente!');
    }

    /**
     * Seed basic test data
     */
    private function seedBasicData(): void
    {
        $this->command->info('📝 Creando datos básicos de prueba...');

        // Create a test company
        // NOTA: Datos de prueba para desarrollo inicial
        // Para producción, crear empresas y usuarios a través del panel de administración
        /*
        try {
            $empresa = Empresa::create([
                'nombre' => 'Empresa de Prueba GIR-365',
                'nit' => '900123456-1',
                'direccion' => 'Carrera 15 #93-47, Bogotá',
                'telefono' => '+57 1 234 5678',
                'email' => 'prueba@gir365.com',
                'representante_legal' => 'Juan Pérez',
                'sector_economico' => 'Tecnología',
                'estado' => true,
                '_esBorrado' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->info('   ✓ Empresa de prueba creada');
        } catch (\Exception $e) {
            $this->command->info('   ⚠ Error creando empresa: ' . $e->getMessage());
        }

        // Create a test profile
        try {
            $perfil = Perfil::create([
                'nombre' => 'Administrador',
                'descripcion' => 'Perfil de administrador básico',
                'nivel' => 5,
                'modulos' => ['dashboard', 'empresas', 'usuarios'],
                'permisos' => ['all'],
                'estado' => true,
                '_esBorrado' => false,
                'es_admin' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->info('   ✓ Perfil de prueba creado');
        } catch (\Exception $e) {
            $this->command->info('   ⚠ Error creando perfil: ' . $e->getMessage());
        }

        // Create a test user (only if we have the necessary data)
        try {
            $empresaTest = Empresa::where('nit', '900123456-1')->where('estado', true)->where('_esBorrado', false)->first();
            $perfilTest = Perfil::where('nombre', 'Administrador')->where('estado', true)->where('_esBorrado', false)->first();

            if ($empresaTest && $perfilTest) {
                $usuario = Usuario::create([
                    'nombre' => 'Administrador del Sistema',
                    'email' => 'admin@gir365.com',
                    'password' => Hash::make('admin123'),
                    'empresa_id' => new ObjectId($empresaTest->_id),
                    'perfil_id' => new ObjectId($perfilTest->_id),
                    'estado' => true,
                    '_esBorrado' => false,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->command->info('   ✓ Usuario administrador creado (admin@gir365.com / admin123)');
            }
        */
        } catch (\Exception $e) {
            $this->command->info('   ⚠ Error creando usuario: ' . $e->getMessage());
        }

        $this->command->info('   ✓ Datos básicos creados correctamente');
    }
}
