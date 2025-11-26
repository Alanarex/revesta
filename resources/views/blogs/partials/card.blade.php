<div class="card mb-3 shadow-sm">
    @php
        use Illuminate\Support\Str;
        // Control which buttons to show
        $showBookmark = $showBookmark ?? true;
        $showShare = $showShare ?? true;
        $showEdit = $showEdit ?? true;
        $showDelete = $showDelete ?? true;
        // Control whether to show author info (hide on profile pages since it's redundant)
        $showAuthor = $showAuthor ?? true;
        // Disable interactive actions (like/comment/bookmark/share) for non-published statuses
        $interactionsDisabled = $blog->isDraft() || $blog->isPending() || $blog->isRejected();
    @endphp
    <a href="{{ route('blogs.show', $blog) }}" class="text-decoration-none" style="color: inherit;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="d-flex align-items-start mb-3">
                        @if ($showAuthor)
                            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                class="text-decoration-none">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                    {{ $blog->user->initials }}
                                </div>
                            </a>
                        @endif
                        <div class="flex-grow-1">
                            @if ($showAuthor)
                                <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                    class="text-decoration-none">
                                    <h6 class="mb-0 text-dark">{{ $blog->user->full_name }}</h6>
                                </a>
                            @endif
                            <a href="{{ route('blogs.show', $blog) }}" class="text-decoration-none">
                                <h5 class="mt-2 mb-2 text-dark">{{ $blog->title }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($blog->short_description, 150) }}</p>
                            </a>
                            <div class="d-flex gap-3 text-muted small">
                                @php
                                    // Count only direct blog likes (exclude comment likes)
                                    $blogLikesCount = \App\Models\BlogLike::where(
                                        'likeable_type',
                                        \App\Models\Blog::class,
                                    )
                                        ->where('likeable_id', $blog->id)
                                        ->count();
                                    // Count only direct comments (exclude replies)
                                    $directCommentsCount = $blog->comments->count();
                                @endphp
                                <span><i class="fa fa-clock"></i> {{ $blog->time_ago }}</span>
                                <span><i class="fas fa-heart"></i> {{ $blogLikesCount }}</span>
                                <span><i class="fa fa-comment"></i> {{ $directCommentsCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end gap-2">
                    @if ($showBookmark)
                        @auth
                            @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                            <button class="btn btn-outline-secondary bookmark-btn" type="button"
                                data-blog-id="{{ $blog->id }}" data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}"
                                @if ($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Actions désactivées pour ce statut" @endif>
                                <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-secondary" type="button" data-auth-required
                                @if ($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Actions désactivées pour ce statut" @endif>
                                <i class="far fa-bookmark"></i>
                            </button>
                        @endauth
                    @endif

                    @if ($showShare)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown"
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

                    @auth
                        @if ((auth()->check() && auth()->user()->isAdmin()) || Auth::id() === $blog->user_id)
                            <div class="d-flex gap-2">
                                @if ($showEdit && Auth::id() === $blog->user_id)
                                    <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-outline-secondary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endif

                                @if ($showDelete)
                                    <button class="btn btn-outline-danger delete-blog-btn"
                                        data-blog-id="{{ $blog->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </a>
</div>
