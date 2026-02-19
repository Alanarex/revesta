<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return Gate::allows('delete', $target);
    }

    public function rules(): array
    {
        return [];
    }
}
