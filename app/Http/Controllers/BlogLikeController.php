<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleLikeRequest;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Services\LikeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogLikeController extends Controller
{
    public function __construct(
        protected LikeService $likeService
    ) {}

    public function toggle(ToggleLikeRequest $request)
    {
        $validated = $request->validated();

        // Find the likeable model and preload counts including whether the auth user
        // already liked it to avoid extra exists/count queries in the service.
        if ($validated['likeable_type'] === Blog::class) {
            $likeable = Blog::withCount([
                'likes',
                'likes as liked_by_auth' => function ($q) {
                    $q->where('user_id', Auth::id());
                }
            ])->findOrFail($validated['likeable_id']);
        } else {
            $likeable = BlogComment::withCount([
                'likes',
                'likes as liked_by_auth' => function ($q) {
                    $q->where('user_id', Auth::id());
                }
            ])->findOrFail($validated['likeable_id']);
        }

        $result = $this->likeService->toggleLike(Auth::user(), $likeable);

        return response()->json([
            'success' => true,
            'liked' => $result['liked'],
            'likes_count' => $result['count']
        ]);
    }
}
