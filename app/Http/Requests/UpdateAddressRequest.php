<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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

    public function messages(): array
    {
        return [
            'label.string' => 'Le libelle doit etre une chaine de caracteres.',
            'label.max' => 'Le libelle ne doit pas depasser 255 caracteres.',
            'street.required' => 'La rue est obligatoire.',
            'street.string' => 'La rue doit etre une chaine de caracteres.',
            'street.max' => 'La rue ne doit pas depasser 255 caracteres.',
            'number.string' => 'Le numero doit etre une chaine de caracteres.',
            'number.max' => 'Le numero ne doit pas depasser 50 caracteres.',
            'complement.string' => 'Le complement d\'adresse doit etre une chaine de caracteres.',
            'complement.max' => 'Le complement ne doit pas depasser 255 caracteres.',
            'postal_code.required' => 'Le code postal est obligatoire.',
            'postal_code.string' => 'Le code postal doit etre une chaine de caracteres.',
            'postal_code.max' => 'Le code postal ne doit pas depasser 10 caracteres.',
            'city.required' => 'La ville est obligatoire.',
            'city.string' => 'La ville doit etre une chaine de caracteres.',
            'city.max' => 'La ville ne doit pas depasser 255 caracteres.',
            'departement.string' => 'Le departement doit etre une chaine de caracteres.',
            'departement.max' => 'Le departement ne doit pas depasser 50 caracteres.',
            'insee_code.string' => 'Le code INSEE doit etre une chaine de caracteres.',
            'insee_code.max' => 'Le code INSEE ne doit pas depasser 10 caracteres.',
            'lat.numeric' => 'La latitude doit etre un nombre.',
            'lat.between' => 'La latitude doit etre entre -90 et 90.',
            'lng.numeric' => 'La longitude doit etre un nombre.',
            'lng.between' => 'La longitude doit etre entre -180 et 180.',
        ];
    }
}
