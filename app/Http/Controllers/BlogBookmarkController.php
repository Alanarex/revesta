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
        * @authenticated
     * @bodyParam blog_id integer required ID of the blog to toggle bookmark for. Example: 42
     */
    public function toggle(ToggleBookmarkRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $blog = Blog::findOrFail($validated['blog_id']);

        $result = $this->bookmarkService->toggleBookmark(Auth::user(), $blog);

        return response()->json([
            'success' => true,
            'bookmarked' => $result['bookmarked'],
            'message' => $result['bookmarked'] ? 'Signet ajouté!' : 'Signet retiré!'
        ]);
    }
}
