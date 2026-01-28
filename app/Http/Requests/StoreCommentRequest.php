<?php

namespace App\Http\Requests;

use App\Models\BlogComment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', BlogComment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id',
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
            'content.required' => 'Le contenu du commentaire est obligatoire.',
            'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'parent_id.exists' => 'Le commentaire parent n\'existe pas.',
        ];
    }

    /**
     * Provide body parameter definitions for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'content' => [
                'description' => 'The text content of the comment.',
                'type' => 'string',
                'required' => true,
                'example' => 'Great article — thanks for sharing!',
            ],
            'parent_id' => [
                'description' => 'Optional parent comment ID for nested replies.',
                'type' => 'integer',
                'required' => false,
                'example' => null,
            ],
        ];
    }
}
