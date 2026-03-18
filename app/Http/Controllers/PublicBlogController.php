<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowBlogRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Support\Facades\Auth;

class PublicBlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {}

    public function show(ShowBlogRequest $request, Blog $blog)
    {
        $blog = $this->blogService->findBlog($blog->id, Auth::id());

        return view('blogs.show', [
            'blog' => $blog,
            'title' => $blog->title,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Blogs'],
                ['label' => $blog->title],
            ],
        ]);
    }
}
