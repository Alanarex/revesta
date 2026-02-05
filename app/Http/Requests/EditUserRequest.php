<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', \App\Models\User::class);

    }

    public function rules(): array
    {
        return [];
    }
}
