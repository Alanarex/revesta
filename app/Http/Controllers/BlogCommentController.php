<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BlogCommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    ) {}

    public function store(StoreCommentRequest $request, Blog $blog)
    {
        $validated = $request->validated();

        $comment = $this->commentService->createComment(
            Auth::user(), 
            $blog, 
            $validated['content'], 
            $validated['parent_id'] ?? null
        );

        $comment->load('user');

        // Render the single comment HTML using the existing partial to keep markup
        // consistent with server-rendered comments. Determine the level: top-level
        // comments are level 0; replies are level 1 (client may increase further).
        $level = $validated['parent_id'] ? 1 : 0;

        $html = view('admin.blogs.partials.comments', [
            'comments' => collect([$comment]),
            'level' => $level
        ])->render();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté!',
            'comment' => $comment,
            'html' => $html,
            'level' => $level
        ]);
    }

    public function loadMore(Blog $blog, Request $request)
    {
        $offset = $request->get('offset', 0);

        $result = $this->commentService->getComments($blog->id, $offset);

        return response()->json([
            'success' => true,
            'comments' => $result['comments'],
            'hasMore' => $result['hasMore']
        ]);
    }

    /**
     * Load more replies for a specific comment (public access).
     */
    public function loadMoreReplies(Blog $blog, BlogComment $comment, Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $limit = (int) $request->get('limit', 2);
        $level = (int) $request->get('level', 1);

        $result = $this->commentService->getReplies($comment->id, $offset, $limit);

        // Render the replies using the existing comments partial for consistency
        $html = view('admin.blogs.partials.comments', [
            'comments' => $result['replies'],
            'level' => $level
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $result['hasMore']
        ]);
    }

    public function destroy(BlogComment $comment)
    {
        Gate::authorize('delete', $comment);

        $this->commentService->deleteComment($comment);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé!'
        ]);
    }
}
