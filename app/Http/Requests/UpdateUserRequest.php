<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($userId ? ','.$userId : '')],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'civil_status' => ['nullable', 'string', 'in:'.implode(',', array_keys(config('users.civil_statuses')))],
            'family_status' => ['nullable', 'string', 'in:'.implode(',', array_keys(config('users.family_statuses')))],
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
            'phone.max' => 'Le telephone ne doit pas depasser 50 caracteres.',
            'role_id.required' => 'Le role est obligatoire.',
            'role_id.integer' => 'Le role selectionne est invalide.',
            'role_id.exists' => 'Le role selectionne est introuvable.',
            'bio.string' => 'La biographie doit etre une chaine de caracteres.',
            'bio.max' => 'La biographie ne doit pas depasser 5000 caracteres.',
            'civil_status.string' => 'Le statut civil doit etre une chaine de caracteres.',
            'civil_status.in' => 'Le statut civil selectionne est invalide.',
            'family_status.string' => 'Le statut familial doit etre une chaine de caracteres.',
            'family_status.in' => 'Le statut familial selectionne est invalide.',
        ];
    }
}
