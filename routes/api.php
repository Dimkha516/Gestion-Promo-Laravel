<?php

use App\Http\Controllers\ApprenantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::prefix('v1')->group(function () {

    // Route::get('api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api')->name('l5swagger.api');

    //----------------------------------------AUTHENTIFICATIONS:
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });

    //-------------------------------------------------------USERS

    Route::prefix('users')->group(function () {

        //----------------------------LISTER TOUS LES USERS
        // Route::middleware('auth:api')->get("/", [UserController::class, 'index']);
        Route::get("/", [UserController::class, 'index']);

        //----------------------------AJOUTER UN USER

        // Route::middleware('auth:api')->post("/", [UserController::class, 'store']);
        Route::post("/", [UserController::class, 'store']);

        //----------------------------METTRE À JOUR UN UTILISATEUR
        Route::middleware('auth:api')->patch("/{id}", [UserController::class, 'update']);

    });


    //-----------------------------------------------------REFÉRENTIELS

    Route::prefix('referentiels')->group(function () {

        //------------AJOUTER UN RÉFÉRENTIEL:

        // Route::middleware('auth:api')->post("/", [ReferentielController::class, 'store']);
        Route::post("/", [ReferentielController::class, 'store']);



        //------------LISTER LES RÉFÉRENTIELS AVEC POSSIBILITÉ DE FILTRE:

        // Route::middleware('auth:api')->get('/', [ReferentielController::class, 'index']);
        Route::get("/", [ReferentielController::class, 'index']);


        // FILTRER SUR UN RÉFÉRENTIEL PAR COMPÉTENCE OU MODULE:

        // Route::middleware('auth:api')->get('/{id}', [ReferentielController::class, 'filterByCompetenceOrModule']);
        Route::get("/{id}", [ReferentielController::class, 'filterByCompetenceOrModule']);


        // ACTION D'AJOUT ET DE MODIFICATION SUR LES RÉFÉRENTIELS:
        // Route::middleware('auth:api')->patch("/{id}", [ReferentielController::class, "update"]);
        Route::patch("/{id}", [ReferentielController::class, 'update']);

        // Route::middleware('auth:api')->delete("/{id}", [ReferentielController::class, "deleteReferentiel"]);
        // SUPPRESION D'UN RÉFÉRENTIEL DANS LA BASE DE DONNÉES:
        Route::delete("/{id}", [ReferentielController::class, 'deleteReferentiel']);

        // LISTER LES RÉFÉRENTIELS ARCHIVÉS:
        // Route::middleware('auth:api')->get('/archive/referentiel', [ReferentielController::class, 'archivedList']);
        Route::get("/archive/referentiel", [ReferentielController::class, 'archivedList']);
    });


    //-----------------------------------------------------APPRENANTS

    Route::prefix('apprenants')->group(function () {
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Unauthenticated.'], 401);
        // }

        // // Vérifie si l'utilisateur connecté a l'un des rôles requis
        // $currentUser = auth()->user();

        // if (!in_array($currentUser->role, ['Admin'])) {
        //     return response()->json(['message' => 'Accès refusé.'], 403);
        // }
        // Route::middleware('auth:api')->post('/', [ApprenantController::class, 'store']);
        Route::post("/", [ApprenantController::class, 'store']);

        Route::post('/import', [ApprenantController::class, 'importApprenants']);


        Route::post('/enrolle', [ApprenantController::class, 'inscrireApprenantPromoActif']);

        Route::get('/apprenants', [ApprenantController::class, 'listerApprenantsPromoActif']);
    });


    //-----------------------------------------------------PROMOS

    Route::prefix('promotions')->group(function () {

        // Route::middleware('auth:api')->get('/', [PromoController::class, 'index']);
        Route::get("/", [PromoController::class, 'index']);


        // Route::middleware('auth:api')->post('/', [PromoController::class, 'store']);
        Route::post('/', [PromoController::class, 'store']);


        // Route::middleware('auth:api')->post('/{id}/referentiels', [PromoController::class, 'addReferentielToPromo']);
        Route::post('/{id}/referentiels', [PromoController::class, 'addReferentielToPromo']);


        //------------------------------------------NOT YET ON SWAGGER: 
        // Route::middleware('auth:api')->patch('/{id}/etat', [PromoController::class, 'updateEtat']);
        Route::patch('/{id}/etat', [PromoController::class, 'updateEtat']);

        // Route::middleware('auth:api')->get('/encours', [PromoController::class, 'showActivePromo']);
        Route::get('/encours', [PromoController::class, 'showActivePromo']);


        // Route::middleware('auth:api')->get('/{id}/referentiels', [PromoController::class, 'listReferentiels']);
        Route::get('/{id}/referentiels', [PromoController::class, 'listReferentiels']);


    });



});


/*
 * 
 * 
 * "servers": [
        {
            "url": "https://localhost:3000",
            "description": "Serveur Local"
        }
    ],
 */