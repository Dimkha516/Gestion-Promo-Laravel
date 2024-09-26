<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Assure-toi d'importer cette classe
use App\Services\Auth\AuthService;


class AuthController extends Controller{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Se connecter",
     *      tags={"Authentifaction"},
     *     @OA\Response(response="200", description="Login réussie !")
     * )
     */
    public function login(Request $request)
    {
        return $this->authService->login($request);
    }
}
