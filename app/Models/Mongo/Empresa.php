<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'mongodb_cuentas';
    protected $collection = 'empresas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}
