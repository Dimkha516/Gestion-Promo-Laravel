<?php

namespace App\Services\Database;

use Kreait\Firebase\Factory;

class FirebaseService implements DatabaseServiceInterface
{
    protected $database;
    protected $auth;


    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->withDatabaseUri(config('services.firebase.database_url'));
        $this->auth = $firebase->createAuth();  // Utilisation de l'authentification Firebase
        $this->database = $firebase->createDatabase();
    }

    public function createUser(array $data)
    {
        try {
            // Création de l'utilisateur avec Firebase Auth
            $user = $this->auth->createUserWithEmailAndPassword($data['email'], $data['password']);
            if ($user) {
                // Ajouter les informations supplémentaires dans la Realtime Database
                $this->database->getReference('users/' . $user->uid)->set([
                    'nom' => $data["nom"] ?? null,
                    'prenom' => $data["prenom"] ?? null,
                    'adresse' => $data["adresse"] ?? null,
                    'telephone' => $data["telephone"] ?? null,
                    'email' => $data['email'],
                    'photo' => $data["photo"] ?? null,
                    'statut' => $data["statut"] ?? null,
                    'role' => $data["role"] ?? null,
                ]);
                return $user; // Retourne l'utilisateur créé, y compris l'UID

                // return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
            }


        } catch (\Exception $e) {
            // Gérer l'erreur en cas d'échec de création d'utilisateur
            return response()->json(['message' => 'User creation failed: ' . $e->getMessage()], 400);
        }
    }

    public function listUsers()
    {
        return $this->database->getReference('users')->getValue();
    }

    public function updateUser($userId, $data)
    {
        // Récupérer l'utilisateur dans Firebase
        $firebaseUser = $this->auth->getUser($userId);

        // Mettre à jour l'utilisateur dans Firebase (email, photo, etc.)
        $this->auth->updateUser($firebaseUser->uid, [
            'email' => $data['email'] ?? $firebaseUser->email,
            // 'nom' => $data['nom'] ?? $firebaseUser->displayName,
            // 'photoUrl' => $data['photo'] ?? $firebaseUser->photoUrl,
            // Ajoutez d'autres champs si nécessaire
        ]);

        return $firebaseUser;
    }
}

/**
 * eyJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsInByb2plY3RfaWQiOiAiZ2VzdGlvbi1wcmVzZW5j
ZXMtbGFyYXZlbCIsInByaXZhdGVfa2V5X2lkIjogIjM4NDQxZDhmYmY2NDUyNGQ5NTliZjZkZTg1
NmI3Yzc4MWQxOTljMTAiLCJwcml2YXRlX2tleSI6ICItLS0tLUJFR0lOIFBSSVZBVEUgS0VZLS0t
LS1cbk1JSUV2UUlCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQktjd2dnU2pBZ0VBQW9JQkFRREl5
Z3N0L1NycndYdGJcbk41ZGpYV01CcnhWWGNyL011NmtoUzFJcnFkWG5GUWNEOTQyL3QvczRPTk5T
VlRVYUh1RjQ5bHBGUnYxbG1KSzFcbkZUS3hBL2dmdVpQaU5hbFVsS0Zsc0ltcnQwVFpqZGJmeFM5
WmpaZ3M2YURQYjB5QVRudEZIUGp3MDhEaWNWWnZcblRUNytodENpYnBWc09TMEl0b0dRQUNyRUtl
OVVwb0tYems1SmZQajZlNTM4UVoxdG0rRUlmZEVQeXBrMkpRYmxcbm8ySFhJY3ZFY0MrOGtiUzVM
MzhTaVNTaVVwR3crWWlCaGlJMlJ1a2U1ZUdlck9qRkJrNGlPS3M2bElmZVpJRStcbjFYVUVvb05Q
enhaOXZTUytUckY4cEVMRXF3VFFLRDhxSEtYQVF6cDYvc3k0TC9jWmF0VWg2VmpMdGJBZmhCbDJc
bnlSUUIvUmliQWdNQkFBRUNnZ0VBQU1pbnk0UlVwdy96SUg0eWorem5rVFJJUStEMW9ReDRmV0h6
RGxXVU9WbmZcbjFFMmlsdHJXZ2JmWVoxYTFldHZLVmQ1aHFDbm9uUDFxcG9kUWZTMXBTZzBVNTk5
U3dSQkZlRnFuUEVMZVkyK3dcbno0c2xyZ0ZRcWUyTmpGSEFhRE1tT3dwV2FjWkIxMVhKbUd5aVJY
eHNQYjRWeE9nM2tKNzV4Q3RPcWhrSnowMmNcbnA5TU9JdmR1cytmUDFkOGhRc1BHOEQyeHpVQ2dW
bkJubWlZMldPR3NVTElraUZKR3Eya0dEL2FDUllBWVFlTzZcblRTdjIxUml6VzdodE80ZEZ5OE8y
M0ZBMG44cFpua3pzbGE2Z2lZMzYrcTM4TlAyY1d4YVczSUNZUlU2NWZsSXZcbjZqTFBaUnNEZWg0
MWUvSnV3dGsrU3pOb0FNaldCdVA0Z3R4ZkJjb201UUtCZ1FEMmk0cmNaZXRSS05kZllKY2Ncbkw0
NUdqMFFVamJXalpXanZReVBPK2pEb1ZRN214OEpZUmpOMFJxRjJnRkQxNy9qcEQ0Y3JCdWF0Sktp
OHF5MitcbkpyMk5DNTZLWURkZll1SHRJSGRCaWdKNkFDb09nNVEvWUdBcmdMQVcvenhKK2tmc0w5
d3hDQk56RXB0VldmU0Zcbi8xSlkrNWlLUEdpQlNCUGpyWnFZVzFVd1BRS0JnUURRZlVyOUg5OWV4
RWp4dFRlaGlSQm03QUFuOXVXQmdmRHJcbk4vS204dTliZWtNMEpUTWFEWTcvb3M2WW5BMkdiQ0R6
N3lVbko5YUY4QjBycXNjT09yV0loaHZHZHYzYW5WS3BcbmhybGYwMytsNjNMSFN1VUJXbEZPQmRa
ZzUwcm4wYkZrR21HTS9IMC9MLy9NbWRxcnNUQ0dZUHkzQlRTd1hiSFpcbjVQdm1aSnJodHdLQmdR
QzY5cmxZU0FYNnB4RWtxQmdQN05SbEY5SVdFQnoxaXpyWGJyL2gvd05GdlBNdStiK2NcbnQ2ZmZm
b3lGVSt1UmhlanB2TGZSWGt0aWRCRnQraFlYUkVPc2lXb3lINkwrTi91Q21vTTFkVlpKZElSd08w
SWFcbkNaa0VoSE9GRFF1SVFqOTJraGpXS2Z5dDRDZGh3M1RXaFBmS3VRbEZrMmZnMm1zUzFwME0r
djRRTlFLQmdFaXNcbjlEL3FKVllHZkJydGZaZnNqSEFrSWlYTU5kS0FOamY0UjdpVWhJVlJ5QzFj
TGtVTnB1UkxuMUtwU3ptcFpZOUNcbnRLUENpbEFrRkRjTmo5ZlE5VWpDM3RtK3p0eXU3SXExc05i
TGhmcVRhVzQ1R0R6eGU5Z0R4dWYrbUNqWStzb1hcblBCd0dRNjZNRDlJRmE2bGYyYmR1QlluUzRi
djgzU3RFVzk3REFRNWZBb0dBSG9IWTZSajJFYUtRRXFIVGFDZlVcbi93ZW1ENldxUFMrcEtEVHBC
eGVQN0plWWZMMGVQZHZzNlcyemtNZURjNTZhVG0yZHJYQU1MWjNZZk9sTE9oZ0VcbjR6WE5QdTVC
blFmL0RMbGpWK3A3UkFBT3EyMzlXTExOVFZWSnhDcWE0Y2Z3OHlOME5lc1ZSU2MxY29Na3Y5VmFc
bkxZL1VLS3FKV3Zhb285eUZKQ0RGRFZrPVxuLS0tLS1FTkQgUFJJVkFURSBLRVktLS0tLVxuIiwi
Y2xpZW50X2VtYWlsIjogImZpcmViYXNlLWFkbWluc2RrLXVuYTJmQGdlc3Rpb24tcHJlc2VuY2Vz
LWxhcmF2ZWwuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLCJjbGllbnRfaWQiOiAiMTA3ODI2Mjk3
MDU2OTQ5MDgzODcwIiwiYXV0aF91cmkiOiAiaHR0cHM6Ly9hY2NvdW50cy5nb29nbGUuY29tL28v
b2F1dGgyL2F1dGgiLCJ0b2tlbl91cmkiOiAiaHR0cHM6Ly9vYXV0aDIuZ29vZ2xlYXBpcy5jb20v
dG9rZW4iLCJhdXRoX3Byb3ZpZGVyX3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xl
YXBpcy5jb20vb2F1dGgyL3YxL2NlcnRzIiwiY2xpZW50X3g1MDlfY2VydF91cmwiOiAiaHR0cHM6
Ly93d3cuZ29vZ2xlYXBpcy5jb20vcm9ib3QvdjEvbWV0YWRhdGEveDUwOS9maXJlYmFzZS1hZG1p
bnNkay11bmEyZiU0MGdlc3Rpb24tcHJlc2VuY2VzLWxhcmF2ZWwuaWFtLmdzZXJ2aWNlYWNjb3Vu
 * 
 * 
 */
