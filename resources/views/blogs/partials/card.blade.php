<div class="card mb-3 shadow-sm">
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

    <div class="card-body">
        <div class="row align-items-start g-3">
            <!-- Checkbox Column -->
            @if ($canCheckbox)
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input blog-checkbox" type="checkbox" value="{{ $blog->id }}"
                            id="blog-{{ $blog->id }}" style="border: 1px solid black; transform: scale(1.2);">
                    </div>
                </div>
            @endif

            <!-- Content Column -->
            <div class="col">
                <div class="d-flex align-items-start mb-3">
                    @if ($canAuthor)
                        <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                            class="text-decoration-none me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ $blog->user->initials }}
                            </div>
                        </a>
                    @endif
                    <div class="flex-grow-1">
                        @if ($canAuthor)
                            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                class="text-decoration-none">
                                <h6 class="mb-1">{{ $blog->user->full_name }}</h6>
                            </a>
                        @endif
                        <a href="{{ route('blogs.show', $blog) }}" class="text-decoration-none">
                            <h5 class="mb-2 text-dark">{{ $blog->title }}</h5>
                            <p class="text-muted mb-2">{{ Str::limit($blog->short_description, 150) }}</p>
                        </a>
                        <div class="d-flex gap-3 text-muted small">
                            @php
                                $blogLikesCount = \App\Models\BlogLike::where('likeable_type', \App\Models\Blog::class)
                                    ->where('likeable_id', $blog->id)
                                    ->count();
                                $directCommentsCount = $blog->comments->count();
                            @endphp
                            <span><i class="fa fa-clock"></i> {{ $blog->time_ago }}</span>
                            <span><i class="fas fa-heart"></i> {{ $blogLikesCount }}</span>
                            <span><i class="fa fa-comment"></i> {{ $directCommentsCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons Column -->
            <div class="col-auto">
                <div class="d-flex flex-column gap-2">
                    <!-- First Row: General Actions -->
                    <div class="d-flex gap-1 flex-wrap">
                        @if ($canBookmark)
                            @auth
                                @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-secondary btn-sm bookmark-btn" type="button"
                                    data-blog-id="{{ $blog->id }}"
                                    data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}"
                                    @if ($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Actions désactivées pour ce statut" @endif>
                                    <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-auth-required>
                                    <i class="far fa-bookmark"></i>
                                </button>
                            @endauth
                        @endif

                        @if ($canShare)
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                    data-bs-toggle="dropdown"
                                    @if ($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Partage désactivé pour ce statut" @endif>
                                    <i class="fa fa-share-alt"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item copy-link @if ($interactionsDisabled) disabled @endif"
                                            href="#" data-url="{{ route('blogs.show', $blog) }}"
                                            @if ($interactionsDisabled) aria-disabled="true" data-disabled="true" title="Copie désactivée pour ce statut" @endif>
                                            <i class="fa fa-copy"></i> Copier le lien
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        @if ($canEdit && $isOwner)
                            <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endif

                        @if ($canDelete && ($isOwner || $isAdmin))
                            <button class="btn btn-outline-danger btn-sm delete-blog-btn"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Second Row: Admin Actions -->
                    @if ($canApproveReject && $isAdmin)
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-success btn-sm approve-single-btn"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-check"></i> Approuver
                            </button>
                            <button type="button" class="btn btn-danger btn-sm reject-single-btn"
                                data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-times"></i> Rejeter
                            </button>
                        </div>
                    @endif
                </div>
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
