<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprenantStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           // Règles pour le compte utilisateur
           'nom' => 'required|string|max:255',
           'prenom' => 'required|string|max:255',
           'adresse' => 'required|string|max:255',
           'telephone' => [
               'required',
               'string',
               'unique:users,telephone',
               'regex:/^((77|76|75|70|78)\d{3}\d{2}\d{2})|(33[8]\d{2}\d{2}\d{2})$/',
           ],
        //    'role' => 'required|in:Apprenant',
           'email' => 'required|email|max:255|unique:users,email',
           'password' => 'required|string|min:6',
           'photo' => 'nullable|image|max:1024',
           'statut' => 'required|in:actif,bloqué',

           // Règles spécifiques à l'apprenant
           'nom_tuteur' => 'required|string|max:255',
           'contact_tuteur' => 'required|string|max:255',
           'photocopie_cni' => 'required|file|mimes:jpeg,png,pdf|max:2048',
           'diplome' => 'required|file|mimes:jpeg,png,pdf|max:2048',
           'visite_medicale' => 'required|file|mimes:jpeg,png,pdf|max:2048',
           'extrait_naissance' => 'required|file|mimes:jpeg,png,pdf|max:2048',
           'casier_judiciaire' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        //    'promo_uid' => 'required|string|exists:promos,uid',  // Référence à la promo
        //    'referentiel_uid' => 'required|string|exists:referentiels,uid',  // Référence au référentiel
        ];
    }

    public function messages()
    {
        return [
            'tuteurNom.required' => 'Le nom du tuteur est obligatoire.',
            'tuteurContact.required' => 'Le contact du tuteur est obligatoire.',
            'cni.required' => 'La photocopie de la CNI est obligatoire.',
            'cni.mimes' => 'La CNI doit être un fichier de type: pdf, jpeg, png.',
            'diplome.required' => 'Le diplôme est obligatoire.',
            'diplome.mimes' => 'Le diplôme doit être un fichier de type: pdf, jpeg, png.',
            'referentiel_id.exists' => 'Le référentiel spécifié n\'existe pas.',
        ];
    }
}
