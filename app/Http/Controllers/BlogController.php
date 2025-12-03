<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBlogRequest;
use App\Http\Requests\DeleteBlogRequest;
use App\Http\Requests\EditBlogRequest;
use App\Http\Requests\PublishBlogRequest;
use App\Http\Requests\SearchPublicBlogsRequest;
use App\Http\Requests\ShowBlogRequest;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {
    }
    public function index(SearchPublicBlogsRequest $request)
    {
        $search = $request->getSearchTerm();
        $blogs = $this->blogService->getPublishedBlogs($search, 10, Auth::id());

        return view('blogs.index', [
            'blogs' => $blogs,
            'search' => $search,
            'title' => 'Blogs',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
            ],
        ]);
    }

    public function show(ShowBlogRequest $request, Blog $blog)
    {
        $blog = $this->blogService->findBlog($blog->id, Auth::id());

        // Calculate permissions
        $interactionsDisabled = !$blog->isPublished();
        $canLike = Auth::check() && !$interactionsDisabled;
        $canBookmark = Auth::check() && !$interactionsDisabled;
        $canShare = !$interactionsDisabled;
        $canComment = Auth::check();
        $canEdit = Auth::check() && Auth::id() === $blog->user_id;
        $canDelete = Auth::check() && ((auth()->user()->isAdmin()) || Auth::id() === $blog->user_id);

        // Calculate counts
        $blogLikesCount = \App\Models\BlogLike::where('likeable_type', Blog::class)
            ->where('likeable_id', $blog->id)
            ->count();
        $directCommentsCount = $blog->comments->count();

        return view('blogs.show', [
            'blog' => $blog,
            'title' => $blog->title,
            'canLike' => $canLike,
            'canBookmark' => $canBookmark,
            'canShare' => $canShare,
            'canComment' => $canComment,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
            'blogLikesCount' => $blogLikesCount,
            'directCommentsCount' => $directCommentsCount,
            'interactionsDisabled' => $interactionsDisabled,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
                ['label' => $blog->title, 'url' => route('blogs.show', $blog)],
            ],
        ]);
    }

    public function create(CreateBlogRequest $request)
    {

        return view('blogs.create', [
            'title' => 'Créer un blog',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
                ['label' => 'Créer', 'url' => route('blogs.create')],
            ],
        ]);
    }

    public function store(StoreBlogRequest $request)
    {
        $blog = $this->blogService->createBlog(Auth::user(), $request->validated());

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $request->validated()['status'] === 'pending'
                    ? 'Blog soumis pour approbation!'
                    : 'Brouillon sauvegardé!',
                'redirect' => route('blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('blogs.show', $blog))
            ->with('success', $request->validated()['status'] === 'pending'
                ? 'Blog soumis pour approbation!'
                : 'Brouillon sauvegardé!');
    }

    public function edit(EditBlogRequest $request, Blog $blog)
    {

        return view('blogs.edit', [
            'blog' => $blog,
            'title' => 'Modifier le blog',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
                ['label' => $blog->title, 'url' => route('blogs.show', $blog)],
                ['label' => 'Modifier', 'url' => route('blogs.edit', $blog)],
            ],
        ]);
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $this->blogService->updateBlog($blog, $request->validated());

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $request->validated()['status'] === 'pending'
                    ? 'Blog soumis pour approbation!'
                    : 'Brouillon sauvegardé!',
                'redirect' => route('blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('blogs.show', $blog))
            ->with('success', $request->validated()['status'] === 'pending'
                ? 'Blog soumis pour approbation!'
                : 'Brouillon sauvegardé!');
    }

    public function publish(PublishBlogRequest $request, Blog $blog)
    {
        $this->blogService->publishBlog($blog);

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog soumis pour approbation!',
                'redirect' => route('blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('blogs.show', $blog))
            ->with('success', 'Blog soumis pour approbation!');
    }

    public function destroy(DeleteBlogRequest $request, Blog $blog)
    {

        $this->blogService->deleteBlog($blog);

        return response()->json([
            'success' => true,
            'message' => 'Blog supprimé avec succès!'
        ]);
    }
}
