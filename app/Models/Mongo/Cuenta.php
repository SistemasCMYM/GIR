<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Cuenta extends Model
{
    protected $connection = 'mongodb_cuentas'; // or 'mongodb' if default
    protected $collection = 'cuentas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'centro_key', 'canales', 'empresas', 'tipo', 'estado', 'rol', 'dni',
        'contrasena', 'email', 'nick', 'empleado_id', '_tags', '_slug', '_esBorrado',
        '_esPublico', '_fechaModificado', '_fechaBorrado', '_fechaCreado'
    ];

    // Relación con empresas
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'id', 'empresas');
    }

    public function getPerfil()
    {
        switch ($this->rol) {
            case 'superadmin':
                return 'Super Administrador';
            case 'admin':
                return 'Administrador empresa cliente';
            case 'psicologo':
                return 'Psicólogo/a o Profesional Psicosocial';
            case 'tecnico':
                return 'Técnico/a de Hallazgos';
            case 'supervisor':
                return 'Supervisor/a de Casos';
            case 'usuario':
                return 'Usuario Final';
            default:
                return 'Desconocido';
        }
    }
}
