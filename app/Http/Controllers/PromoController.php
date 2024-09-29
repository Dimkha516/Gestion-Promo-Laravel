<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoStoreRequest;
use App\Services\PromoService;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    protected $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/promotions",
     *     summary="Lister toutes les promos",
     *      tags={"promos"},
     *     @OA\Response(response="200", description="Liste des promos")
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

        $promos = $this->promoService->getAllPromos();

        return response()->json($promos);


    }

    /**
     * @OA\Post(
     *     path="/api/v1/promotions",
     *      tags={"promos"},
     *     summary="Créer une nouvelle promo",
     *     @OA\Response(response="201", description="Création Promo")
     * )
     */
    public function store(PromoStoreRequest $request)
    {
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Unauthenticated.'], 401);
        // }

        // // Vérifie si l'utilisateur connecté a l'un des rôles requis
        // $currentUser = auth()->user();

        // if (!in_array($currentUser->role, ['Admin'])) {
        //     return response()->json(['message' => 'Accès refusé.'], 403);
        // }


        // Valide les données de la requête 
        $data = $request->validated();

        // Créer la promotion 
        $result = $this->promoService->createPromo($data);

        if ($result) {
            return response()->json(['message' => 'Promotion créée avec succès'], 201);
        }

        return response()->json(['message' => 'Échec de la création de la promotion'], 500);
    }


    // Endpoint pour ajouter un référentiel à une promo
    /**
     * @OA\Post(
     *     path="/api/v1/promotions/{id}/referentiels",
     *     summary="Ajouter un référentiel actif à une promo",
     *      tags={"promos"},
     *     @OA\Response(response="201", description="Ajout Référentiel promo")
     * )
     */
    public function addReferentielToPromo(Request $request, $promoUid)
    {
        $referentielUid = $request->input('referentiel_uid');
        // Vérifie si le referentiel_uid est fourni
        if (!$referentielUid) {
            return response()->json(['message' => 'Referentiel UID is required'], 400);
        }


        // Appelle le service pour ajouter le référentiel à la promo
        return $this->promoService->addReferentielToPromo($promoUid, $referentielUid);
    }


}
