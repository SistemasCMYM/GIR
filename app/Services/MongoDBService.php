<?php

namespace App\Services;

use MongoDB\Client;

class MongoDBService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client('mongodb://127.0.0.1:27017');
    }

    public function getDatabase($dbName)
    {
        return $this->client->selectDatabase($dbName);
    }

    public function getClient()
    {
        return $this->client;
    }
}
