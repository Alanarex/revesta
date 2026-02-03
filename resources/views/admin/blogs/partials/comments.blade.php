@foreach ($comments as $comment)
    <div class="comment-item mb-3" data-comment-id="{{ $comment->id }}" style="margin-left: {{ $level * 30 }}px;">
        <div class="d-flex align-items-start">
            @php
                // Avoid triggering lazy-loading of the `user` relation in the view.
                // Use the already-loaded relation when available; otherwise render
                // a lightweight placeholder (no DB query).
                $commentUser = $comment->relationLoaded('user') ? $comment->user : null;
            @endphp
            <a href="{{ route('users.show', ['user' => $comment->user_id]) }}" class="text-decoration-none">
                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                    {{ $commentUser ? $commentUser->initials : strtoupper(substr((string) $comment->user_id, 0, 1)) }}
                </div>
            </a>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <a href="{{ route('users.show', ['user' => $comment->user_id]) }}"
                            class="text-decoration-none">
                            <h6 class="mb-1">{{ $commentUser ? $commentUser->full_name : __('Utilisateur') }}
                            </h6>
                        </a>
                        <p class="mb-2">{{ $comment->content }}</p>
                        <div class="d-flex gap-3 align-items-center">
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            @auth
                                @php
                                    $commentLiked = ($comment->liked_by_auth ?? 0) > 0;
                                @endphp
                                <button class="btn btn-link p-0 like-btn text-decoration-none text-danger"
                                    data-likeable-id="{{ $comment->id }}" data-likeable-type="App\Models\BlogComment"
                                    data-liked="{{ $commentLiked ? 'true' : 'false' }}">
                                    <i class="{{ $commentLiked ? 'fas' : 'far' }} fa-heart" style="font-size: 1.25rem;"></i>
                                    <span
                                        class="likes-count ms-1 small">{{ $comment->likes_count ?? $comment->likes->count() }}</span>
                                </button>
                                <button class="btn btn-sm btn-link text-decoration-none p-0 reply-btn"
                                    data-comment-id="{{ $comment->id }}">
                                    Répondre
                                </button>
                            @endauth
                            @php $repliesCount = $comment->replies_count ?? $comment->replies->count(); @endphp
                            @if ($repliesCount > 0)
                                <button class="btn btn-sm btn-link text-decoration-none p-0 show-replies-btn"
                                    data-comment-id="{{ $comment->id }}" data-shown="false" data-replies-count="{{ $repliesCount }}">
                                    <i class="fa fa-chevron-down"></i> {{ $repliesCount }} réponse(s)
                                </button>
                            @endif
                        </div>
                    </div>
                    @auth
                        @if ((auth()->check() && auth()->user()->isAdmin()) || Auth::id() === $comment->user_id)
                            <button class="btn btn-link p-0 delete-comment-btn text-decoration-none text-danger"
                                data-comment-id="{{ $comment->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        @endif
                    @endauth
                </div>

                <div class="reply-form-container mt-2" style="display: none;"></div>

                <div class="replies-container mt-3 position-relative" style="display: none;">
                    <div style="position: absolute; left: -25px; top: -15px; bottom: 0; width: 2px; background-color: #e0e0e0;"></div>
                    @if ($level < 3)
                        @include('admin.blogs.partials.comments', [
                            'comments' => $comment->replies->take(2),
                            'level' => $level + 1,
                        ])
                    @endif
                    @if (($comment->replies_count ?? $comment->replies->count()) > 2)
                        <button class="btn btn-sm btn-link load-more-replies" data-comment-id="{{ $comment->id }}"
                            data-offset="2" data-level="{{ $level + 1 }}">
                            Voir plus de réponses...
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
