<?php

namespace App\Http\Requests;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Blog::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,published',
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
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'short_description.required' => 'La description courte est obligatoire.',
            'short_description.max' => 'La description ne peut pas dépasser 500 caractères.',
            'content.required' => 'Le contenu est obligatoire.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être soit brouillon, soit en attente, soit publié.',
        ];
    }
}
