{{-- Blogs Tab Content --}}
@if ($isViewingOwnProfile)
    <!-- Status Filter Pills -->
    <div class="mb-4">
        <ul class="nav nav-pills blog-status-pills" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-blogs">
                    <i class="fa fa-list me-2"></i>Tous 
                    <span class="badge bg-secondary ms-1">{{ $publishedBlogs->count() + $draftBlogs->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#published-blogs">
                    <i class="fa fa-check-circle me-2"></i>Publiés 
                    <span class="badge bg-success ms-1">{{ $publishedBlogs->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#pending-blogs">
                    <i class="fa fa-clock me-2"></i>En attente 
                    <span class="badge bg-warning ms-1">{{ $draftBlogs->where('status', \App\Models\Blog::PENDING)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#draft-blogs">
                    <i class="fa fa-edit me-2"></i>Brouillons 
                    <span class="badge bg-info ms-1">{{ $draftBlogs->where('status', \App\Models\Blog::DRAFT)->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#rejected-blogs">
                    <i class="fa fa-times-circle me-2"></i>Refusés 
                    <span class="badge bg-danger ms-1">{{ $draftBlogs->where('status', \App\Models\Blog::REJECTED)->count() }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <!-- All Blogs -->
        <div class="tab-pane fade show active" id="all-blogs">
            @php
                $allBlogs = $publishedBlogs->concat($draftBlogs)->sortByDesc('created_at');
            @endphp
            @forelse($allBlogs as $blog)
                <div class="mb-3">
                    @include('admin.blogs.partials.card', [
                        'blog' => $blog,
                        'canAuthor' => false,
                        'canBookmark' => false,
                        'canShare' => true,
                        'canEdit' => true,
                        'canDelete' => true,
                    ])
                </div>
            @empty
                <div class="empty-state text-center py-5">
                    <i class="fa fa-newspaper fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun blog</h5>
                    <p class="text-muted mb-3">Commencez à partager vos idées avec le monde</p>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                        <i class="fa fa-pen me-2"></i>Écrire votre premier blog
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Published Blogs -->
        <div class="tab-pane fade" id="published-blogs">
            @forelse($publishedBlogs as $blog)
                <div class="mb-3">
                    @include('admin.blogs.partials.card', [
                        'blog' => $blog,
                        'canAuthor' => false,
                        'canBookmark' => false,
                        'canShare' => true,
                        'canEdit' => true,
                        'canDelete' => true,
                    ])
                </div>
            @empty
                <div class="empty-state text-center py-5">
                    <i class="fa fa-check-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun blog publié</h5>
                    <p class="text-muted">Vos blogs publiés apparaîtront ici</p>
                </div>
            @endforelse
        </div>

        <!-- Pending Blogs -->
        <div class="tab-pane fade" id="pending-blogs">
            @php
                $pendingBlogs = $draftBlogs->where('status', \App\Models\Blog::PENDING);
            @endphp
            @forelse($pendingBlogs as $blog)
                <div class="mb-3">
                    @include('admin.blogs.partials.card', [
                        'blog' => $blog,
                        'canAuthor' => false,
                        'canBookmark' => false,
                        'canShare' => false,
                        'canEdit' => true,
                        'canDelete' => true,
                    ])
                </div>
            @empty
                <div class="empty-state text-center py-5">
                    <i class="fa fa-clock fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun blog en attente</h5>
                    <p class="text-muted">Les blogs en attente de validation apparaîtront ici</p>
                </div>
            @endforelse
        </div>

        <!-- Draft Blogs -->
        <div class="tab-pane fade" id="draft-blogs">
            @php
                $drafts = $draftBlogs->where('status', \App\Models\Blog::DRAFT);
            @endphp
            @forelse($drafts as $blog)
                <div class="mb-3">
                    @include('admin.blogs.partials.card', [
                        'blog' => $blog,
                        'canAuthor' => false,
                        'canBookmark' => false,
                        'canShare' => false,
                        'canEdit' => true,
                        'canDelete' => true,
                    ])
                </div>
            @empty
                <div class="empty-state text-center py-5">
                    <i class="fa fa-edit fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun brouillon</h5>
                    <p class="text-muted">Vos brouillons apparaîtront ici</p>
                </div>
            @endforelse
        </div>

        <!-- Rejected Blogs -->
        <div class="tab-pane fade" id="rejected-blogs">
            @php
                $rejectedBlogs = $draftBlogs->where('status', \App\Models\Blog::REJECTED);
            @endphp
            @forelse($rejectedBlogs as $blog)
                <div class="mb-3">
                    @include('admin.blogs.partials.card', [
                        'blog' => $blog,
                        'canAuthor' => false,
                        'canBookmark' => false,
                        'canShare' => false,
                        'canEdit' => true,
                        'canDelete' => true,
                    ])
                </div>
            @empty
                <div class="empty-state text-center py-5">
                    <i class="fa fa-times-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun blog refusé</h5>
                    <p class="text-muted">Les blogs refusés apparaîtront ici</p>
                </div>
            @endforelse
        </div>
    </div>
@else
    <!-- Viewing another user's published blogs -->
    <div class="mb-4">
        <h5 class="mb-1"><i class="fa fa-newspaper me-2"></i>Blogs publiés</h5>
        <p class="text-muted small mb-0">Articles partagés par cet utilisateur</p>
    </div>
    
    @forelse($publishedBlogs as $blog)
        <div class="mb-3">
            @include('admin.blogs.partials.card', [
                'blog' => $blog,
                'canAuthor' => false,
                'canBookmark' => true,
                'canShare' => true,
                'canEdit' => false,
                'canDelete' => false,
            ])
        </div>
    @empty
        <div class="empty-state text-center py-5">
            <i class="fa fa-newspaper fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun blog publié</h5>
            <p class="text-muted">Cet utilisateur n'a pas encore publié de blog</p>
        </div>
    @endforelse
@endif

@include('admin.blogs.partials.scripts')

@push('styles')
<style>
.blog-status-pills {
    gap: 0.5rem;
    flex-wrap: wrap;
}

.blog-status-pills .nav-link {
    border-radius: 2rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--bs-secondary);
    background: var(--bs-light);
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.blog-status-pills .nav-link:hover {
    background: white;
    border-color: var(--bs-primary);
    color: var(--bs-primary);
}

.blog-status-pills .nav-link.active {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.blog-status-pills .nav-link.active .badge {
    background: rgba(255, 255, 255, 0.3);
}

.empty-state {
    background: var(--bs-light);
    border-radius: 1rem;
    padding: 3rem 1rem;
}
</style>
@endpush

