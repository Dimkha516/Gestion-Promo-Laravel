<?php

namespace App\Repositories;

use App\Models\FirebaseInterfaceImplement;

class ApprenantRepository
{
    protected $firebase;

    public function __construct(FirebaseInterfaceImplement $firebase)
    {
        $this->firebase = $firebase;
    }

    // Créer un apprenant uniquement dans Firebase
    public function create(array $data)
    {
        return $this->firebase->createInFirebase($data);
    }

    // Mettre à jour un apprenant uniquement dans Firebase
    public function update($id, array $data)
    {
        return $this->firebase->updateInFirebase($id, $data);
    }

    // Supprimer un apprenant uniquement dans Firebase
    public function delete($id)
    {
        return $this->firebase->deleteFromFirebase($id);
    }

    // Récupérer un apprenant depuis Firebase
    public function find($id)
    {
        return $this->firebase->findInFirebase($id);
    }

    // Récupérer tous les apprenants depuis Firebase
    // public function all()
    // {
    //     return $this->firebase->getAllFromFirebase('apprenants');
    // }

    public function createUser($userData)
    {
        $userReference = $this->firebase->getReference('users')->push($userData);
        return $userReference->getKey();  // UID généré par Firebase
    }

    public function createApprenant($apprenantData)
    {
        $apprenantReference = $this->firebase->getReference('apprenants')->push($apprenantData);
        return $apprenantReference->getKey();  // UID généré pour l'apprenant
    }


    // public function createApprenantByExport(array $data)
    public function createApprenantByExport(array $data)
    {
        // Inscription de l'utilisateur associé à l'apprenant
        $userRef = $this->firebase->getReference('users')->push([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'role' => 'Apprenant',
            'password' => bcrypt($data['password']),
            'statut' => $data['statut'],
        ]);

        // Création de l'apprenant dans la collection apprenants
        $this->firebase->getReference('apprenants')->push([
            'user_id' => $userRef->getKey(),
            'nom_tuteur' => $data['nom_tuteur'],
            'contact_tuteur' => $data['contact_tuteur'],
            // Ajoutez ici d'autres attributs spécifiques à l'apprenant
        ]);

        return $userRef;
    }


}
