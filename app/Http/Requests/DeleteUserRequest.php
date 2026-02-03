<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');
        if (!auth()->check()) {
            return false;
        }

        $me = auth()->user();
        // allow admins or the owner to delete their account
        return ($me->isAdmin() ?? false) || ($target && $me->id === $target->id);
    }

    public function rules(): array
    {
        return [];
    }
}
