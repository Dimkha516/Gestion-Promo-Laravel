<?php

namespace App\Services;

use App\Imports\ApprenantImport;
use App\Repositories\ApprenantRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ApprenantStoreRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
class ApprenantService
{
    protected $apprenantRepository;

    public function __construct(ApprenantRepository $apprenantRepository)
    {
        $this->apprenantRepository = $apprenantRepository;
    }

    // public function createApprenant(array $data)
    // {
    //     return $this->apprenantRepository->create($data);
    // }

    public function updateApprenant($id, array $data)
    {
        return $this->apprenantRepository->update($id, $data);
    }

    public function deleteApprenant($id)
    {
        return $this->apprenantRepository->delete($id);
    }

    public function findApprenant($id)
    {
        return $this->apprenantRepository->find($id);
    }

    public function getAllApprenants()
    {
        // return $this->apprenantRepository->all();
    }


    public function createApprenant($validatedData)
    {
        // Enregistrement des informations dans la collection users
        $userData = [
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'email' => $validatedData['email'],
            // 'telephone' => $validatedData['telephone'],
            'role' => 'Apprenant',
            'password' => bcrypt($validatedData['password']),
            'photo' => $validatedData['photo'] ?? null,
            'statut' => $validatedData['statut'] ?? 'actif',
        ];
        $userUid = $this->apprenantRepository->createUser($userData);

        // Enregistrement des informations de l'apprenant
        $apprenantData = [
            'user_uid' => $userUid,
            'nom_tuteur' => $validatedData['nom_tuteur'],
            'contact_tuteur' => $validatedData['contact_tuteur'],
            'photocopie_cni' => $validatedData['photocopie_cni'], // Chemin vers le fichier uploadé
            'diplome' => $validatedData['diplome'],
            'visite_medicale' => $validatedData['visite_medicale'],
            'extrait_naissance' => $validatedData['extrait_naissance'],
            'casier_judiciaire' => $validatedData['casier_judiciaire'],
            // 'promo_uid' => $validatedData['promo_uid'],
            // 'referentiel_uid' => $validatedData['referentiel_uid'],
            'moyenne' => null,  // Initialisé à null
            'appreciation' => null,
            'notes' => [],  // Initialisé à un tableau vide
            'presences' => [],
        ];

        return $this->apprenantRepository->createApprenant($apprenantData);
    }


    // Méthode d'importation des apprenants depuis un fichier Excel
    public function importApprenants($file)
    {   
        
        try {
            // Utilisation de l'importateur pour lire les données
            Excel::import(new ApprenantImport($this->apprenantRepository), $file);
            return true;
        } catch (\Exception $e) {
            \Log::error("Erreur d'importation des apprenants : " . $e->getMessage());
            return false;
        }
    }

    // public function importApprenants($file)
    // {
    //     // Supposons que vous avez un moyen de lire le fichier Excel et d'obtenir les apprenants
    //     $apprenants = $this->parseExcelFile($file);

    //     foreach ($apprenants as $data) {
    //         try { 
    //             // Appliquer la validation via ApprenantStoreRequest
    //             $validatedData = (new ApprenantStoreRequest())->validate($data);

    //             // Si la validation réussit, envoyer les données à Firebase ou base de données
    //             $this->envoyerDonneesFirebase($validatedData);

    //         } catch (ValidationException $e) {
    //             // Enregistrer les erreurs dans les logs
    //             Log::error('Erreur de validation pour l\'apprenant : ' . $data['email'], $e->errors());

    //             // Renvoyer les erreurs au contrôleur pour arrêter l'importation
    //             return [
    //                 'success' => false,
    //                 'errors' => $e->errors(),
    //                 'email' => $data['email']
    //             ];
    //         }
    //     }

    //     // Si tout est validé et importé correctement
    //     return [
    //         'success' => true,
    //         'message' => 'Importation réussie'
    //     ];
    // }

    private function parseExcelFile($file)
    {
        // Logic to read Excel file and return apprenants data as an array
    }

    private function envoyerDonneesFirebase($validatedData)
    {
        // Logic to send data to Firebase
    }

}
