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

    //------------------INSCRIRE APPRENANT EXISTANT DANS LA PROMO ENCOURS:
    public function findApprenantById($apprenantId)
    {
        return $this->firebase->getReference('apprenants/' . $apprenantId)->getValue();
    }
    public function findPromoActif()
    {
        $promos = $this->firebase->getReference('promos')
            ->orderByChild('etat')
            ->equalTo('Actif')
            ->getValue();

        // Vérifier si une promotion active existe
        if (!empty($promos)) {
            // Récupérer la première promo active
            $promoActif = array_values($promos)[0];

            // Récupérer la clé Firebase (l'ID)
            $promoActifKey = array_key_first($promos);

            // Retourner la promo active avec sa clé Firebase
            return ['promo' => $promoActif, 'key' => $promoActifKey];
        }

        return null;
    }


    public function findReferentielById($referentielId)
    {
        return $this->firebase->getReference('referentiels/' . $referentielId)->getValue();
    }
    public function isApprenantInPromo($apprenantId, $promoKey)
    {
        $enrolledApprenants = $this->firebase->getReference('promos/' . $promoKey . '/apprenants')->getValue();
        return isset($enrolledApprenants[$apprenantId]);
    }

    public function enrollApprenantInPromo($apprenantId, $promoKey, $referentielId)
    {
        $this->firebase->getReference('promos/' . $promoKey . '/apprenants/' . $apprenantId)->set([
            'referentiel_id' => $referentielId,
            'date_inscription' => date('Y-m-d H:i:s')
        ]);
    }
    //------------------INSCRIRE APPRENANT EXISTANT DANS LA PROMO ENCOURS FIN:


    //------------------LISTER LES APPRENANTS DE LA PROMO ACTIVE DEBUT:


    // public function findApprenantsInPromoActif()
    // {
    //     // Récupérer la promo active
    //     $promoActifData = $this->findPromoActif();
    //     if (!$promoActifData) {
    //         return null; // Aucun promo actif trouvé
    //     }

    //     $promoKey = $promoActifData['key'];

    //     // Récupérer les apprenants inscrits dans la promo active
    //     $apprenants = $this->firebase->getReference('promos/' . $promoKey . '/apprenants')->getValue();

    //     if (!$apprenants) {
    //         return []; // Aucun apprenant dans cette promo
    //     }

    //     $result = [];
    //     foreach ($apprenants as $apprenantId => $apprenantData) {
    //         // Récupérer les informations de l'apprenant
    //         $apprenant = $this->findApprenantById($apprenantId);
    //         if ($apprenant) {
    //             // Récupérer le libellé du référentiel
    //             $referentiel = $this->findReferentielById($apprenantData['referentiel_id']);

    //             $result[] = [
    //                 // 'apprenant' => $apprenant['nom'] . ' ' . $apprenant['prenom'],
    //                 'nom' => $apprenant['nom_tuteur'], // Récupérer le nom_tuteur directement
    //                 'libelleReferentiel' => $referentiel ? $referentiel['libelleReferentiel'] : 'Référentiel non trouvé'
    //             ];
    //         }
    //     }

    //     return $result;
    // }

    public function findApprenantsInPromoActif($referentielId = null)
    {
        // Récupérer la promo active
        $promoActifData = $this->findPromoActif();
        if (!$promoActifData) {
            return null; // Aucun promo actif trouvé
        }

        $promoKey = $promoActifData['key'];

        // Récupérer les apprenants inscrits dans la promo active
        $apprenants = $this->firebase->getReference('promos/' . $promoKey . '/apprenants')->getValue();

        if (!$apprenants) {
            return []; // Aucun apprenant dans cette promo
        }

        $result = [];
        foreach ($apprenants as $apprenantId => $apprenantData) {
            // Appliquer le filtre si un referentiel_id est passé
            if ($referentielId && $apprenantData['referentiel_id'] !== $referentielId) {
                continue; // Sauter les apprenants qui ne sont pas dans le référentiel spécifié
            }

            // Récupérer les informations de l'apprenant
            $apprenant = $this->findApprenantById($apprenantId);
            if ($apprenant) {
                // Récupérer le libellé du référentiel
                $referentiel = $this->findReferentielById($apprenantData['referentiel_id']);

                $result[] = [
                    // 'apprenant' => $apprenant['nom'] . ' ' . $apprenant['prenom'],
                    'nom' => $apprenant['nom_tuteur'], // Récupérer le nom_tuteur directement
                    'libelleReferentiel' => $referentiel ? $referentiel['libelleReferentiel'] : 'Référentiel non trouvé'
                ];
            }
        }

        return $result;
    }



    //------------------LISTER LES APPRENANTS DE LA PROMO ACTIVE FIN:

}
