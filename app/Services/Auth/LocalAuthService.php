<?php
namespace App\Services\Auth;
use App\Models\User2;
use Auth;
use Illuminate\Http\Request; // Assure-toi d'importer cette classe
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;





class LocalAuthService
{
    public function loginLocal(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Récupérer l'utilisateur
        $user = User2::where('email', $request->email)->first();
    
        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Authentifier l'utilisateur
            Auth::login($user);
            
            // Générer un token
            $token = $user->createToken('YourAppName')->accessToken;
    
            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ], 200);
        }
    
        return response()->json(['message' => 'Identifiants utilisateur incorrects !'], 401);

    }
}