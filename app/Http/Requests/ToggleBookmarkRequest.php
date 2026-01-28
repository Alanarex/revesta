<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleBookmarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $blogId = $this->input('blog_id');
        $blog = \App\Models\Blog::find($blogId);

        return $blog && \Gate::allows('bookmark', $blog);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blog_id' => 'required|exists:blogs,id',
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
            'blog_id.required' => 'L\'ID du blog est obligatoire.',
            'blog_id.exists' => 'Le blog n\'existe pas.',
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
            'blog_id' => [
                'description' => 'ID of the blog to toggle bookmark for.',
                'type' => 'integer',
                'required' => true,
                'example' => 42,
            ],
        ];
    }
}
