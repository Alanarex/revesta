<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:App\\Models\\User'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prenom est obligatoire.',
            'first_name.string' => 'Le prenom doit etre une chaine de caracteres.',
            'first_name.max' => 'Le prenom ne doit pas depasser 255 caracteres.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.string' => 'Le nom doit etre une chaine de caracteres.',
            'last_name.max' => 'Le nom ne doit pas depasser 255 caracteres.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit etre une adresse valide.',
            'email.max' => 'L\'email ne doit pas depasser 255 caracteres.',
            'email.unique' => 'Cet email est deja utilise.',
        ];
    }
}
