<div class="blog-item mb-4 pb-4 border-bottom">
    @php
        use Illuminate\Support\Str;

        // Permissions and visibility controls
        $canCheckbox = $canCheckbox ?? false;
        $canBookmark = $canBookmark ?? true;
        $canShare = $canShare ?? true;
        $canEdit = $canEdit ?? true;
        $canDelete = $canDelete ?? true;
        $canApproveReject = $canApproveReject ?? false;
        $canAuthor = $canAuthor ?? true;

        // Disable interactive actions for non-published blogs
        $interactionsDisabled = !$blog->isPublished();
        $isOwner = Auth::check() && Auth::id() === $blog->user_id;
        $isAdmin = Auth::check() && auth()->user()->isAdmin();
    @endphp

    <!-- Author Section -->
    <div class="d-flex align-items-center gap-2 mb-2">
        @if ($canAuthor)
            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                class="text-decoration-none flex-shrink-0">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 24px; height: 24px; font-size: 11px; font-weight: bold;">
                    {{ $blog->user->initials }}
                </div>
            </a>
            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                class="text-decoration-none">
                <small class="text-muted">par <strong>{{ $blog->user->full_name }}</strong></small>
            </a>
        @endif
        <span class="badge {{ $blog->getStatusBadgeClass() }} ms-auto">
            {{ $blog->getStatusLabel() }}
        </span>
    </div>

    <!-- Title -->
    <a href="{{ route('admin.blogs.show', $blog) }}" class="text-decoration-none">
        <h5 class="mb-2 text-dark fw-bold">{{ $blog->title }}</h5>
    </a>

    <!-- Description -->
    <a href="{{ route('admin.blogs.show', $blog) }}" class="text-decoration-none">
        <p class="text-muted mb-3 small">{{ Str::limit($blog->short_description, 150) }}</p>
    </a>

    <!-- Stats and Actions Row -->
    <div class="d-flex align-items-center justify-content-between">
        <!-- Left: Stats -->
        <div class="d-flex gap-3 text-muted small">
            @php
                // Use preloaded counts when available to avoid N+1 queries.
                $blogLikesCount = $blog->likes_count ?? 0;
                $directCommentsCount = $blog->direct_comments_count ?? 0;
            @endphp
            <span>
                <i class="fa fa-clock"></i>
                <span class="ms-1">{{ $blog->time_ago }}</span>
            </span>
            <span class="text-danger">
                <i class="fas fa-heart"></i>
                <span class="ms-1">{{ $blogLikesCount }}</span>
            </span>
            <span>
                <i class="fa fa-comment"></i>
                <span class="ms-1">{{ $directCommentsCount }}</span>
            </span>
        </div>

        <!-- Right: Bookmark and Menu -->
        <div class="d-flex align-items-center gap-2">
            @if ($canBookmark)
                @auth
                    @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                    <button class="btn btn-link p-0 bookmark-btn text-decoration-none" type="button"
                        data-blog-id="{{ $blog->id }}"
                        data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}"
                        @if ($interactionsDisabled) disabled aria-disabled="true" @endif
                        title="Ajouter aux signets">
                        <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                    </button>
                @else
                    <button class="btn btn-link p-0 text-decoration-none" type="button" data-auth-required
                        title="Connexion requise">
                        <i class="far fa-bookmark"></i>
                    </button>
                @endauth
            @endif

            <!-- Three Dots Menu -->
            <div class="dropdown">
                <button class="btn btn-link p-0 text-decoration-none text-muted" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false"
                    title="Plus d'options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if ($canShare)
                        <li>
                            <a class="dropdown-item copy-link" href="#"
                                data-url="{{ route('admin.blogs.show', $blog) }}"
                                @if ($interactionsDisabled) data-disabled="true" @endif>
                                <i class="fa fa-copy me-2"></i> Copier le lien
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    @endif

                    @if ($canEdit && $isOwner)
                        <li>
                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="dropdown-item">
                                <i class="fa fa-edit me-2"></i> Modifier
                            </a>
                        </li>
                    @endif

                    @if ($canDelete && ($isOwner || $isAdmin))
                        <li>
                            <button class="dropdown-item text-danger delete-blog-btn" type="button"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-trash me-2"></i> Supprimer
                            </button>
                        </li>
                    @endif

                    @if ($canApproveReject && $isAdmin)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-success approve-single-btn"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-check me-2"></i> Approuver
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger reject-single-btn"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-times me-2"></i> Rejeter
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        @if ($canBookmark ?? true)
            @vite('resources/js/blogs/components/bookmark.js')
        @endif

        @if ($canShare ?? true)
            @vite('resources/js/blogs/components/share.js')
        @endif

        @if ($canDelete ?? true)
            @vite('resources/js/blogs/components/delete.js')
        @endif
    @endpush
@endonce
