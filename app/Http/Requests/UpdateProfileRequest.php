<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Normalize incoming data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => is_string($this->first_name) ? trim($this->first_name) : $this->first_name,
            'last_name'  => is_string($this->last_name) ? trim($this->last_name) : $this->last_name,
            'email'      => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
            'phone'      => is_string($this->phone) ? preg_replace('/\s+/', '', $this->phone) : $this->phone,
            'civil_status' => $this->civil_status === '' ? null : $this->civil_status,
            'family_status' => $this->family_status === '' ? null : $this->family_status,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'min:6', 'max:20'],
            'civil_status' => ['nullable', 'string', Rule::in(['monsieur', 'madame'])],
            'family_status' => ['nullable', 'string', Rule::in(['married', 'civil_partnership', 'divorced', 'separated', 'single', 'widowed'])],
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prenom est obligatoire.',
            'first_name.string' => 'Le prenom doit etre une chaine de caracteres.',
            'first_name.max' => 'Le prenom ne doit pas depasser 255 caracteres.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.string' => 'Le nom doit etre une chaine de caracteres.',
            'last_name.max' => 'Le nom ne doit pas depasser 255 caracteres.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit etre une adresse valide.',
            'email.max' => 'L\'email ne doit pas depasser 255 caracteres.',
            'email.unique' => 'Cet email est deja utilise.',
            'phone.string' => 'Le telephone doit etre une chaine de caracteres.',
            'phone.min' => 'Le telephone doit contenir au moins 6 caracteres.',
            'phone.max' => 'Le telephone ne doit pas depasser 20 caracteres.',
            'civil_status.string' => 'Le statut civil doit etre une chaine de caracteres.',
            'civil_status.in' => 'Le statut civil selectionne est invalide.',
            'family_status.string' => 'Le statut familial doit etre une chaine de caracteres.',
            'family_status.in' => 'Le statut familial selectionne est invalide.',
            'bio.string' => 'La biographie doit etre une chaine de caracteres.',
            'bio.max' => 'La biographie ne doit pas depasser 500 caracteres.',
        ];
    }
}
