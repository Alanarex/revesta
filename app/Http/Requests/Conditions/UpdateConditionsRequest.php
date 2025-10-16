<?php

namespace App\Http\Requests\Conditions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConditionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conditions' => ['required', 'array'],

            // condition data
            'conditions.*.*.aid_id' => ['required', 'integer', 'exists:aids,id'],
            'conditions.*.*.model' => ['required', 'string', 'in:user,housing,address,fiscal_income'],
            'conditions.*.*.attribute' => ['required', 'string'],
            'conditions.*.*.operator' => ['required', 'string', 'in:=,!=,<,<=,>,>='],
            'conditions.*.*.value' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'conditions.*.*.aid_id.required' => 'L\'ID de l\'aide est requis.',
            'conditions.*.*.aid_id.integer' => 'L\'ID de l\'aide doit être un entier.',
            'conditions.*.*.aid_id.exists' => 'L\'ID de l\'aide est invalide.',

            'conditions.*.*.model.required' => 'Le champ modèle est requis.',
            'conditions.*.*.model.in' => 'Le modèle sélectionné est invalide.',

            'conditions.*.*.attribute.required' => 'Le champ attribut est requis.',

            'conditions.*.*.operator.required' => 'Le champ opérateur est requis.',
            'conditions.*.*.operator.in' => 'L\'opérateur sélectionné est invalide.',
        ];
    }
}
