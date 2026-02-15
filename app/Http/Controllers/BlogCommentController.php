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
        try {
            $validated = $request->validated();

            $comment = $this->commentService->createComment(
                Auth::user(),
                $blog,
                $validated['content'],
                $validated['parent_id'] ?? null
            );

            $comment->load(['user', 'likes', 'replies']);
            $level = isset($validated['parent_id']) && $validated['parent_id'] ? 1 : 0;
            $html = view('admin.blogs.partials.comments.item', [
                'comment' => $comment,
                'level' => $level,
            ])->render();

            $response = [
                'success' => true,
                'message' => 'Commentaire ajouté!',
                'html' => $html,
                'insertType' => ($validated['parent_id'] ?? null) ? 'reply' : 'top-level',
                'targetId' => ($validated['parent_id'] ?? null) ?: null,
            ];

            // If this is a reply, send parent updated metadata
            if ($validated['parent_id'] ?? null) {
                $parentComment = BlogComment::find($validated['parent_id']);
                if ($parentComment) {
                    $parentComment->loadCount('replies');
                    $response['parentCommentId'] = $parentComment->id;
                    $response['parentRepliesCount'] = $parentComment->replies_count;
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Comment creation failed', [
                'user_id' => Auth::id(),
                'blog_id' => $blog->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue'
            ], 500);
        }
    }

    /**
     * Render reply form for a comment
     *
     * @group Blogs
     * @authenticated
     */
    public function replyForm(Blog $blog, BlogComment $comment): JsonResponse
    {
        $html = view('admin.blogs.partials.comments.form', [
            'blogId' => $blog->id,
            'parentId' => $comment->id,
            'placeholder' => 'Répondre...',
            'showAvatar' => true,
            'userInitials' => Auth::user()->initials,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
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
        try {
            $offset = (int) $request->get('offset', 0);
            $limit = (int) $request->get('limit', 2);
            $level = (int) $request->get('level', 1);

            $result = $this->commentService->getReplies($comment->id, $offset, $limit);

            // Render replies
            $html = view('admin.blogs.partials.comments.list', [
                'comments' => $result['replies'],
                'level' => $level
            ])->render();

            // Append load-more button if there are more replies
            if ($result['hasMore']) {
                $html .= view('admin.blogs.partials.comments.load-more-button', [
                    'commentId' => $comment->id,
                    'offset' => $offset + $limit,
                    'level' => $level,
                ])->render();
            }

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Exception $e) {
            \Log::error('Load more replies failed', [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id ?? null,
                'blog_id' => $blog->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue'
            ], 500);
        }
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
        try {
            Gate::authorize('delete', $comment);

            $parentId = $comment->parent_id;
            $parentComment = null;

            // If this is a reply, load the parent before deletion
            if ($parentId) {
                $parentComment = BlogComment::find($parentId);
            }

            $this->commentService->deleteComment($comment);

            $response = [
                'success' => true,
                'message' => 'Commentaire supprimé!'
            ];

            // If this was a reply, return updated parent HTML with new count
            if ($parentComment) {
                $parentComment->load(['user', 'likes', 'replies.user', 'replies.likes']);
                $parentComment->loadCount('replies');
                
                $parentHtml = view('admin.blogs.partials.comments.item', [
                    'comment' => $parentComment,
                    'level' => 0,
                ])->render();

                $response['updatedParentHtml'] = $parentHtml;
                $response['parentCommentId'] = $parentId;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Comment deletion failed', [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue'
            ], 500);
        }
    }
}
