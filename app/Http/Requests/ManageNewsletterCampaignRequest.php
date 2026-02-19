<?php

namespace App\Http\Requests;

use App\Models\NewsletterCampaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ManageNewsletterCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get campaign from route parameter
        $campaign = $this->route('campaign');

        if (! $campaign) {
            return Gate::allows('viewAny', NewsletterCampaign::class);
        }

        // For specific campaign actions (edit, show, destroy, etc.)
        return Gate::allows('update', $campaign);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [];
    }
}
