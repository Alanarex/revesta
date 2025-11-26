<div class="card">
    <div class="card-body">
        @if ($isViewingOwnProfile)
            <!-- Header with Write Blog Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Mes Blogs</h5>
                <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                    <i class="fa fa-pen"></i> Écrire un blog
                </a>
            </div>

            <!-- Status Filter Tags -->
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#all-blogs">
                        Tous ({{ $publishedBlogs->count() + $draftBlogs->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#published-blogs">
                        Publiés ({{ $publishedBlogs->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pending-blogs">
                        En attente ({{ $draftBlogs->where('status', 'pending')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#draft-blogs">
                        Brouillons ({{ $draftBlogs->where('status', 'draft')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rejected-blogs">
                        Refusés ({{ $draftBlogs->where('status', 'rejected')->count() }})
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- All Blogs -->
                <div class="tab-pane fade show active" id="all-blogs">
                    @php
                        $allBlogs = $publishedBlogs->concat($draftBlogs)->sortByDesc('created_at');
                    @endphp
                    @forelse($allBlogs as $blog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $blog->title }}
                                    @if ($blog->status === 'published')
                                        <span class="badge bg-success ms-2"><i class="fa fa-check-circle"></i> Publié</span>
                                    @elseif($blog->status === 'pending')
                                        <span class="badge bg-warning"><i class="fa fa-clock"></i> En attente</span>
                                    @elseif($blog->status === 'rejected')
                                        <span class="badge bg-danger"><i class="fa fa-times-circle"></i> Refusé</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fa fa-file"></i> Brouillon</span>
                                    @endif
                                </h6>
                                <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                <div class="text-muted small">
                                    <i class="fa fa-heart"></i> {{ $blog->likes_count ?? 0 }}
                                    <i class="fa fa-comment ms-2"></i> {{ $blog->comments_count ?? 0 }}
                                    <i class="fa fa-clock ms-2"></i> {{ $blog->created_at->diffForHumans() }}
                                </div>
                                @if ($blog->status === 'rejected' && $blog->rejection_reason)
                                    <small class="text-danger d-block mt-1">Raison:
                                        {{ $blog->rejection_reason }}</small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                @if ($blog->status === 'published')
                                    <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-outline-primary"
                                        title="Voir">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary copy-link"
                                        data-url="{{ route('blogs.show', $blog) }}" title="Copier le lien">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                @endif
                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                    data-blog-id="{{ $blog->id }}" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucun blog.</p>
                    @endforelse
                </div>

                <!-- Published Blogs -->
                <div class="tab-pane fade" id="published-blogs">
                    @forelse($publishedBlogs as $blog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $blog->title }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                <div class="text-muted small">
                                    <i class="fa fa-heart"></i> {{ $blog->likes_count ?? 0 }}
                                    <i class="fa fa-comment ms-2"></i> {{ $blog->comments_count ?? 0 }}
                                    <i class="fa fa-clock ms-2"></i> {{ $blog->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-outline-primary"
                                    title="Voir">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary copy-link"
                                    data-url="{{ route('blogs.show', $blog) }}" title="Copier le lien">
                                    <i class="fa fa-copy"></i>
                                </button>
                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                    data-blog-id="{{ $blog->id }}" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucun blog publié.</p>
                    @endforelse
                </div>

                <!-- Pending Blogs -->
                <div class="tab-pane fade" id="pending-blogs">
                    @php
                        $pendingBlogs = $draftBlogs->where('status', 'pending');
                    @endphp
                    @forelse($pendingBlogs as $blog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $blog->title }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                <div class="text-muted small">
                                    <i class="fa fa-clock"></i> {{ $blog->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                    data-blog-id="{{ $blog->id }}" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucun blog en attente.</p>
                    @endforelse
                </div>

                <!-- Draft Blogs -->
                <div class="tab-pane fade" id="draft-blogs">
                    @php
                        $drafts = $draftBlogs->where('status', 'draft');
                    @endphp
                    @forelse($drafts as $blog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $blog->title }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                <div class="text-muted small">
                                    <i class="fa fa-clock"></i> {{ $blog->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                    data-blog-id="{{ $blog->id }}" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucun brouillon.</p>
                    @endforelse
                </div>

                <!-- Rejected Blogs -->
                <div class="tab-pane fade" id="rejected-blogs">
                    @php
                        $rejectedBlogs = $draftBlogs->where('status', 'rejected');
                    @endphp
                    @forelse($rejectedBlogs as $blog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $blog->title }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                <div class="text-muted small">
                                    <i class="fa fa-clock"></i> {{ $blog->created_at->diffForHumans() }}
                                </div>
                                @if ($blog->rejection_reason)
                                    <small class="text-danger d-block mt-1">Raison:
                                        {{ $blog->rejection_reason }}</small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-sm btn-outline-warning"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                    data-blog-id="{{ $blog->id }}" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">Aucun blog refusé.</p>
                    @endforelse
                </div>
            </div>
        @else
            <!-- Viewing another user's published blogs -->
            <h5 class="mb-3">Blogs publiés</h5>
            @forelse($publishedBlogs as $blog)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $blog->title }}</h6>
                        <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                        <div class="text-muted small">
                            <i class="fa fa-heart"></i> {{ $blog->likes_count ?? 0 }}
                            <i class="fa fa-comment ms-2"></i> {{ $blog->comments_count ?? 0 }}
                            <i class="fa fa-clock ms-2"></i> {{ $blog->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-sm btn-outline-primary"
                            title="Voir">
                            <i class="fa fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-secondary copy-link"
                            data-url="{{ route('blogs.show', $blog) }}" title="Copier le lien">
                            <i class="fa fa-copy"></i>
                        </button>
                        @if (Auth::user()->isAdmin())
                            <button class="btn btn-sm btn-outline-danger delete-blog-btn"
                                data-blog-id="{{ $blog->id }}" title="Supprimer">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4">Aucun blog publié.</p>
            @endforelse
        @endif
    </div>
</div>

@include('blogs.partials.scripts')
