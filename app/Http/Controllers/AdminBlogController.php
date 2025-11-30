<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectBlogRequest;
use App\Http\Requests\SearchBlogsRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminBlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {
    }

    public function index(SearchBlogsRequest $request)
    {
        Gate::authorize('manage', Blog::class);

        $search = $request->getSearchTerm();
        $authorId = $request->getAuthorId();

        $blogs = $this->blogService->getPendingBlogs(20, $search, $authorId);

        // Get all authors who have pending blogs for the filter dropdown
        $authors = $this->blogService->getAuthorsWithPendingBlogs();

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.blogs.partials.blogs-list', [
                    'blogs' => $blogs,
                ])->render(),
                'pagination' => $blogs->appends($request->query())->links()->toHtml(),
                'count' => $blogs->total(),
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
            ]);
        }

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

    public function approveBulk(Request $request)
    {
        Gate::authorize('manage', Blog::class);

        $request->validate([
            'blog_ids' => 'required|array|min:1',
            'blog_ids.*' => 'exists:blogs,id',
        ]);

        $blogIds = $request->input('blog_ids');

        $results = $this->blogService->bulkAction('approve', $blogIds, Auth::user());

        return response()->json([
            'success' => true,
            'message' => $results['message'],
            'processed' => $results['processed'],
            'failed' => $results['failed'],
        ]);
    }

    public function rejectBulk(Request $request)
    {
        Gate::authorize('manage', Blog::class);

        $request->validate([
            'blog_ids' => 'required|array|min:1',
            'blog_ids.*' => 'exists:blogs,id',
            'reason' => 'required|string|max:500',
        ]);

        $blogIds = $request->input('blog_ids');
        $reason = $request->input('reason');

        $results = $this->blogService->bulkAction('reject', $blogIds, Auth::user(), $reason);

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
