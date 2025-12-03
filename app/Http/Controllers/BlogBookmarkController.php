<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToggleBookmarkRequest;
use App\Models\Blog;
use App\Services\BookmarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogBookmarkController extends Controller
{
    public function __construct(
        protected BookmarkService $bookmarkService
    ) {}

    public function toggle(ToggleBookmarkRequest $request)
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
