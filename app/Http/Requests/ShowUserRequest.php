<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ShowUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('view', $this->route('user'));
    }

    public function rules(): array
    {
        return [];
    }
}
