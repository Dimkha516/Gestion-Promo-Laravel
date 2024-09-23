<?php

namespace App\Services;

use App\Models\FirebaseInterfaceImplement;
use App\Repositories\ReferentielRepository;

class ReferentielService
{
    protected $referentielRepository;

    public function __construct(ReferentielRepository $referentielRepository)
    {
        $this->referentielRepository = $referentielRepository;
    }

    public function createReferentiel(array $data)
    {
        if ($this->referentielRepository->exists($data)) {
            throw new \Exception('Un référentiel avec ce code ou ce libellé existe déjà.');
        }

        $this->referentielRepository->create($data);
    }

    public function updateReferentiel($id, array $data)
    {
        return $this->referentielRepository->update($id, $data);
    }

    public function deleteReferentiel($id)
    {
        return $this->referentielRepository->delete($id);
    }

    public function findReferentiel($id)
    {
        return $this->referentielRepository->find($id);
    }

    public function getAllReferentiels()
    {
        // return $this->referentielRepository->all();

    }

    // LISTER LES RÉFÉRENTIELS AVEC POSSIBILTÉ DE FILTRE PAR STATUT:
    // Récupérer les référentiels avec option de filtre par statut
    public function listReferentiels($statut = null)
    {
        return $this->referentielRepository->getReferentiels($statut);
    }

    // LISTER LES RÉFÉRENTIELS ARCHIVÉS:
    public function listArchivedReferentiel(){
        return $this->referentielRepository->getArchivedReferentiels();
    }



    // Filtrer les compétences ou les modules d'un référentiel par son uid
    public function filterByCompetenceOrModule($uid, $filterType, $filterValue)
    {
        // Récupérer le référentiel par uid
        $referentiel = $this->referentielRepository->getReferentielByUid($uid);

        // Filtrer par compétence ou module
        return $this->referentielRepository->filterByCompetenceOrModule($referentiel, $filterType, $filterValue);
    }


    public function addCompetence($referentiel, $competence)
    {

        // \Log::info('Referentiel:', $referentiel);

        // Si le référentiel ne contient pas déjà un `uid`, nous allons le récupérer depuis Firebase via le repository
        if (!isset($referentiel['uid'])) {
            $referentiel['uid'] = $this->referentielRepository->getFirebaseUid($referentiel);
        }

        if (isset($referentiel['uid'])) {
            // Récupérer les compétences existantes
            $competences = $referentiel['competences'] ?? [];

            // Ajouter la nouvelle compétence
            $competences[] = $competence;

            // Mettre à jour le référentiel dans Firebase
            $updated = $this->referentielRepository->update($referentiel['uid'], ['competences' => $competences]);

            if ($updated) {
                return response()->json(['message' => 'Competence added successfully']);
            } else {
                return response()->json(['message' => 'Failed to update referentiel'], 500);
            }

        }

        return response()->json(['message' => 'Referentiel UID not found'], 404);

    }

    public function addModulesToCompetence($referentiel, $competenceNom, $modules)
    {
        // Si le référentiel ne contient pas déjà un `uid`, nous allons le récupérer depuis Firebase via le repository
        if (!isset($referentiel['uid'])) {
            $referentiel['uid'] = $this->referentielRepository->getFirebaseUid($referentiel);
        }

        // Ajouter les modules à la compétence spécifiée
        $competences = $referentiel['competences'] ?? [];

        foreach ($competences as &$competence) {
            if ($competence['nom'] === $competenceNom) {
                $competence['modules'] = array_merge($competence['modules'] ?? [], $modules);
                break;
            }
        }
        // Mettre à jour le référentiel dans Firebase
        $updated = $this->referentielRepository->update($referentiel['uid'], ['competences' => $competences]);
        if ($updated) {
            return response()->json(['message' => 'Competence added successfully']);
        } else {
            return response()->json(['message' => 'Failed to update referentiel'], 500);
        }
    }

    public function removeModuleFromCompetence($referentiel, $competenceNom, $moduleNom)
    {
        // Si le référentiel ne contient pas déjà un `uid`, nous allons le récupérer depuis Firebase via le repository
        if (!isset($referentiel['uid'])) {
            $referentiel['uid'] = $this->referentielRepository->getFirebaseUid($referentiel);
        }
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

        // Mettre à jour le référentiel dans Firebase
        $updated = $this->referentielRepository->update($referentiel['uid'], ['competences' => $competences]);
        if ($updated) {
            return response()->json(['message' => 'Competence added successfully']);
        } else {
            return response()->json(['message' => 'Failed to update referentiel'], 500);
        }
        // Mettez à jour le référentiel dans Firebase
        // $this->referentielRepository->update($referentiel['uid'], ['competences' => $competences]);

    }


    public function softDeleteReferentiel($referentiel)
    {
        // Vérifiez si le uid est disponible
        $uid = $this->referentielRepository->getFirebaseUid($referentiel);

        if (!$uid) {
            return response()->json(['message' => 'Referentiel UID not found'], 404);
        }

        // Marquez le référentiel comme supprimé (soft delete)
        $deletedAt = now()->toDateTimeString(); // Récupérer la date actuelle

        // Mettre à jour le champ deleted_at dans Firebase
        $updated = $this->referentielRepository->update($uid, ['deleted_at' => $deletedAt]);

        if ($updated) { 
            return response()->json(['message' => 'Referentiel soft deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete referentiel'], 500);
        }
    }
 
}
