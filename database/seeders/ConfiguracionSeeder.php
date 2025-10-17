<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion\Configuracion;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuraciones base para el sistema (sin empresa específica)
        $configuracionesBase = [
            // Configuraciones de Empresa
            [
                'modulo' => 'empresa',
                'componente' => 'informacion_general',
                'clave' => 'empresa_formato_nit',
                'valor' => ['formato' => 'NIT: {nit}-{dv}'],
                'tipo_dato' => 'object',
                'descripcion' => 'Formato de visualización del NIT de la empresa',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 1
            ],
            [
                'modulo' => 'empresa',
                'componente' => 'branding',
                'clave' => 'empresa_colores_tema',
                'valor' => ['primario' => '#007bff', 'secundario' => '#6c757d'],
                'tipo_dato' => 'object',
                'descripcion' => 'Colores del tema de la empresa',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 2
            ],

            // Configuraciones de Estructura
            [
                'modulo' => 'estructura',
                'componente' => 'jerarquia',
                'clave' => 'estructura_max_niveles',
                'valor' => [5],
                'tipo_dato' => 'integer',
                'descripcion' => 'Máximo número de niveles jerárquicos permitidos',
                'activo' => true,
                'aplicar_a_modulos' => ['estructura', 'hallazgos', 'psicosocial'],
                'orden' => 1
            ],

            // Configuraciones de Fecha y Hora
            [
                'modulo' => 'fechahora',
                'componente' => 'timezone',
                'clave' => 'sistema_zona_horaria',
                'valor' => ['America/Bogota'],
                'tipo_dato' => 'string',
                'descripcion' => 'Zona horaria por defecto del sistema',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 1
            ],
            [
                'modulo' => 'fechahora',
                'componente' => 'formato',
                'clave' => 'formato_fecha',
                'valor' => ['d/m/Y'],
                'tipo_dato' => 'string',
                'descripcion' => 'Formato de fecha por defecto',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 2
            ],
            [
                'modulo' => 'fechahora',
                'componente' => 'formato',
                'clave' => 'formato_hora',
                'valor' => ['H:i:s'],
                'tipo_dato' => 'string',
                'descripcion' => 'Formato de hora por defecto',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 3
            ],

            // Configuraciones de Reportes
            [
                'modulo' => 'reportes',
                'componente' => 'general',
                'clave' => 'reportes_logo_watermark',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Mostrar logo como marca de agua en reportes',
                'activo' => true,
                'aplicar_a_modulos' => ['reportes', 'hallazgos', 'psicosocial'],
                'orden' => 1
            ],
            [
                'modulo' => 'reportes',
                'componente' => 'exportacion',
                'clave' => 'reportes_formatos_disponibles',
                'valor' => ['pdf', 'excel', 'word'],
                'tipo_dato' => 'array',
                'descripcion' => 'Formatos de exportación disponibles para reportes',
                'activo' => true,
                'aplicar_a_modulos' => ['reportes', 'hallazgos', 'psicosocial'],
                'orden' => 2
            ],

            // Configuraciones de Seguridad
            [
                'modulo' => 'seguridad',
                'componente' => 'sesiones',
                'clave' => 'seguridad_tiempo_sesion',
                'valor' => [480], // 8 horas en minutos
                'tipo_dato' => 'integer',
                'descripcion' => 'Tiempo de duración de sesión en minutos',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 1
            ],
            [
                'modulo' => 'seguridad',
                'componente' => 'passwords',
                'clave' => 'seguridad_longitud_password',
                'valor' => [8],
                'tipo_dato' => 'integer',
                'descripcion' => 'Longitud mínima de contraseñas',
                'activo' => true,
                'aplicar_a_modulos' => ['autenticacion'],
                'orden' => 2
            ],
            [
                'modulo' => 'seguridad',
                'componente' => 'auditoria',
                'clave' => 'seguridad_log_actividades',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Registrar actividades de usuarios en logs',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 3
            ],

            // Configuraciones de Notificaciones
            [
                'modulo' => 'notificaciones',
                'componente' => 'email',
                'clave' => 'notificaciones_email_habilitado',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar notificaciones por email',
                'activo' => true,
                'aplicar_a_modulos' => ['notificaciones', 'hallazgos', 'psicosocial'],
                'orden' => 1
            ],
            [
                'modulo' => 'notificaciones',
                'componente' => 'sistema',
                'clave' => 'notificaciones_sistema_habilitado',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar notificaciones del sistema',
                'activo' => true,
                'aplicar_a_modulos' => ['all'],
                'orden' => 2
            ],

            // Configuraciones de Integraciones
            [
                'modulo' => 'integraciones',
                'componente' => 'api',
                'clave' => 'integraciones_api_habilitada',
                'valor' => [false],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar API para integraciones externas',
                'activo' => true,
                'aplicar_a_modulos' => ['integraciones'],
                'orden' => 1
            ],

            // Configuraciones de Autenticación
            [
                'modulo' => 'autenticacion',
                'componente' => 'metodos',
                'clave' => 'auth_metodos_disponibles',
                'valor' => ['local', 'ldap'],
                'tipo_dato' => 'array',
                'descripcion' => 'Métodos de autenticación disponibles',
                'activo' => true,
                'aplicar_a_modulos' => ['autenticacion'],
                'orden' => 1
            ],

            // Configuraciones de Procesos
            [
                'modulo' => 'procesos',
                'componente' => 'automatizacion',
                'clave' => 'procesos_automaticos_habilitados',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Habilitar procesos automáticos del sistema',
                'activo' => true,
                'aplicar_a_modulos' => ['procesos', 'hallazgos', 'psicosocial'],
                'orden' => 1
            ],

            // Configuraciones específicas para Hallazgos
            [
                'modulo' => 'reportes',
                'componente' => 'hallazgos',
                'clave' => 'hallazgos_estados_disponibles',
                'valor' => ['abierto', 'en_proceso', 'cerrado', 'cancelado'],
                'tipo_dato' => 'array',
                'descripcion' => 'Estados disponibles para hallazgos',
                'activo' => true,
                'aplicar_a_modulos' => ['hallazgos'],
                'orden' => 1
            ],
            [
                'modulo' => 'seguridad',
                'componente' => 'hallazgos',
                'clave' => 'hallazgos_tipos_disponibles',
                'valor' => ['incidente', 'accidente', 'condicion_insegura', 'acto_inseguro'],
                'tipo_dato' => 'array',
                'descripcion' => 'Tipos de hallazgos disponibles',
                'activo' => true,
                'aplicar_a_modulos' => ['hallazgos'],
                'orden' => 2
            ],

            // Configuraciones específicas para Psicosocial
            [
                'modulo' => 'procesos',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_estados_evaluacion',
                'valor' => ['iniciada', 'en_progreso', 'completada', 'validada'],
                'tipo_dato' => 'array',
                'descripcion' => 'Estados disponibles para evaluaciones psicosociales',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 1
            ],
            [
                'modulo' => 'seguridad',
                'componente' => 'psicosocial',
                'clave' => 'psicosocial_confidencialidad',
                'valor' => [true],
                'tipo_dato' => 'boolean',
                'descripcion' => 'Aplicar políticas de confidencialidad a evaluaciones psicosociales',
                'activo' => true,
                'aplicar_a_modulos' => ['psicosocial'],
                'orden' => 2
            ]
        ];

        // Crear configuraciones base (sin empresa_id para que sean templates)
        foreach ($configuracionesBase as $config) {
            Configuracion::updateOrCreate(
                [
                    'clave' => $config['clave'],
                    'empresa_id' => null // Configuraciones template
                ],
                $config
            );
        }

        $this->command->info('Configuraciones base creadas exitosamente.');
    }
}
