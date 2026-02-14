<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBlogRequest;
use App\Http\Requests\DeleteBlogRequest;
use App\Http\Requests\EditBlogRequest;
use App\Http\Requests\PublishBlogRequest;
use App\Http\Requests\RejectBlogRequest;
use App\Http\Requests\SearchBlogsRequest;
use App\Http\Requests\ShowBlogRequest;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Requests\ApproveBlogRequest;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {
    }
    public function index(SearchBlogsRequest $request)
    {
        $search = $request->getSearchTerm();
        $authorId = $request->getAuthorId();
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $blogs = $this->blogService->getAllBlogs(20, $search, $authorId, $status, $dateFrom, $dateTo);

        // Get all authors who have created blogs for the filter dropdown
        $authors = $this->blogService->getAllAuthors();

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.blogs.partials.blogs-list', [
                    'blogs' => $blogs,
                ])->render(),
                'pagination' => $blogs->appends($request->query())->links('pagination::bootstrap-5')->toHtml(),
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
            'currentStatus' => $status,
            'currentDateFrom' => $dateFrom,
            'currentDateTo' => $dateTo,
            'title' => 'Gestion des blogs',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Administration', 'url' => '#'],
                ['label' => 'Gestion des blogs'],
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
        $canApprove = Auth::check() && auth()->user()->isAdmin() && $blog->isPending();
        $canReject = Auth::check() && auth()->user()->isAdmin() && $blog->isPending();

        // Prepare status alert
        $statusAlert = $this->prepareStatusAlert($blog, $canEdit);

        return view('admin.blogs.show', [
            'blog' => $blog,
            'title' => $blog->title,
            'canLike' => $canLike,
            'canBookmark' => $canBookmark,
            'canShare' => $canShare,
            'canComment' => $canComment,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
            'canApprove' => $canApprove,
            'canReject' => $canReject,
            'interactionsDisabled' => $interactionsDisabled,
            'statusAlert' => $statusAlert,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Blogs', 'url' => route('admin.blogs.index')],
                ['label' => $blog->title],
            ],

        ]);
    }

    public function create(CreateBlogRequest $request)
    {
        return view('admin.blogs.create', [
            'title' => 'Créer un blog',
            'action' => route('admin.blogs.store'),
            'method' => 'POST',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Blogs', 'url' => route('admin.blogs.index')],
                ['label' => 'Créer'],
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
                'redirect' => route('admin.blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('admin.blogs.show', $blog))
            ->with('success', $request->validated()['status'] === 'pending'
                ? 'Blog soumis pour approbation!'
                : 'Brouillon sauvegardé!');
    }

    public function edit(EditBlogRequest $request, Blog $blog)
    {

        return view('admin.blogs.edit', [
            'blog' => $blog,
            'title' => 'Modifier le blog',
            'action' => route('admin.blogs.update', $blog),
            'method' => 'PUT',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Blogs', 'url' => route('admin.blogs.index')],
                ['label' => $blog->title, 'url' => route('admin.blogs.show', $blog)],
                ['label' => 'Modifier'],
            ],
        ]);
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $this->blogService->updateBlog($blog, $request->validated());

        // Determine success message based on status
        $status = $request->validated()['status'];
        $message = match ($status) {
            'published' => 'Blog publié avec succès!',
            'pending' => 'Blog soumis pour approbation!',
            'draft' => 'Brouillon sauvegardé!',
        };

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('admin.blogs.show', $blog))->with('success', $message);
    }

    public function publish(PublishBlogRequest $request, Blog $blog)
    {
        $this->blogService->publishBlog($blog);

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog soumis pour approbation!',
                'redirect' => route('admin.blogs.show', $blog)
            ]);
        }

        // Regular form submission - redirect directly
        return redirect(route('admin.blogs.show', $blog))
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

    public function approve(ApproveBlogRequest $request, Blog $blog)
    {
        // Authorization is handled by ApproveBlogRequest
        $this->blogService->approveBlog($blog, Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Blog approuvé et publié!'
        ]);
    }

    public function reject(RejectBlogRequest $request, Blog $blog)
    {
        // Authorization is handled by RejectBlogRequest
        $this->blogService->rejectBlog($blog, Auth::user(), $request->validated()['reason'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Blog refusé!'
        ]);
    }

    /**
     * Prepare the status alert data for the blog show view
     * 
     * @param Blog $blog
     * @param bool $canEdit
     * @return array|null
     */
    private function prepareStatusAlert(Blog $blog, bool $canEdit): ?array
    {
        if ($blog->isPublished()) {
            return null;
        }

        if ($blog->isDraft()) {
            return [
                'type' => 'info',
                'message' => 'Ce blog est un brouillon.',
                'action' => $canEdit ? [
                    'label' => 'Soumettre pour approbation',
                    'url' => route('admin.blogs.publish', $blog),
                    'csrfToken' => csrf_token(),
                ] : null,
            ];
        }

        if ($blog->isPending()) {
            return [
                'type' => 'info',
                'message' => 'Ce blog est en attente d\'approbation par les administrateurs.',
                'action' => null,
            ];
        }

        if ($blog->isRejected()) {
            return [
                'type' => 'danger',
                'message' => 'Ce blog a été rejeté.',
                'reason' => $blog->rejection_reason,
                'action' => null,
            ];
        }

        return null;
    }
}
