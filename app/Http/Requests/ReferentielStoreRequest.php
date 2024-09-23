<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferentielStoreRequest extends FormRequest
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
            'codeReferentiel' => 'required|string|unique:referentiels,codeReferentiel',
            'libelleReferentiel' => 'required|string|unique:referentiels,libelleReferentiel',
            'StatutReferentiel' => 'required|in:Actif,Inactif,Archiver',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'Description' => 'nullable|string',
            'competences' => 'nullable|array',
            'competences.*.nom' => 'required_with:competences|string',
            'competences.*.description' => 'nullable|string',
            'competences.*.duree_acquisition' => 'nullable|integer',
            'competences.*.type' => 'required_with:competences|in:Back-end,Front-end',
            'competences.*.modules' => 'nullable|array',
            'competences.*.modules.*.nom' => 'required_with:competences.*.modules|string',
            'competences.*.modules.*.description' => 'nullable|string',
            'competences.*.modules.*.duree_acquisition' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'codeReferentiel.required' => 'Le code du référentiel est obligatoire.',
            'libelleReferentiel.required' => 'Le libellé du référentiel est obligatoire.',
            'StatutReferentiel.required' => 'Le statut du référentiel est obligatoire.',
            'StatutReferentiel.in' => 'Le statut doit être Actif, Inactif ou Archiver.',
            'photo.image' => 'La photo doit être une image valide.',
            'photo.mimes' => 'Les formats acceptés sont : jpeg, png, jpg, gif, svg.',
        ];
    }
}
