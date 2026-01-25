@forelse($blogs as $blog)
    <div class="blog-item mb-3" data-blog-id="{{ $blog->id }}">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                @include('admin.blogs.partials.card', [
                    'blog' => $blog,
                    'canBookmark' => $blog->isPublished(),
                    'canShare' => $blog->isPublished(),
                    'canEdit' => auth()->user()->isAdmin(),
                    'canDelete' => auth()->user()->isAdmin(),
                    'canCheckbox' => false,
                    'canApproveReject' => auth()->check() && auth()->user()->isAdmin() && $blog->isPending(),
                ])
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fa fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun blog trouvé pour cette recherche.</p>
        </div>
    </div>
@endforelse

@if ($blogs->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endif
