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
}
