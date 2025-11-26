<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectBlogRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminBlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('manage', Blog::class);


    /**
     * Show a single blog for admin review.
     */
        $search = $request->input('search');
        $authorId = $request->input('author');

        $blogs = $this->blogService->getPendingBlogs(20, $search, $authorId);

        // Get all authors who have pending blogs for the filter dropdown
        $authors = $this->blogService->getAuthorsWithPendingBlogs();

        return view('admin.blogs.index', [
            'blogs' => $blogs,
            'authors' => $authors,
            'currentAuthor' => $authorId,
            'currentSearch' => $search,
            'title' => 'Gérer les blogs',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Administration', 'url' => '#'],
                ['label' => 'Gérer les blogs', 'url' => route('admin.blogs.index')],
            ],
        ]);
    }

    public function show(Blog $blog)
    {
        Gate::authorize('manage', Blog::class);

        $blog = $this->blogService->findBlog($blog->id);
        
        return view('admin.blogs.show', [
            'blog' => $blog,
            'title' => 'Réviser le blog',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Administration', 'url' => '#'],
                ['label' => 'Gérer les blogs', 'url' => route('admin.blogs.index')],
                ['label' => 'Réviser', 'url' => route('admin.blogs.show', $blog)],
            ],
        ]);
    }

    public function approve(Blog $blog)
    {
        Gate::authorize('approve', $blog);


    /**
     * Reject a blog with an optional reason. Authorization is handled by RejectBlogRequest.
     */
        $this->blogService->approveBlog($blog, Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Blog approuvé et publié!'
        ]);
    }

    public function reject(Blog $blog, RejectBlogRequest $request)
    {
        $this->blogService->rejectBlog($blog, Auth::user(), $request->validated()['reason'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Blog refusé!'
        ]);
    }

    public function bulkAction(Request $request)
    {
        Gate::authorize('manage', Blog::class);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'blog_ids' => 'required|array|min:1',
            'blog_ids.*' => 'exists:blogs,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $action = $request->input('action');
        $blogIds = $request->input('blog_ids');
        $reason = $request->input('reason');

        $results = $this->blogService->bulkAction($action, $blogIds, Auth::user(), $reason);

        return response()->json([
            'success' => true,
            'message' => $results['message'],
            'processed' => $results['processed'],
            'failed' => $results['failed'],
        ]);
    }

    public function getAllPendingIds(Request $request)
    {
        Gate::authorize('manage', Blog::class);

        $search = $request->input('search');
        $authorId = $request->input('author');

        $ids = $this->blogService->getAllPendingBlogIds($search, $authorId);

        return response()->json([
            'success' => true,
            'ids' => $ids,
            'count' => count($ids),
        ]);
    }
}
