<?php

namespace App\Http\Requests;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SearchBlogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage', Blog::class);
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
                'regex:/^[a-zA-Z0-9\s\-_\.\,\!\?\:\;\'\"]+$/', // Allow alphanumeric, spaces, and common punctuation
            ],
            'author' => [
                'nullable',
                'integer',
                'exists:users,id',
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
            'author.exists' => 'L\'auteur sélectionné n\'existe pas.',
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
     * Get the author ID filter.
     */
    public function getAuthorId(): ?int
    {
        return $this->input('author');
    }

    /**
     * Get the page number.
     */
    public function getPage(): int
    {
        return $this->input('page', 1);
    }
}