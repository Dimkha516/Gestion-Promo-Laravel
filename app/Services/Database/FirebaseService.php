<?php

namespace App\Services\Database;

use Kreait\Firebase\Factory;

class FirebaseService implements DatabaseServiceInterface
{
    protected $database;
    protected $auth;


    public function __construct()   
    {   
        $encodedCredentials = config('services.firebase.credentials');
        $decodedCredentials = base64_decode($encodedCredentials);

        if (!$decodedCredentials) {
            throw new \Exception("Failed to decode Firebase credentials");
        }

        // Nettoyage du JSON décodé
        $decodedCredentials = trim($decodedCredentials);

        $credentialsArray = json_decode($decodedCredentials, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse Firebase credentials JSON: " . json_last_error_msg());
        }
        $firebase = (new Factory)
            // ->withServiceAccount(config('services.firebase.credentials'))
            ->withServiceAccount($credentialsArray)
            ->withDatabaseUri(config('services.firebase.database_url'));
        $this->auth = $firebase->createAuth();  // Utilisation de l'authentification Firebase
        $this->database = $firebase->createDatabase();
    }

    public function createUser(array $data)
    {
        try {
            // Création de l'utilisateur avec Firebase Auth
            $user = $this->auth->createUserWithEmailAndPassword($data['email'], $data['password']);
            if ($user) {
                // Ajouter les informations supplémentaires dans la Realtime Database
                $this->database->getReference('users/' . $user->uid)->set([
                    'nom' => $data["nom"] ?? null,
                    'prenom' => $data["prenom"] ?? null,
                    'adresse' => $data["adresse"] ?? null,
                    'telephone' => $data["telephone"] ?? null,
                    'email' => $data['email'],
                    'photo' => $data["photo"] ?? null,
                    'statut' => $data["statut"] ?? null,
                    'role' => $data["role"] ?? null,
                ]);
                return $user; // Retourne l'utilisateur créé, y compris l'UID

                // return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
            }


        } catch (\Exception $e) {
            // Gérer l'erreur en cas d'échec de création d'utilisateur
            return response()->json(['message' => 'User creation failed: ' . $e->getMessage()], 400);
        }
    }

    public function listUsers()
    {
        return $this->database->getReference('users')->getValue();
    }

    public function updateUser($userId, $data)
    {
        // Récupérer l'utilisateur dans Firebase
        $firebaseUser = $this->auth->getUser($userId);

        // Mettre à jour l'utilisateur dans Firebase (email, photo, etc.)
        $this->auth->updateUser($firebaseUser->uid, [
            'email' => $data['email'] ?? $firebaseUser->email,
            // 'nom' => $data['nom'] ?? $firebaseUser->displayName,
            // 'photoUrl' => $data['photo'] ?? $firebaseUser->photoUrl,
            // Ajoutez d'autres champs si nécessaire
        ]);

        return $firebaseUser;
    }
}
