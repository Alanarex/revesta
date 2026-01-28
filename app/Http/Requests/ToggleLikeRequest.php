<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $likeableType = $this->input('likeable_type');
        $likeableId = $this->input('likeable_id');

        if ($likeableType === 'App\Models\Blog') {
            $blog = \App\Models\Blog::find($likeableId);
            return $blog && \Gate::allows('like', $blog);
        }

        if ($likeableType === 'App\Models\BlogComment') {
            $comment = \App\Models\BlogComment::find($likeableId);
            return $comment && \Gate::allows('like', $comment);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|in:App\Models\Blog,App\Models\BlogComment',
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
            'likeable_id.required' => 'L\'ID de l\'élément est obligatoire.',
            'likeable_id.integer' => 'L\'ID doit être un nombre entier.',
            'likeable_type.required' => 'Le type d\'élément est obligatoire.',
            'likeable_type.in' => 'Type d\'élément invalide.',
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
            'likeable_id' => [
                'description' => 'ID of the resource to like/unlike.',
                'type' => 'integer',
                'required' => true,
                'example' => 123,
            ],
            'likeable_type' => [
                'description' => 'Fully-qualified model class name of the likeable resource.',
                'type' => 'string',
                'required' => true,
                'example' => 'App\\Models\\Blog',
            ],
        ];
    }
}
