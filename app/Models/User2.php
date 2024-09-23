<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User2 extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'users2';  // Nom explicite de la table

    protected $fillable = [
        'nom',
        'prenom',
        'adresse',
        'telephone',
        'email',
        'password',
        'photo',
        'statut',
        'role',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    // Scope pour filtrer par rôle
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

}
