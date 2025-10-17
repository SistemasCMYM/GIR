<?php
namespace App\Services;

use MongoDB\Client;

class MongoEmpresaService
{
    protected $client;
    protected $db;
    protected $collection;

    public function __construct()
    {
        $this->client = new Client(
            'mongodb://' . env('MONGO_EMPRESAS_HOST', '127.0.0.1') . ':' . env('MONGO_EMPRESAS_PORT', 27017)
        );
        $this->db = $this->client->{env('MONGO_EMPRESAS_DATABASE', 'empresas')};
        $this->collection = $this->db->empresas;
    }

    public function findByNit($nit)
    {
        return $this->collection->findOne(['nit' => $nit]);
    }
}
