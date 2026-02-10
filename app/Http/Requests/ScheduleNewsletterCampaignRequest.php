<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ScheduleNewsletterCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('campaign'));
    }

    /**
     * Prepare the data for validation.
     * Combines scheduled_date and scheduled_time into scheduled_at.
     */
    protected function prepareForValidation(): void
    {
        $date = $this->input('scheduled_date');
        $time = $this->input('scheduled_time');

        if ($date && $time) {
            $this->merge([
                'scheduled_at' => "$date $time:00",
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s|after:now',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'scheduled_date.required' => 'La date d\'envoi est requise.',
            'scheduled_date.date_format' => 'La date doit être au format : YYYY-MM-DD.',
            'scheduled_date.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur.',
            'scheduled_time.required' => 'L\'heure est requise.',
            'scheduled_time.date_format' => 'L\'heure doit être au format : HH:MM.',
            'scheduled_at.required' => 'La date et l\'heure programmées sont requises.',
            'scheduled_at.date_format' => 'La date programmée doit être au format : YYYY-MM-DD HH:MM:SS.',
            'scheduled_at.after' => 'La date et l\'heure programmées doivent être dans le futur.',
        ];
    }
}
