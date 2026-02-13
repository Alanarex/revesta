<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');
        if (!auth()->check()) {
            return false;
        }

        $me = auth()->user();
        // allow admins or the owner to reset their password
        return ($me->isAdmin() ?? false) || ($target && $me->id === $target->id);
    }

    public function rules(): array
    {
        $user = $this->route('user');
        
        return [
            'password' => [
                'required', 
                'string', 
                'min:8',
                function ($attribute, $value, $fail) use ($user) {
                    // Check if password is different from old password
                    if ($user && $user->password && Hash::check($value, $user->password)) {
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
            'password.string' => 'Le mot de passe doit etre une chaine de caracteres.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caracteres.',
        ];
    }
}

