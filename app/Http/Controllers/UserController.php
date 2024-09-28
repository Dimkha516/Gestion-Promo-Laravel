<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User2;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Log;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Liste tous les utilisateurs",
     *      tags={"users"},
     *     @OA\Response(response="200", description="Liste des utilisateurs")
     * )
     */
    public function store(StoreUserRequest $request)
    {

        $data = $request->validated();
        // Créer un nouvel utilisateur avec les données validées
        $newUser = new User2($data);

        // Vérifier si l'utilisateur connecté a les permissions pour créer cet utilisateur
        // if (Gate::denies('create', $newUser)) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Si les permissions sont accordées, créer l'utilisateur via le service
        $this->userService->createUser($data);

        return response()->json(['message' => 'Utilisateur créé avec succès'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Créer un nouvel utilisateur",
     *      tags={"users"},
     *     @OA\Response(response="201", description="Créaation utilisateurs")
     * )
     */
    public function index(Request $request)
    {

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        // $currentUser = auth()->user();
        // if (!in_array($currentUser->role, ['Admin', 'CM', 'Manager'])) {
        //     return response()->json(['message' => 'Accès refusé.'], 403);
        // }


        // Récupérer le rôle à filtrer (optionnel)
        $role = $request->query('role');

        // Appeler le service pour obtenir la liste des utilisateurs (avec ou sans filtre de rôle)
        $users = $this->userService->listUsers($role);


        return response()->json($users);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *      tags={"users"},
     *     @OA\Response(response="200", description="Mise à jour utilisateur")
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {

        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Vérifie si l'utilisateur connecté a l'un des rôles requis
        $currentUser = auth()->user();

        if (!in_array($currentUser->role, ['Admin', 'CM', 'Manager'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $data = $request->validated();

        // Appeler le service pour mettre à jour l'utilisateur
        $user = $this->userService->updateUser($id, $data);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user]);
    }
}
