@forelse($blogs as $blog)
    <div class="blog-item mb-3" data-blog-id="{{ $blog->id }}">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                @include('blogs.partials.card', [
                    'blog' => $blog,
                    'canBookmark' => false,
                    'canShare' => false,
                    'canEdit' => false,
                    'canDelete' => false,
                    'canCheckbox' => true,
                    'canApproveReject' => true,
                ])
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fa fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun blog trouvé pour cette recherche.</p>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="$('#search').val('').trigger('input'); $('#author').val('').trigger('change');">
                <i class="fa fa-times"></i> Effacer les filtres
            </button>
        </div>
    </div>
@endforelse

@if($blogs->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->appends(request()->query())->links() }}
    </div>
@endif