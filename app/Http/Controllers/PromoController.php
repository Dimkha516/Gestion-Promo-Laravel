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


    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $promos = $this->promoService->getAllPromos();

        return response()->json($promos);


    }


    public function store(PromoStoreRequest $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }


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
