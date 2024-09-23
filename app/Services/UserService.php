<?php

namespace App\Services;

use App\Models\User2;
use App\Repositories\UserRepository;
use App\Services\Database\FirebaseService;
use App\Services\Database\MysqlService;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $firebaseService;
    protected $mysqlService;

    public function __construct(
        FirebaseService $firebaseService,
        MysqlService $mysqlService
    ) {
        $this->firebaseService = $firebaseService;
        $this->mysqlService = $mysqlService;
    }

    public function createUser(array $data)
    {
        // Crypter le mot de passe
        // $data['password'] = Hash::make($data['password']);

        // Gestion de la photo par défaut
        if (empty($data['photo'])) {
            $data['photo'] = url('https://res.cloudinary.com/dytchfsin/image/upload/v1725465088/xcb8pgm42qc6vkzgwnvd.png'); // URL de la photo par défaut
        }

        // // Ajout dans Firebase
        // $this->firebaseService->createUser($data);
        // Ajout dans Firebase et récupérer l'utilisateur créé
        $firebaseUser = $this->firebaseService->createUser($data);


        // Ajout dans la base MySQL
        // $user = $this->mysqlService->createUser($data);

        // Ajouter l'UID de Firebase aux données
        $data['firebase_uid'] = $firebaseUser->uid;


        // Ajout dans la base MySQL
        return $this->mysqlService->createUser($data);
    }

    public function listUsers($role = null)
    {
        // Vérifie si un rôle est fourni pour filtrer les utilisateurs par ce rôle
        if ($role) {
            return User2::role($role)->get();  // Utilisation du scope 'role'
        }

        // return $this->firebaseService->listUsers();
        return User2::all();
    }

    public function updateUser(int $id, array $data)
    {
        // Gestion de la photo si elle n'est pas fournie
        if (empty($data['photo'])) {
            $data['photo'] = url('https://res.cloudinary.com/dytchfsin/image/upload/v1725465088/xcb8pgm42qc6vkzgwnvd.png'); // URL de la photo par défaut
        }

        // Récupérer l'utilisateur local
        $user = $this->mysqlService->getUserById($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Récupérer l'UID de Firebase
        $firebaseUid = $user->firebase_uid; // Assurez-vous que ce champ existe


        // Mettre à jour l'utilisateur dans MySQL
        $this->mysqlService->updateUser($id, $data);

        // Mettre à jour l'utilisateur dans Firebase
        $this->firebaseService->updateUser($firebaseUid, $data);

        return response()->json(['message' => 'User updated successfully'], 200);
    }



    // public function updateUser($id, array $data)
    // {
    //     return $this->userRepository->updateUser($id, $data);
    // }

    // public function deleteUser($id)
    // {
    //     return $this->userRepository->deleteUser($id);
    // }

}
