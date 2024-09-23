<?php

namespace App\Services\Database;

use Illuminate\Support\Facades\App;

class DatabaseServiceManager
{
    protected $service;

    public function __construct()
    {
        $this->service = config('database.default_service'); // Récupère la clé de service en cours.
    }

    public function getService(): DatabaseServiceInterface
    {
        if ($this->service === 'firebase') {
            return App::make(FirebaseService::class);
        } elseif ($this->service === 'mysql') {
            return App::make(MysqlService::class);
        }

        throw new \Exception('Service de base de données non valide');
    }
}
