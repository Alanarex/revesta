<?php

namespace App\Http\Requests;

use App\Models\Simulation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ShowSimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $simulation = $this->route('simulation');

        return auth()->check()
            && $simulation instanceof Simulation
            && Gate::allows('view', $simulation);
    }

    public function rules(): array
    {
        return [];
    }
}
