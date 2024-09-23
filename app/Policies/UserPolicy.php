<?php

namespace App\Policies;

use App\Models\User2;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User2 $user, User2 $newUser)
    {
        // Un Admin peut créer un Admin, Coach, Manager, CM ou Apprenant
        if ($user->role === 'Admin') {
            return in_array($newUser->role, ['Admin', 'Coach', 'Manager', 'CM', 'Apprenant']);
        }

        // Un Manager peut créer un Coach, Manager, CM ou Apprenant
        if ($user->role === 'Manager') {
            return in_array($newUser->role, ['Coach', 'Manager', 'CM', 'Apprenant']);
        }

        // Un CM ne peut créer que des Apprenants
        if ($user->role === 'CM') {
            return $newUser->role === 'Apprenant';
        }

        // Les autres rôles ne peuvent pas créer d'utilisateurs
        return false;
    }


}
