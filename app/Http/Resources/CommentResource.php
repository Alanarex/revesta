<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes' => [
                'count' => $this->likes->count(),
                'is_liked' => Auth::check() ? $this->likes()->where('user_id', Auth::id())->exists() : false,
                'can_like' => Auth::check(),
            ],
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
