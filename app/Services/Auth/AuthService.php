<?php
namespace App\Services\Auth;

use App\Services\Auth\FireBaseAuthService;
use App\Services\Auth\LocalAuthService;
use Illuminate\Http\Request; // Assure-toi d'importer cette classe


class AuthService
{
    protected $firebaseAuthService;
    protected $localAuthService;

    public function __construct(FireBaseAuthService $fireBaseAuthService, LocalAuthService $localAuthService)
    {
        $this->firebaseAuthService = $fireBaseAuthService;
        $this->localAuthService = $localAuthService;
    }

    public function login(Request $request)
    {
        if (env('DATABASE_SERVICE') === 'firebase') {
            return $this->firebaseAuthService->loginFirebase($request);
            // return FireBaseAuthService($request);
        } else {
            return $this->localAuthService->loginLocal($request);
        }
    }
}