<?php

namespace App\Http\Requests;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SearchPublicBlogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Blog::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.\,\!\?\:\;\'\"À-ÿ]+$/u', // Allow alphanumeric, spaces, common punctuation, and accented characters
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.regex' => 'La recherche contient des caractères non autorisés.',
            'search.max' => 'La recherche ne peut pas dépasser 255 caractères.',
            'page.min' => 'Le numéro de page doit être supérieur à 0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize search input
        if ($this->has('search') && !empty($this->search)) {
            $sanitized = strip_tags($this->search); // Remove HTML tags
            $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8'); // Escape special characters
            $sanitized = trim($sanitized); // Remove leading/trailing whitespace

            $this->merge([
                'search' => $sanitized,
            ]);
        }
    }

    /**
     * Get the sanitized search term.
     */
    public function getSearchTerm(): ?string
    {
        return $this->input('search');
    }

    /**
     * Get the page number.
     */
    public function getPage(): int
    {
        return $this->input('page', 1);
    }

    /**
     * Provide body parameter definitions for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'search' => [
                'description' => 'Optional search term to filter public blogs.',
                'type' => 'string',
                'required' => false,
                'example' => 'housing',
            ],
            'page' => [
                'description' => 'Page number for pagination.',
                'type' => 'integer',
                'required' => false,
                'example' => 1,
            ],
        ];
    }
}