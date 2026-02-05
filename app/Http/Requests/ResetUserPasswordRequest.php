<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
        return [
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
