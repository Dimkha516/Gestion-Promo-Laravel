<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoStoreRequest extends FormRequest
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
            'libelle' => 'required|unique:promos',  // Vérifier que le libellé est unique
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut', // Optionnel, mais doit être après date_debut si fourni
            'duree' => 'nullable|numeric', // Optionnel si la date_fin est fournie
            'referentiels' => 'nullable|array',  // Facultatif, liste de référentiels
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Facultatif, lien vers la photo de couverture
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé de la promo est obligatoire',
            'libelle.unique' => 'Le libellé de la promotion doit être unique.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'photo.image' => 'La photo doit être une image valide.',
            'photo.mimes' => 'Les formats acceptés sont : jpeg, png, jpg, gif, svg.',
        ];
    }
}
