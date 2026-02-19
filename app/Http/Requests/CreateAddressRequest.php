<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Address::class);
    }

    /**
     * Get the validation rules that apply to the request.
     * No validation needed - this request only shows the form.
     * Validation happens in StoreAddressRequest when data is actually submitted.
     */
    public function rules(): array
    {
        return [];
    }
}
