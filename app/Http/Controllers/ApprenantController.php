<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprenantStoreRequest;
use App\Imports\ApprenantImport;
use App\Repositories\ApprenantRepository;
use App\Services\ApprenantService;
use Illuminate\Http\Request;
use App\Jobs\ImportApprenantsJob;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class ApprenantController extends Controller
{
    protected $apprenantService;
    protected $apprenantRepository;

    public function __construct(ApprenantService $apprenantService, ApprenantRepository $apprenantRepository)
    {
        $this->apprenantService = $apprenantService;
        $this->apprenantRepository = $apprenantRepository;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/apprenants",
     *     summary="Inscrire un apprenant",
     *      tags={"apprenants"},
     *     @OA\Response(response="201", description="Inscription apprenant")
     * )
     */
    public function store(ApprenantStoreRequest $request)
    {

        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Valider et enregistrer l'apprenant et le compte utilisateur
        $validatedData = $request->validated();
        $apprenantUid = $this->apprenantService->createApprenant($validatedData);

        if ($apprenantUid) {
            return response()->json([
                'message' => 'Apprenant créé avec succès',
                'apprenant_uid' => $apprenantUid
            ], 201);
        } else {
            return response()->json(['message' => 'Erreur lors de la création de l\'apprenant'], 500);
        }
    }


    // Méthode pour importer les apprenants depuis un fichier Excel
    /**
     * @OA\Post(
     *     path="/api/v1/import",
     *     summary="Inscrire plusieurs apprenants par import fichier excel",
     *      tags={"apprenants"},
     *     @OA\Response(response="201", description="Inscription en masse")
     * )
     */
    public function importApprenants(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,ods' // Validation du fichier
        ]);

        // Appel du service pour traiter l'importation
        $result = $this->apprenantService->importApprenants($request->file('file'));

        if ($result) {
            return response()->json(['message' => 'Importation réussie'], 200);
        } else {
            return response()->json(['message' => 'Échec de l\'importation'], 500);
        }

        // if ($result['success']) {
        //     return response()->json(['message' => 'Importation réussie'], 200);
        // } else {
        //     // En cas d'erreurs de validation
        //     return response()->json([
        //         'message' => 'Échec de l\'importation pour l\'apprenant : ' . $result['email'],
        //         'errors' => $result['errors']
        //     ], 422);
        // }

    }





}
