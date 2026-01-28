<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Blog;
use App\Models\BlogComment;

class ToggleLikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Add conditional validation: existence of the selected model and permission check.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('likeable_type');
            $id = $this->input('likeable_id');

            if ($type === Blog::class) {
                $model = Blog::find($id);
                if (!$model) {
                    $validator->errors()->add('likeable_id', 'The specified blog does not exist.');
                    return;
                }

                if (!\Gate::allows('like', $model)) {
                    $validator->errors()->add('likeable', 'This action is unauthorized.');
                    return;
                }
            }

            if ($type === BlogComment::class) {
                $model = BlogComment::find($id);
                if (!$model) {
                    $validator->errors()->add('likeable_id', 'The specified comment does not exist.');
                    return;
                }

                if (!\Gate::allows('like', $model)) {
                    $validator->errors()->add('likeable', 'This action is unauthorized.');
                    return;
                }
            }
        });
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
