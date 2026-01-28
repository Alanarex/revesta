<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchPublicBlogsRequest;
use App\Http\Requests\ShowBlogRequest;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Blog endpoints
     *
     * @group Blogs
     */
    public function __construct(
        protected BlogService $blogService
    ) {
    }

    /**
     * Get list of published blogs with likes and comments count
     * 
     * @param SearchPublicBlogsRequest $request
     * @group Blogs
     * @return BlogCollection
     */
    public function index(SearchPublicBlogsRequest $request): BlogCollection
    {
        $search = $request->getSearchTerm();
        $perPage = $request->query('per_page', 10);

        $blogs = $this->blogService->getPublishedBlogs($search, $perPage);

        return new BlogCollection($blogs);
    }

    /**
     * Get a single blog with comments, likes, and bookmarks
     * 
     * @param ShowBlogRequest $request
     * @param Blog $blog
     * @group Blogs
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowBlogRequest $request, Blog $blog): JsonResponse
    {
        $blog = $this->blogService->findBlog($blog->id, Auth::id());

        return response()->json([
            'success' => true,
            'data' => [
                'blog' => new BlogResource($blog)
            ],
        ]);
    }
}
