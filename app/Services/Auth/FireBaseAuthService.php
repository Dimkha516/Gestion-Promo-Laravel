<?php
namespace App\Services\Auth;
use App\Models\User2;
use Illuminate\Http\Request; // Assure-toi d'importer cette classe
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\InvalidCustomToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidToken;
use Kreait\Firebase\Exception\Auth\RevokedIdToken;




class FireBaseAuthService
{
    protected $firebaseAuth;

    public function __construct()
    {   


        $encodedCredentials = config('services.firebase.credentials');
        $decodedCredentials = base64_decode($encodedCredentials);

        if (!$decodedCredentials) {
            throw new \Exception("Failed to decode Firebase credentials");
        }

        // Nettoyage du JSON décodé
        $decodedCredentials = trim($decodedCredentials);

        $credentialsArray = json_decode($decodedCredentials, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse Firebase credentials JSON: " . json_last_error_msg());
        }


        $firebase = (new Factory)
            // ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withServiceAccount($credentialsArray)
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        // On initialise FirebaseAuth via la Factory
        $this->firebaseAuth = $firebase->createAuth();
    }

    public function loginFirebase(Request $request)
    {
        // Appelle la méthode validate directement sur l'instance de $request

        // Valider les champs email et mot de passe
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Authentification avec email et mot de passe
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($validated['email'], $validated['password']);

            // Récupérer le token et les informations utilisateur après authentification réussie
            $firebaseToken = $signInResult->idToken(); // Token d'authentification Firebase
            $firebaseUser = $this->firebaseAuth->getUser($signInResult->firebaseUserId()); // Détails utilisateur

            // / Maintenant, on vérifie si l'utilisateur existe dans la base de données locale
            $user = User2::where('email', $validated['email'])->first();

            if (!$user) {
                // Si l'utilisateur n'existe pas encore localement, on peut le créer
                $user = User2::create([
                    'email' => $validated['email'],
                ]);
            }

            // Générer un token Passport pour cet utilisateur
            $passportToken = $user->createToken('YourAppName')->accessToken;

            return response()->json([
                'message' => "Authentification réussie !",
                'firebase_token' => $firebaseToken,
                'passport_token' => $passportToken, // Ce token sera utilisé pour accéder aux routes protégées
                // 'user' => $firebaseUser
            ], 200);

        } catch (InvalidCustomToken $e) {
            // Si le token n'est pas valide
            return response()->json(['message' => 'Invalid token: ' . $e->getMessage()], 401);

        } catch (RevokedIdToken $e) {
            // Si le token a été révoqué
            return response()->json(['message' => 'Token has been revoked: ' . $e->getMessage()], 401);

        } catch (\Exception $e) {
            // Pour toute autre exception
            return response()->json(['message' => 'Authentication failed: ' . $e->getMessage()], 401);
        }
    }
    // protected $firebaseAuth;

    // public function __construct(FirebaseAuth $firebaseAuth)
    // {
    //     $this->firebaseAuth = $firebaseAuth;
    // }

    // public function loginFirebase(Request $request)
    // {
    //     $request->validate([
    //         'firebase_token' => 'required|string',
    //     ]);

    //     try {
    //         $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->firebase_token);
    //         $uid = $verifiedIdToken->claims()->get('sub');

    //         $user = $this->firebaseAuth->getUser($uid);

    //         // Optionally, store user details locally or return Firebase user info
    //         return response()->json(['user' => $user], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Authentication failed: ' . $e->getMessage()], 401);
    //     }
    // }
}
