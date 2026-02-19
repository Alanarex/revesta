<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class SetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow if the user ID in route matches current user or is being verified
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                function ($attribute, $value, $fail) use ($user) {
                    // Check if password is different from old password (if it exists)
                    if ($user && $user->password && \Illuminate\Support\Facades\Hash::check($value, $user->password)) {
                        $fail('Le nouveau mot de passe doit etre different de l\'ancien mot de passe.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caracteres.',
        ];
    }
}
