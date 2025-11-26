<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|in:App\Models\Blog,App\Models\BlogComment',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'likeable_id.required' => 'L\'ID de l\'élément est obligatoire.',
            'likeable_id.integer' => 'L\'ID doit être un nombre entier.',
            'likeable_type.required' => 'Le type d\'élément est obligatoire.',
            'likeable_type.in' => 'Type d\'élément invalide.',
        ];
    }
}
