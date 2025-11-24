<?php

namespace App\Http\Controllers;

use App\Models\BlogBookmark;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;
        $bookmarks = $request->user()
            ->blogBookmarks()
            ->with(['blog.user'])
            ->latest()
            ->paginate($perPage);

        return view('bookmarks.index', compact('bookmarks'));
    }

    public function destroy(Request $request, BlogBookmark $bookmark)
    {
        // ensure the bookmark belongs to the current user
        if ($bookmark->user_id !== $request->user()->id) {
            abort(403);
        }

        $bookmark->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Bookmark removed']);
        }

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark removed');
    }
}
