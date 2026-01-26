<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class BlogListResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->likes->count(),
            'comments_count' => $this->comments->count(),
            'is_liked' => Auth::check() ? $this->likes()->where('user_id', Auth::id())->exists() : false,
            'is_bookmarked' => Auth::check() ? $this->bookmarks()->where('user_id', Auth::id())->exists() : false,
        ];
    }
}
