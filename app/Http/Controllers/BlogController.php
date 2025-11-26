<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {
    }
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Blog::class);

    $search = $request->get('search');
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

    public function show(Blog $blog)
    {
        Gate::authorize('view', $blog);

        $blog = $this->blogService->findBlog($blog->id, Auth::id());

        return view('blogs.show', [
            'blog' => $blog,
            'title' => $blog->title,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
                ['label' => $blog->title, 'url' => route('blogs.show', $blog)],
            ],
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Blog::class);

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

        return response()->json([
            'success' => true,
            'message' => $request->validated()['status'] === 'pending'
                ? 'Blog soumis pour approbation!'
                : 'Brouillon sauvegardé!',
            'redirect' => route('profile.edit', ['tab' => 'blogs'])
        ]);
    }

    public function edit(Blog $blog)
    {
        Gate::authorize('update', $blog);

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

        return response()->json([
            'success' => true,
            'message' => $request->validated()['status'] === 'pending'
                ? 'Blog soumis pour approbation!'
                : 'Brouillon sauvegardé!',
            'redirect' => route('profile.edit', ['tab' => 'blogs'])
        ]);
    }

    public function destroy(Blog $blog, Request $request)
    {
        Gate::authorize('delete', $blog);

        $this->blogService->deleteBlog($blog);

        return response()->json([
            'success' => true,
            'message' => 'Blog supprimé avec succès!'
        ]);
    }
}
