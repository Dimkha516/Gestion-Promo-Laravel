<?php

namespace App\Services\Database;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\User2;
use Illuminate\Support\Facades\DB;

class MysqlService implements DatabaseServiceInterface
{
    public function createUser(array $data)
    {
        // Hacher le mot de passe avant de l'enregistrer
        $data['password'] = Hash::make($data['password']);


        // Créer l'utilisateur
        $user = User2::create($data);

        return $user;
    }

    public function listUsers()
    {
        return User2::all(); // Récupérer tous les utilisateurs.
    }


    public function getUserById($id){
        $user = User2::findOrFail($id);
        return $user;
    }

    public function updateUser($userId, $data)
    {
        $user = User2::findOrFail($userId);
        $user->update($data);  // Met à jour les informations utilisateur

        return $user;
    }
}
