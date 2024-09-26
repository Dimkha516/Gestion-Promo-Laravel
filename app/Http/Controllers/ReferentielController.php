<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferentielStoreRequest;
use App\Services\ReferentielService;
use Illuminate\Http\Request;

class ReferentielController extends Controller
{
    protected $referentielService;

    public function __construct(ReferentielService $referentielService)
    {
        $this->referentielService = $referentielService;
    }

    // Endpoint pour lister les référentiels avec filtre par statut
    /**
     * @OA\Get(
     *     path="/api/V1/referentiels",
     *     summary="Liste tous les référentiels",
     *     @OA\Response(response="200", description="Liste des référentiels")
     * )
     */
    public function index(Request $request)
    {
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Unauthenticated.'], 401);
        // }

        // // Vérifie si l'utilisateur connecté a l'un des rôles requis
        // $currentUser = auth()->user();

        // if (!in_array($currentUser->role, ['Admin'])) {
        //     return response()->json(['message' => 'Accès refusé.'], 403);
        // }

        
        $statut = $request->query('statut'); // Filtrer par statut si fourni
        $referentiels = $this->referentielService->listReferentiels($statut);



        return response()->json($referentiels);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/archive/referentiel",
     *     summary="Liste tous les référentiels archivés",
     *     @OA\Response(response="200", description="Liste des référentiels archivés")
     * )
     */
    public function archivedList()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $archivedRef = $this->referentielService->listArchivedReferentiel();
        return response()->json($archivedRef);
    }



    /**
     * @OA\Post(
     *     path="/api/v1/referentiel",
     *     summary="Créer un nouveau référentiel",
     *     @OA\Response(response="200", description="Création référentiel")
     * )
     */
    public function store(ReferentielStoreRequest $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $data = $request->validated();

        // Convertir la chaîne JSON des compétences en tableau
        if ($request->has('Competences')) {
            $data['Competences'] = json_decode($request->input('Competences'), true);
        }

        $this->referentielService->createReferentiel($data);


        return response()->json(['message' => 'Référentiel créé avec succès'], 201);


    }


    public function show($id)
    {
        return response()->json($this->referentielService->findReferentiel($id));
    }

    // public function update(Request $request, $id)
    // {
    //     return response()->json($this->referentielService->updateReferentiel($id, $request->all()));
    // }

    public function destroy($id)
    {
        return response()->json($this->referentielService->deleteReferentiel($id));
    }


    // Endpoint pour filtrer les compétences ou les modules d'un référentiel
    /**
     * @OA\Get(
     *     path="/api/v1/referentiels/{id}",
     *     summary="Filtre compétences ou modules d'un référentiel",
     *     @OA\Response(response="200", description="Filtre référentiels par compétences/modules")
     * )
     */
    public function filterByCompetenceOrModule(Request $request, $uid)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }


        // Récupérer le type de filtre (competence ou module) et la valeur du filtre
        $filterType = $request->query('filterType'); // 'competence' ou 'module'
        $filterValue = $request->query('filterValue'); // le nom de la compétence ou du module

        if (!in_array($filterType, ['competence', 'module'])) {
            return response()->json(['error' => 'Invalid filter type'], 400);
        }

        // Appeler le service pour filtrer les données
        $result = $this->referentielService->filterByCompetenceOrModule($uid, $filterType, $filterValue);

        return response()->json($result);
    }


    /**
     * @OA\Patch(
     *     path="/api/v1/referentiel/{id}",
     *     summary="Ajout compétences et module pour référentiel",
     *     @OA\Response(response="200", description="modifier référentiel par ajout")
     * )
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }


        $data = $request->validate([
            'action' => 'required|string',
            'competence_nom' => 'string|nullable',
            'competence' => 'array|nullable',
            'modules' => 'array|nullable',
            'module_nom' => 'string|nullable',
        ]);

        $referentiel = $this->referentielService->findReferentiel($id);

        if (!$referentiel) {
            return response()->json(['message' => 'Referentiel not found'], 404);
        }

        switch ($data['action']) {
            case 'add_competence':
                return $this->referentielService->addCompetence($referentiel, $data['competence']);
            case 'add_modules':
                return $this->referentielService->addModulesToCompetence($referentiel, $data['competence_nom'], $data['modules']);
            case 'remove_module':
                return $this->referentielService->removeModuleFromCompetence($referentiel, $data['competence_nom'], $data['module_nom']);
            default:
                return response()->json(['message' => 'Invalid action'], 400);
        }
    }

    // SUPPRESSION SOFT DANS LA BASE DE DONNÉES FIREBASE:

    /**
     * @OA\Delete(
     *     path="/api/v1/referentiel/{id}",
     *     summary="Archiver référentiel",
     *     @OA\Response(response="200", description="Archivage référentiel")
     * )
     */
    public function deleteReferentiel($id)
    {
        // Vérifie si l'utilisateur est authentifié et a les droits requis
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $currentUser = auth()->user();
        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Récupérer le référentiel par son ID
        $referentiel = $this->referentielService->findReferentiel($id);

        if (!$referentiel) {
            return response()->json(['message' => 'Referentiel not found'], 404);
        }

        // Effectuer le soft delete
        return $this->referentielService->softDeleteReferentiel($referentiel);
    }




}
