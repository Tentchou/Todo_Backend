<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'][3] = Rule::unique('tags')->where(function ($query) {
                return $query->where('user_id', auth()->id());
            })->ignore($this->route('tag'));
        }

        return $rules;
    }
}
