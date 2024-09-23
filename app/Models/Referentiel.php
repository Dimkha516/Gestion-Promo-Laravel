<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referentiel extends Model
{
    use HasFactory;


    protected $table = 'referentiels';
    protected $fillable = [
        'codeReferentiel',
        'libelleReferentiel',
        'StatutReferentiel',
        'photo',
        'description',
    ];

    // Scope pour filtrer par statut
    public function scopeByStatut($query, $statut = 'Actif')
    {
        return $query->where('StatutReferentiel', $statut);
    }

    // Scope pour filtrer les compétences d'un référentiel
    public function scopeByCompetence($query, $competenceName)
    {
        return $query->whereJsonContains('competences', [
            'nom' => $competenceName
        ]);
    }

    // Scope pour filtrer les modules d'un référentiel
    public function scopeByModule($query, $moduleName)
    {
        return $query->whereJsonContains('modules', [
            'nom' => $moduleName
        ]);
    }

    public function apprenants()
    {
        return $this->hasMany(Apprenant::class, 'referentiel_id');
    }

}
