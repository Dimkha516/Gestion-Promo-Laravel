<?php

namespace App\Services\Database;

interface DatabaseServiceInterface
{
    public function createUser(array $data);
    public function listUsers();
    // Ajouter d'autres méthodes que vous utiliserez pour les opérations CRUD.
}
