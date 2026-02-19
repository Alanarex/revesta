<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleBookmarkRequest;
use App\Models\Blog;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlogBookmarkController extends Controller
{
    /**
     * Blog interactions (bookmarks)
     *
     * @group Blogs
     */
    public function __construct(
        protected BookmarkService $bookmarkService
    ) {}

    /**
     * Toggle bookmark for a blog
     *
     * @group Blogs
     *
     * @authenticated
     */
    public function toggle(ToggleBookmarkRequest $request, Blog $blog): JsonResponse
    {
        try {
            $result = $this->bookmarkService->toggleBookmark(Auth::user(), $blog);

            return response()->json([
                'success' => true,
                'is_filled' => $result['bookmarked'],
                'bookmarked' => $result['bookmarked'],
                'message' => $result['bookmarked'] ? 'Signet ajouté!' : 'Signet retiré!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Bookmark toggle failed', [
                'user_id' => Auth::id(),
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
            ], 500);
        }
    }
}
