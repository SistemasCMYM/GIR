<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

class ConfiguracionPlataformaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeding de configuración de la plataforma GIR-365...');
        
        $this->seedConfiguracionAdmin();
        
        $this->command->info('✅ Seeding de configuración de plataforma completado exitosamente.');
    }

    /**
     * Crear configuración específica de la plataforma en la base de datos admin
     */
    private function seedConfiguracionAdmin(): void
    {
        $this->command->info('📊 Creando configuración de la plataforma en base de datos admin...');

        // Conectar directamente a la base de datos admin usando MongoDB Client
        $mongoUri = config('database.connections.mongodb.dsn') ?: 
                   'mongodb://' . config('database.connections.mongodb.host') . ':' . config('database.connections.mongodb.port');
        
        $client = new Client($mongoUri);
        $adminDb = $client->selectDatabase('admin');

        // Crear la configuración de la plataforma
        $this->createConfiguracionPlataforma($adminDb);
        $this->createModulosPlataforma($adminDb);
        $this->createConfiguracionSistema($adminDb);

        $this->command->info('   ✅ Configuración de admin completada');
    }

    /**
     * Crear configuración principal de la plataforma
     */
    private function createConfiguracionPlataforma($adminDb): void
    {
        $this->command->info('   📝 Creando configuración principal de la plataforma...');

        $collection = $adminDb->selectCollection('Configuracion');

        // Verificar si ya existe
        $existente = $collection->findOne(['tipo' => 'plataforma']);
        if ($existente) {
            $this->command->info('   ⚠ Configuración de plataforma ya existe, omitiendo...');
            return;
        }

        $configuracionPlataforma = [
            '_id' => $this->generateCustomId(),
            'tipo' => 'plataforma',
            'nombre_plataforma' => 'GIR-365',
            'descripcion_completa' => 'Sistema de Gestión Integral de Riesgos - Plataforma completa para el manejo de riesgos laborales, psicosociales y de salud ocupacional',
            'version' => '1.0.0',
            'empresa_desarrolladora' => 'DevTeam Solutions',
            'contacto_soporte' => [
                'email' => env('SUPPORT_EMAIL', 'soporte@tudominio.com'),
                'telefono' => '+57 300 123 4567',
                'horario_atencion' => '8:00 AM - 6:00 PM'
            ],
            'configuracion_visual' => [
                'logo_principal' => 'assets/img/gir365_logo.png',
                'logo_secundario' => 'assets/img/gir365_logo_small.png',
                'favicon' => 'assets/img/favicon.ico',
                'colores_corporativos' => [
                    'primario' => '#2c3e50',
                    'secundario' => '#3498db',
                    'acento' => '#e74c3c',
                    'success' => '#27ae60',
                    'warning' => '#f39c12',
                    'danger' => '#e74c3c'
                ],
                'tema_por_defecto' => 'claro'
            ],
            'configuracion_legal' => [
                'terminos_condiciones' => 'Términos y condiciones de uso de la plataforma GIR-365',
                'politica_privacidad' => 'Política de privacidad y tratamiento de datos personales',
                'licencia' => 'Licencia comercial GIR-365 v2.0'
            ],
            'estado' => 'activo',
            'fecha_creacion' => new UTCDateTime(),
            'fecha_actualizacion' => new UTCDateTime(),
            'creado_por' => 'sistema'
        ];

        $collection->insertOne($configuracionPlataforma);
        $this->command->info('   ✓ Configuración principal de plataforma creada');
    }

    /**
     * Crear configuración de módulos de la plataforma
     */
    private function createModulosPlataforma($adminDb): void
    {
        $this->command->info('   📝 Creando configuración de módulos...');

        $collection = $adminDb->selectCollection('Configuracion');

        // Verificar si ya existe
        $existente = $collection->findOne(['tipo' => 'modulos']);
        if ($existente) {
            $this->command->info('   ⚠ Configuración de módulos ya existe, omitiendo...');
            return;
        }

        $modulosConfig = [
            '_id' => $this->generateCustomId(),
            'tipo' => 'modulos',
            'modulos_disponibles' => [
                'dashboard' => [
                    'nombre' => 'Dashboard Principal',
                    'descripcion' => 'Panel principal con métricas y resúmenes',
                    'icono' => 'fa-dashboard',
                    'activo' => true,
                    'orden' => 1
                ],
                'empresas' => [
                    'nombre' => 'Gestión de Empresas',
                    'descripcion' => 'Administración de empresas cliente',
                    'icono' => 'fa-building',
                    'activo' => true,
                    'orden' => 2
                ],
                'empleados' => [
                    'nombre' => 'Gestión de Empleados',
                    'descripcion' => 'Administración de empleados por empresa',
                    'icono' => 'fa-users',
                    'activo' => true,
                    'orden' => 3
                ],
                'evaluaciones' => [
                    'nombre' => 'Evaluaciones Psicosociales',
                    'descripcion' => 'Gestión de evaluaciones de riesgo psicosocial',
                    'icono' => 'fa-clipboard-check',
                    'activo' => true,
                    'orden' => 4
                ],
                'planes' => [
                    'nombre' => 'Planes de Acción',
                    'descripcion' => 'Creación y seguimiento de planes de mejora',
                    'icono' => 'fa-tasks',
                    'activo' => true,
                    'orden' => 5
                ],
                'hallazgos' => [
                    'nombre' => 'Gestión de Hallazgos',
                    'descripcion' => 'Registro y seguimiento de hallazgos',
                    'icono' => 'fa-exclamation-triangle',
                    'activo' => true,
                    'orden' => 6
                ],
                'reportes' => [
                    'nombre' => 'Reportes y Análisis',
                    'descripcion' => 'Generación de reportes e informes',
                    'icono' => 'fa-chart-bar',
                    'activo' => true,
                    'orden' => 7
                ],
                'notificaciones' => [
                    'nombre' => 'Sistema de Notificaciones',
                    'descripcion' => 'Gestión de alertas y notificaciones',
                    'icono' => 'fa-bell',
                    'activo' => true,
                    'orden' => 8
                ],
                'configuracion' => [
                    'nombre' => 'Configuración del Sistema',
                    'descripcion' => 'Configuraciones generales y de usuario',
                    'icono' => 'fa-cogs',
                    'activo' => true,
                    'orden' => 9
                ]
            ],
            'configuracion_general' => [
                'modulos_requeridos' => ['dashboard', 'empresas', 'empleados'],
                'modulos_opcionales' => ['evaluaciones', 'planes', 'hallazgos', 'reportes', 'notificaciones'],
                'limite_modulos_simultaneos' => 5,
                'cache_modulos' => true
            ],
            'estado' => 'activo',
            'fecha_creacion' => new UTCDateTime(),
            'fecha_actualizacion' => new UTCDateTime(),
            'creado_por' => 'sistema'
        ];

        $collection->insertOne($modulosConfig);
        $this->command->info('   ✓ Configuración de módulos creada');
    }

    /**
     * Crear configuración básica del sistema
     */
    private function createConfiguracionSistema($adminDb): void
    {
        $this->command->info('   📝 Creando configuración básica del sistema...');

        $collection = $adminDb->selectCollection('Configuracion');

        // Verificar si ya existe
        $existente = $collection->findOne(['tipo' => 'sistema']);
        if ($existente) {
            $this->command->info('   ⚠ Configuración del sistema ya existe, omitiendo...');
            return;
        }

        $configuracionSistema = [
            '_id' => $this->generateCustomId(),
            'tipo' => 'sistema',
            'parametros_sistema' => [
                'timezone' => 'America/Bogota',
                'idioma_por_defecto' => 'es',
                'formato_fecha' => 'd/m/Y',
                'formato_hora' => 'H:i:s',
                'moneda' => 'COP',
                'simbolo_moneda' => '$'
            ],
            'limites_sistema' => [
                'max_empresas_por_cuenta' => 100,
                'max_empleados_por_empresa' => 10000,
                'max_evaluaciones_simultaneas' => 50,
                'max_archivos_por_upload' => 10,
                'max_tamaño_archivo_mb' => 50
            ],
            'configuracion_seguridad' => [
                'session_timeout_minutos' => 120,
                'max_intentos_login' => 5,
                'bloqueo_temporal_minutos' => 30,
                'forzar_cambio_password_dias' => 90,
                'longitud_minima_password' => 8,
                'requerir_2fa' => false
            ],
            'configuracion_cache' => [
                'cache_activo' => true,
                'tiempo_cache_consultas_minutos' => 30,
                'tiempo_cache_reportes_horas' => 2,
                'limpiar_cache_automatico' => true
            ],
            'configuracion_backup' => [
                'backup_automatico' => true,
                'frecuencia_backup_horas' => 24,
                'mantener_backups_dias' => 30,
                'incluir_archivos_upload' => true
            ],
            'estado' => 'activo',
            'fecha_creacion' => new UTCDateTime(),
            'fecha_actualizacion' => new UTCDateTime(),
            'creado_por' => 'sistema'
        ];

        $collection->insertOne($configuracionSistema);
        $this->command->info('   ✓ Configuración del sistema creada');
    }

    /**
     * Generar ID personalizado
     */
    private function generateCustomId(): string
    {
        // Usar la función helper si existe
        if (function_exists('generateBase64UrlId')) {
            return generateBase64UrlId();
        }
        
        // Fallback: generar ID simple base64
        return rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
    }
}
