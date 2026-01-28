<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleLikeRequest;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogLikeController extends Controller
{
    /**
     * Blog interactions (likes)
     *
     * @group Blogs
     */
    public function __construct(
        protected LikeService $likeService
    ) {
    }

    /**
     * Toggle like for a blog or comment
     *
     * @group Blogs
     * @authenticated
     * @bodyParam likeable_id integer required ID of the resource to like/unlike. Example: 123
     * @bodyParam likeable_type string required Fully-qualified model class name. Example: App\\Models\\Blog
     */
    public function toggle(ToggleLikeRequest $request, Blog $blog): JsonResponse
    {
        $validated = $request->validated();

        // For blog likes, use the already-resolved route model `$blog` to avoid
        // an extra select; for comments, find that comment. The service will
        // perform efficient DB-level operations and return the up-to-date count.
        if ($validated['likeable_type'] === Blog::class) {
            $likeable = $blog;
        } else {
            $likeable = BlogComment::findOrFail($validated['likeable_id']);
        }

        $result = $this->likeService->toggleLike(Auth::user(), $likeable);

        return response()->json([
            'success' => true,
            'liked' => $result['liked'],
            'likes_count' => $result['count']
        ]);
    }
}
