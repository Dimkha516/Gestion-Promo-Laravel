<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apprenant extends Model
{
    use HasFactory;

    protected $table = 'apprenants';
    protected $fillable = [
        // 'user_id',
        'nom_tuteur',
        'contact_tuteur',
        'photocopie_cni',
        'diplome',
        'visite_medicale',
        'extrait_naissance',
        'casier_judiciaire',
        // 'referentiel_id',
        'moyenne',
        'appreciation',
        'notes',
    ];



    public function referentiel()
    {
        return $this->belongsTo(Referentiel::class, 'referentiel_id');
    }
}
