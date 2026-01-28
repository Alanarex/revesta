<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoadCommentsRequest;
use App\Http\Requests\LoadRepliesRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BlogCommentController extends Controller
{
    /**
     * Blog interactions (comments)
     *
     * @group Blogs
     */
    public function __construct(
        protected CommentService $commentService
    ) {
    }

    /**
     * Store a comment for a blog
     *
     * @group Blogs
     * @authenticated
     * @bodyParam content string required The text content of the comment. Example: Great article!
     * @bodyParam parent_id integer nullable Optional parent comment id for replies.
     */
    public function store(StoreCommentRequest $request, Blog $blog): JsonResponse
    {
        $validated = $request->validated();

        $comment = $this->commentService->createComment(
            Auth::user(),
            $blog,
            $validated['content'],
            $validated['parent_id'] ?? null
        );

        // Render the single comment HTML using the existing partial to keep markup
        // consistent with server-rendered comments. Determine the level: top-level
        // comments are level 0; replies are level 1 (client may increase further).
        $level = (isset($validated['parent_id']) && $validated['parent_id']) ? 1 : 0;

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté!',
            'comment' => $comment,
            'level' => $level
        ]);
    }

    /**
     * Load more comments (pagination)
     *
     * @group Blogs
     * @authenticated
     */
    public function loadMore(Blog $blog, LoadCommentsRequest $request): JsonResponse
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
     * Load more replies for a specific comment (public access)
     *
     * @group Blogs
     */
    public function loadMoreReplies(Blog $blog, BlogComment $comment, LoadRepliesRequest $request): JsonResponse
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

    /**
     * Delete a comment
     *
     * @authenticated
     * @group Blogs
     * @param BlogComment $comment
     * @return JsonResponse
     * @bodyParam comment integer required ID of the comment to delete. Example: 456
     */
    public function destroy(BlogComment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $this->commentService->deleteComment($comment);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé!'
        ]);
    }
}
