<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class BlogResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_badge_class' => $this->getStatusBadgeClass(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes' => [
                'count' => $this->likes->count(),
                'is_liked' => Auth::check() ? $this->likes()->where('user_id', Auth::id())->exists() : false,
                'can_like' => Auth::check() && $this->isPublished(),
            ],
            'bookmarks' => [
                'is_bookmarked' => Auth::check() ? $this->bookmarks()->where('user_id', Auth::id())->exists() : false,
                'can_bookmark' => Auth::check() && $this->isPublished(),
            ],
            'comments' => [
                'count' => $this->comments->count(),
                'can_comment' => Auth::check(),
                'list' => CommentResource::collection($this->whenLoaded('comments')),
            ],
        ];
    }
}
