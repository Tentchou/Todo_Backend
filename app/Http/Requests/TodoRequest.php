<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'integer', Rule::in([1, 2, 3])], // 1: Low, 2: Medium, 3: High
            'is_completed' => ['boolean'],
            'category_id' => [
                'nullable',
                'exists:categories,id',
                // Vérifie que la catégorie appartient à l'utilisateur courant
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => [
                'integer',
                'exists:tags,id',
                // Vérifie que chaque tag appartient à l'utilisateur courant
                Rule::exists('tags', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
        ];
    }
}
