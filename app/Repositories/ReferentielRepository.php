<?php

namespace App\Repositories;

use App\Models\FirebaseInterfaceImplement;
use App\Models\Referentiel;

class ReferentielRepository
{
    protected $firebase;

    public function __construct(FirebaseInterfaceImplement $firebase)
    {
        $this->firebase = $firebase;
    }

    public function exists(array $data)
    {
        $referentiels = $this->firebase->getAllFromFirebase('referentiels');

        foreach ($referentiels as $referentiel) {
            if (
                $referentiel['codeReferentiel'] === $data['codeReferentiel'] ||
                $referentiel['libelleReferentiel'] === $data['libelleReferentiel']
            ) {
                return true;
            }
        }

        return false;
    }

    // Créer un référentiel uniquement dans Firebase
    public function create(array $data)
    {
        $referentielData = [
            'codeReferentiel' => $data['codeReferentiel'],
            'libelleReferentiel' => $data['libelleReferentiel'],
            'StatutReferentiel' => $data['StatutReferentiel'],
            'photo' => $data['photo'] ?? null,
            'Description' => $data['Description'] ?? '',
            'Competences' => $data['Competences'] ?? [],
        ];
        return $this->firebase->createInFirebase($referentielData);
    }


    // Mettre à jour un référentiel uniquement dans Firebase
    public function update($id, array $data)
    {
        return $this->firebase->updateInFirebase($id, $data);
    }

    // Supprimer un référentiel uniquement dans Firebase
    public function delete($id)
    {
        return $this->firebase->deleteFromFirebase($id);
    }

    // Récupérer un référentiel depuis Firebase
    public function find($id)
    {
        return $this->firebase->findInFirebase($id);
    }


    // RECUPÉRER TOUS LES RÉFÉRENTIELS AVEC FILTRE OPTIONNEL PAR STATUT
    public function getReferentiels($statut = null)
    {
        $referentiels = $this->firebase->getAllFromFirebase('referentiels');

        if ($referentiels) {
            // Filtrer les référentiels supprimés (ceux avec deleted_at)
            $referentiels = array_filter($referentiels, function ($referentiel) {
                return !isset($referentiel['deleted_at']); // Exclure les référentiels avec 'deleted_at'
            });

            // Filtrer par statut si nécessaire
            if ($statut) {
                return array_filter($referentiels, function ($referentiel) use ($statut) {
                    return isset($referentiel['StatutReferentiel']) && $referentiel['StatutReferentiel'] === $statut;
                });
            }
        }
        // Retourne tous les référentiels si aucun statut n'est spécifié
        return $referentiels;
    }

    // Récupérer les compétences d'un référentiel et ses modules
    public function filterByCompetence($competenceName)
    {
        return Referentiel::byCompetence($competenceName)->get();
    }

    // Récupérer les modules d'un référentiel
    public function filterByModule($moduleName)
    {
        return Referentiel::byModule($moduleName)->get();
    }

    // Récupérer un référentiel spécifique depuis Firebase par uid
    public function getReferentielByUid($uid)
    {
        return $this->firebase->findInFirebase($uid);
    }

    // Filtrer par compétence ou module
    public function filterByCompetenceOrModule($referentiel, $filterType, $filterValue)
    {
        if ($filterType === 'competence') {
            return collect($referentiel['competences'] ?? [])->filter(function ($competence) use ($filterValue) {
                return $competence['nom'] === $filterValue;
            });
        }

        if ($filterType === 'module') {
            return collect($referentiel['modules'] ?? [])->filter(function ($module) use ($filterValue) {
                return $module['nom'] === $filterValue;
            });
        }

        return [];
    }


    public function addCompetence($referentiel, $competenceData)
    {
        // Ajouter la compétence au référentiel
        // Assurez-vous de vérifier l'unicité si nécessaire
        $competences = $referentiel['competences'] ?? [];
        $competences[] = $competenceData;

        // Mettez à jour le référentiel dans Firebase
        $this->firebase->updateInFirebase($referentiel['uid'], ['competences' => $competences]);

        return response()->json(['message' => 'Competence added successfully'], 200);
    }

    public function addModulesToCompetence($referentiel, $competenceNom, $modules)
    {
        // Ajouter les modules à la compétence spécifiée
        $competences = $referentiel['competences'] ?? [];

        foreach ($competences as &$competence) {
            if ($competence['nom'] === $competenceNom) {
                $competence['modules'] = array_merge($competence['modules'] ?? [], $modules);
                break;
            }
        }

        // Mettez à jour le référentiel dans Firebase
        $this->firebase->updateInFirebase($referentiel['uid'], ['competences' => $competences]);

        return response()->json(['message' => 'Modules added successfully'], 200);
    }

    public function removeModuleFromCompetence($referentiel, $competenceNom, $moduleNom)
    {
        // Supprimer le module de la compétence spécifiée
        $competences = $referentiel['competences'] ?? [];

        foreach ($competences as &$competence) {
            if ($competence['nom'] === $competenceNom) {
                $competence['modules'] = array_filter($competence['modules'], function ($module) use ($moduleNom) {
                    return $module['nom'] !== $moduleNom;
                });
                break;
            }
        }

        // Mettez à jour le référentiel dans Firebase
        $this->firebase->updateInFirebase($referentiel['uid'], ['competences' => $competences]);

        return response()->json(['message' => 'Module removed successfully'], 200);
    }


    public function update2($uid, $data)
    {
        return $this->firebase->updateInFirebase($uid, $data);
    }

    public function find2($id)
    {
        $referentiel = $this->firebase->findInFirebase($id);
        return $referentiel; // Assurez-vous que uid est inclus ici
    }

    public function getFirebaseUid($referentiel)
    {
        // Appel à la méthode de `FirebaseInterfaceImplement` pour récupérer l'UID
        return $this->firebase->getFirebaseUid($referentiel);
    }


    // LISTER LES RÉFÉRENTIELS SUPPRIMÉS:
    public function getArchivedReferentiels($statut = null)
    {
        $referentiels = $this->firebase->getAllFromFirebase('referentiels');

        if ($referentiels) {
            // Filtrer les référentiels supprimés (ceux avec deleted_at)
            $referentiels = array_filter($referentiels, function ($referentiel) {
                return isset($referentiel['deleted_at']); // Exclure les référentiels avec 'deleted_at'
            });
            return $referentiels;
        }
        // Retourne tous les référentiels si aucun statut n'est spécifié
        return response()->json([
            "message" => "Archive Référentiel vide."
        ], 400);
    }


    // Trouver un référentiel par UID
    public function findReferentielByUid($uid)
    {
        $referentiels = $this->firebase->getAllFromFirebase('referentiels');
        foreach ($referentiels as $referentiel) {
            if ($referentiel['uid'] === $uid) {
                return $referentiel;
            }
        }
        return null;
    }

    // Trouver un référentiel par son libellé
    

}
