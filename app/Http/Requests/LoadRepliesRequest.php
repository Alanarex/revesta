<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadRepliesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for query parameters.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:50',
            'level' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Query parameter definitions for Scribe.
     *
     * @return array<string, array<string, mixed>>
     */
    public function queryParameters(): array
    {
        return [
            'offset' => [
                'description' => 'Offset for pagination (number of items to skip).',
                'type' => 'integer',
                'required' => false,
                'example' => 0,
            ],
            'limit' => [
                'description' => 'Maximum number of replies to return.',
                'type' => 'integer',
                'required' => false,
                'example' => 2,
            ],
            'level' => [
                'description' => 'Nesting level for rendered HTML (used by the client).',
                'type' => 'integer',
                'required' => false,
                'example' => 1,
            ],
        ];
    }
}
