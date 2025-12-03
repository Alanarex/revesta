{{-- Bookmarks Tab --}}
<div class="bookmarks-container">
    @if($bookmarks->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="fa fa-bookmark fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun signet</h5>
            <p class="text-muted mb-3">Vous n'avez pas encore sauvegardé de blog</p>
            <a href="{{ route('blogs.index') }}" class="btn btn-primary">
                <i class="fa fa-newspaper me-2"></i>Découvrir des blogs
            </a>
        </div>
    @else
        @foreach($bookmarks as $bookmark)
            @if($bookmark->blog)
                <div class="mb-3">
                    @include('blogs.partials.card', [
                        'blog' => $bookmark->blog,
                        'canAuthor' => true,
                        'canBookmark' => true,
                        'canShare' => true,
                        'canEdit' => false,
                        'canDelete' => false,
                    ])
                </div>
            @endif
        @endforeach
    @endif
</div>

@push('styles')
<style>
.empty-state {
    background: var(--bs-light);
    border-radius: 1rem;
    padding: 3rem 1rem;
}
</style>
@endpush

