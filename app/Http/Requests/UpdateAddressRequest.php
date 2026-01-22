<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Address;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('address'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $address = $this->route('address');

        return [
            'label' => 'sometimes|nullable|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:50',
            'complement' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'departement' => 'nullable|string|max:50',
            'insee_code' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ];
    }
}
