<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ToggleUserActiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');
        if (!auth()->check()) {
            return false;
        }

        // allow admins or the owner themselves
        $me = auth()->user();
        return ($me->isAdmin() ?? false) || ($target && $me->id === $target->id);
    }

    public function rules(): array
    {
        return [];
    }
}
