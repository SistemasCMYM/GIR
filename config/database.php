<?php

use Illuminate\Support\Str;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'mongodb_empresas'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [
        // Database: empresas - Collections: areas, centros, ciudades, contratos, departamentos, empleados, empresas, grupos, procesos, sectores, usuarios
        'mongodb_empresas' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_EMPRESAS_HOST', '127.0.0.1'),
            'port'     => env('MONGO_EMPRESAS_PORT', 27017),
            'database' => env('MONGO_EMPRESAS_DATABASE', 'empresas'),
            'username' => env('MONGO_EMPRESAS_USERNAME', ''),
            'password' => env('MONGO_EMPRESAS_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_EMPRESAS_AUTHDB', 'admin'),
                'ssl' => env('MONGO_EMPRESAS_SSL', false),
                'tls' => env('MONGO_EMPRESAS_TLS', false),
                'tlsInsecure' => env('MONGO_EMPRESAS_TLS_INSECURE', false),
                'serverSelectionTimeoutMS' => 30000,
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'maxPoolSize' => 100,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-Empresas',
            ],
        ],

        // Database: cmym - Collections: cuentas, notificaciones, perfiles, permisos, roles, sesiones
        'mongodb_cmym' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_CMYM_HOST', '127.0.0.1'),
            'port'     => env('MONGO_CMYM_PORT', 27017),
            'database' => env('MONGO_CMYM_DATABASE', 'cmym'),
            'username' => env('MONGO_CMYM_USERNAME', ''),
            'password' => env('MONGO_CMYM_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_CMYM_AUTHDB', 'admin'),
                'ssl' => env('MONGO_CMYM_SSL', false),
                'tls' => env('MONGO_CMYM_TLS', false),
                'tlsInsecure' => env('MONGO_CMYM_TLS_INSECURE', false),
                'serverSelectionTimeoutMS' => 30000,
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'maxPoolSize' => 100,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-CMYM',
            ],
        ],

        // Database: hallazgos - Collections: reportes, variables
        'mongodb_hallazgos' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_HALLAZGOS_HOST', '127.0.0.1'),
            'port'     => env('MONGO_HALLAZGOS_PORT', 27017),
            'database' => env('MONGO_HALLAZGOS_DATABASE', 'hallazgos'),
            'username' => env('MONGO_HALLAZGOS_USERNAME', ''),
            'password' => env('MONGO_HALLAZGOS_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_HALLAZGOS_AUTHDB', 'admin'),
                'ssl' => env('MONGO_HALLAZGOS_SSL', false),
                'tls' => env('MONGO_HALLAZGOS_TLS', false),
                'tlsInsecure' => env('MONGO_HALLAZGOS_TLS_INSECURE', false),
                'serverSelectionTimeoutMS' => 30000,
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'maxPoolSize' => 100,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-Hallazgos',
            ],
        ],

        // Database: planes - Collections: planes, tareas
        'mongodb_planes' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_PLANES_HOST', '127.0.0.1'),
            'port'     => env('MONGO_PLANES_PORT', 27017),
            'database' => env('MONGO_PLANES_DATABASE', 'planes'),
            'username' => env('MONGO_PLANES_USERNAME', ''),
            'password' => env('MONGO_PLANES_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_PLANES_AUTHDB', 'admin'),
                'ssl' => env('MONGO_PLANES_SSL', false),
                'tls' => env('MONGO_PLANES_TLS', false),
                'tlsInsecure' => env('MONGO_PLANES_TLS_INSECURE', false),
                'serverSelectionTimeoutMS' => 30000,
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'maxPoolSize' => 100,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-Planes',
            ],
        ],

        // Database: psicosocial - Collections: actividades, datos, diagnosticos, hojas, intervenciones, preguntas, respuestas
        'mongodb_psicosocial' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_PSICOSOCIAL_HOST', '127.0.0.1'),
            'port'     => env('MONGO_PSICOSOCIAL_PORT', 27017),
            'database' => env('MONGO_PSICOSOCIAL_DATABASE', 'psicosocial'),
            'username' => env('MONGO_PSICOSOCIAL_USERNAME', ''),
            'password' => env('MONGO_PSICOSOCIAL_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_PSICOSOCIAL_AUTHDB', 'admin'),
                'ssl' => env('MONGO_PSICOSOCIAL_SSL', false),
                'tls' => env('MONGO_PSICOSOCIAL_TLS', false),
                'tlsInsecure' => env('MONGO_PSICOSOCIAL_TLS_INSECURE', false),
                'serverSelectionTimeoutMS' => 30000,
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'maxPoolSize' => 100,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-Psicosocial',
            ],
        ],

        // Default MongoDB connection for general purposes
        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_HOST', '127.0.0.1'),
            'port'     => env('MONGO_PORT', 27017),
            'database' => env('MONGO_DATABASE', 'gir365'),
            'username' => env('MONGO_USERNAME', ''),
            'password' => env('MONGO_PASSWORD', ''),
            'options'  => [
                'database' => env('MONGO_AUTHDB', 'admin'),
                'connectTimeoutMS' => 3000,
                'socketTimeoutMS' => 30000,
                'serverSelectionTimeoutMS' => 3000,
                'maxPoolSize' => 20,
                'minPoolSize' => 5,
                'maxIdleTimeMS' => 900000,
                'appName' => 'GIR365-Default',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
