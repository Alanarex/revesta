<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
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
        $user = auth()->user();
        
        return [
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed',
                function ($attribute, $value, $fail) use ($user) {
                    // Check if new password is different from current password
                    if (Hash::check($value, $user->password)) {
                        $fail('Le nouveau mot de passe doit etre different de l\'ancien mot de passe.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.string' => 'Le mot de passe doit etre une chaine de caracteres.',
            'new_password.min' => 'Le mot de passe doit contenir au moins 8 caracteres.',
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure confirmation field is present to work with 'confirmed' rule
        $this->merge([
            'new_password_confirmation' => $this->input('new_password_confirmation'),
        ]);
    }
}

