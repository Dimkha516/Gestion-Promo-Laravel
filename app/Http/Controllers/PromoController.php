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


    public function updateEtat(Request $request, $promoId)
    {
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Unauthenticated.'], 401);
        // }

        // // Vérifie si l'utilisateur connecté a l'un des rôles requis
        // $currentUser = auth()->user();

        // if (!in_array($currentUser->role, ['Manager'])) {
        //     return response()->json(['message' => 'Accès refusé.'], 403);
        // }

        // Validation de la requête pour l'état
        $validatedData = $request->validate([
            'etat' => 'required|in:Actif,Inactif,Archive'
        ]);

        try {
            // Appel du service pour mettre à jour l'état
            $message = $this->promoService->updatePromoEtat($promoId, $validatedData['etat']);
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            // Gérer les exceptions et renvoyer un message d'erreur
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function showActivePromo()
    {
        try {
            // Appel du service pour récupérer la promo active
            $promo = $this->promoService->getActivePromo();
            return response()->json(['promo' => $promo], 200);
        } catch (\Exception $e) {
            // Gérer les exceptions et renvoyer un message d'erreur
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


    // Endpoint pour lister les référentiels d'une promo
    public function listReferentiels($promoId)
    {
        $referentiels = $this->promoService->getReferentielsByPromo($promoId);

        if (is_null($referentiels)) {
            return response()->json(['message' => 'Promo non trouvée'], 404);
        }

        return response()->json(['referentiels' => $referentiels], 200);
    }



}
