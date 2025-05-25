<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls les utilisateurs authentifiés peuvent gérer les catégories
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                // La catégorie doit être unique pour l'utilisateur courant
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
        ];

        // Si c'est une mise à jour (PUT/PATCH), ignorez la catégorie actuelle pour l'unicité
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'][3] = Rule::unique('categories')->where(function ($query) {
                return $query->where('user_id', auth()->id());
            })->ignore($this->route('category'));
        }

        return $rules;
    }
}

