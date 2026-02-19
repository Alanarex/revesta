<?php

namespace App\Http\Requests;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;

class ToggleBookmarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        // Use the route-bound blog model only (ID is passed in the URL as {blog})
        $blog = $this->route('blog');

        if (! $blog || ! ($blog instanceof Blog)) {
            return false;
        }

        return \Gate::allows('bookmark', $blog);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Provide body parameter definitions for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'blog_id' => [
                'description' => 'ID of the blog to toggle bookmark for. This is passed as the `{blog}` route parameter (URL), not in the request body.',
                'type' => 'integer',
                'required' => false,
                'example' => 42,
            ],
        ];
    }
}
