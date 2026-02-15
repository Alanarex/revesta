<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
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
     * Like or unlike a blog or comment
     *
     * @group Blogs
     * @authenticated
     * @urlParam model string required The model type (Blog or BlogComment). Example: Blog
     * @urlParam modelId integer required The ID of the model to like/unlike. Example: 123
     */
    public function like(Blog $blog, string $model, string $modelId): JsonResponse
    {
        try {
            $modelId = (int) $modelId;

            // Resolve the likeable model based on the model parameter
            if ($model === 'Blog') {
                $likeable = $blog;
            } elseif ($model === 'BlogComment') {
                $likeable = BlogComment::findOrFail($modelId);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue'
                ], 400);
            }

            $result = $this->likeService->like(Auth::user(), $likeable);

            return response()->json([
                'success' => true,
                'is_filled' => $result['liked'],
                'likes_count' => $result['count']
            ]);
        } catch (\Exception $e) {
            \Log::error('Like action failed', [
                'user_id' => Auth::id(),
                'blog_id' => $blog->id,
                'model' => $model,
                'modelId' => $modelId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue'
            ], 500);
        }
    }
}
