<?php

namespace App\Repositories;

use App\Services\Database\DatabaseServiceManager;

class UserRepository
{
    protected $databaseServiceManager;
    protected $firebaseService;
    protected $mysqlService;


    // public function __construct(DatabaseServiceManager $databaseServiceManager)
    // {
    //     $this->databaseServiceManager = $databaseServiceManager;
    // }

    public function __construct(DatabaseServiceManager $databaseServiceManager)
    {
        $this->databaseServiceManager = $databaseServiceManager;
    }

    public function createUser(array $data)
    {
        return $this->databaseServiceManager->getService()->createUser($data);
    }

    public function listUsers()
    {
        return $this->databaseServiceManager->getService()->listUsers();
    }

    public function updateUser($id, array $data)
    {
        // return $this->databaseServiceManager->getService()->updateUser($id, $data);
    }
    
    public function deleteUser($id)
    {
        // return $this->databaseServiceManager->getService()->deleteUser($id);
    }

    public function findUserById($id)
    {
        // return $this->databaseServiceManager->getService()->findUserById($id);
    }
}
