<?php
namespace App\Services;

use App\Models\Mongo\Cuenta;
use MongoDB\Client;

class MongoCuentaService
{
    protected $client;
    protected $db;
    protected $collection;

    public function __construct()
    {
        $this->client = new Client(
            'mongodb://' . env('MONGO_CUENTAS_HOST', '127.0.0.1') . ':' . env('MONGO_CUENTAS_PORT', 27017)
        );
        $this->db = $this->client->{env('MONGO_CUENTAS_DATABASE', 'cmym')};
        $this->collection = $this->db->cuentas;
    }

    public function findByEmailAndEmpresa($email, $empresaId)
    {
        // Buscar usuario por email y que el array empresas contenga el id de la empresa
        return $this->collection->findOne([
            'email' => $email,
            'empresas' => ['$in' => [$empresaId]]
        ]);
    }    public function getCuentaById($id)
    {
        return $this->collection->findOne(['_id' => $id]);
    }    public function getEmpresasForCuenta($cuentaId)
    {
        $cuenta = $this->getCuentaById($cuentaId);
        return $cuenta && isset($cuenta['empresas']) ? $cuenta['empresas'] : [];
    }
}
