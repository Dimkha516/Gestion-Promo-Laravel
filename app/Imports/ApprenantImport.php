<?php

namespace App\Imports;

use App\Http\Requests\ApprenantStoreRequest;
use App\Models\Apprenant;
use App\Repositories\ApprenantRepository;
use App\Services\ApprenantService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;




class ApprenantImport implements ToModel, WithHeadingRow
{

    use Importable;

    protected $apprenantRepository;
    public function __construct(ApprenantRepository $apprenantRepository)
    {
        $this->apprenantRepository = $apprenantRepository;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Créer un nouvel apprenant pour chaque ligne
        // Journalisation des données pour vérifier leur structure
        $data = [
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'adresse' => $row['adresse'],
            'telephone' => $row['telephone'],
            'email' => $row['email'],
            'password' => $row['password'], // Hash le mot de passe dans le repo
            'role' => 'Apprenant',
            'nom_tuteur' => $row['nom_tuteur'],
            'contact_tuteur' => $row['contact_tuteur'],
            'statut' => $row['statut'] ?? 'actif',
            'photocopie_cni' => $row['photocopie_cni'],
            'diplome' => $row['diplome'],
            'visite_medicale' => $row['visite_medicale'],
            'extrait_naissance' => $row['extrait_naissance'],
            'casier_judiciaire' => $row['casier_judiciaire'],
        ];
        // Utilisation du repository pour créer l'apprenant et l'utilisateur associé
        $this->apprenantRepository->createApprenantByExport($data);

    }

    public function rules(): array
    {
        return [
            '*.email' => 'required|email|unique:users,email',
            '*.telephone' => 'required|regex:/^((77|76|75|70|78)\d{3}\d{2}\d{2})|(33[8]\d{2}\d{2}\d{2})$/',
            // Ajoutez les règles de validation ici
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            // Log or save the failure details
            \Log::error('Ligne ' . $failure->row() . ' : ' . $failure->errors()[0]);
        }
    }
}
