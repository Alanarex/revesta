<?php

namespace App\Http\Requests;

use App\Models\Simulation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexSimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && Gate::allows('viewAny', Simulation::class);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:6', 'max:60'],
        ];
    }
}
