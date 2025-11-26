@php
    // Count only direct blog likes (exclude comment likes)
    $blogLikesCount = \App\Models\BlogLike::where('likeable_type', \App\Models\Blog::class)
        ->where('likeable_id', $blog->id)
        ->count();
    // Count only direct comments (exclude replies)
    $directCommentsCount = $blog->comments->count();
    // Disable interactions (likes/comments/bookmarks/copy) when not published
    $interactionsDisabled = ! $blog->isPublished();
@endphp

@extends('blogs.layout')

@section('blogs-content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <meta name="blog-id" content="{{ $blog->id }}">
                    <h1 class="mb-3">{{ $blog->title }}</h1>
                    <p class="lead text-muted mb-4">{{ $blog->short_description }}</p>

                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                            class="text-decoration-none">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ $blog->user->initials }}
                            </div>
                        </a>
                        <div>
                            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                class="text-decoration-none">
                                <h6 class="mb-0 text-dark">{{ $blog->user->full_name }}</h6>
                            </a>
                            <small class="text-muted">{{ $blog->time_ago }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div class="d-flex gap-3">
                            @auth
                                @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-primary like-btn" data-likeable-id="{{ $blog->id }}"
                                    data-likeable-type="App\Models\Blog" data-liked="{{ $blogLiked ? 'true' : 'false' }}" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart"></i>
                                    <span class="likes-count">{{ $blogLikesCount }}</span>
                                </button>
                                <button class="btn btn-outline-secondary scroll-to-comments" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="fa fa-comment"></i>
                                    <span>{{ $directCommentsCount }}</span>
                                </button>
                            @else
                                <button class="btn btn-outline-primary" data-auth-required @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="far fa-heart"></i>
                                    <span>{{ $blogLikesCount }}</span>
                                </button>
                                <button class="btn btn-outline-secondary scroll-to-comments">
                                    <i class="fa fa-comment"></i>
                                    <span>{{ $directCommentsCount }}</span>
                                </button>
                            @endauth
                        </div>

                        <div class="d-flex gap-2">
                            @auth
                                @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-secondary bookmark-btn"
                                    type="button"
                                    data-blog-id="{{ $blog->id }}"
                                    data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Actions désactivées pour ce statut" @endif>
                                    <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-secondary"
                                    type="button"
                                    data-auth-required @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Actions désactivées pour ce statut" @endif>
                                    <i class="far fa-bookmark"></i>
                                </button>
                            @endauth

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="fa fa-share-alt"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item copy-link @if($interactionsDisabled) disabled @endif" href="#"
                                            data-url="{{ route('blogs.show', $blog) }}" @if($interactionsDisabled) aria-disabled="true" data-disabled="true" title="Copie désactivée pour ce statut" @endif>
                                            <i class="fa fa-copy"></i> Copier le lien
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            @auth
                                @if (Auth::id() === $blog->user_id)
                                    <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-outline-secondary"
                                        title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endif

                                @if ((auth()->check() && auth()->user()->isAdmin()) || Auth::id() === $blog->user_id)
                                    <button class="btn btn-outline-danger delete-blog-btn" data-blog-id="{{ $blog->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <hr>

                    <div class="blog-content mb-5">
                        {!! $blog->content !!}
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            @auth
                                @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-primary like-btn" data-likeable-id="{{ $blog->id }}"
                                    data-likeable-type="App\\Models\\Blog" data-liked="{{ $blogLiked ? 'true' : 'false' }}" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart"></i>
                                    <span class="likes-count">{{ $blogLikesCount }}</span>
                                </button>
                                <button class="btn btn-outline-secondary scroll-to-comments" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="fa fa-comment"></i>
                                    <span>{{ $directCommentsCount }}</span>
                                </button>
                            @else
                                <button class="btn btn-outline-primary" data-auth-required @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="far fa-heart"></i>
                                    <span>{{ $blogLikesCount }}</span>
                                </button>
                                <button class="btn btn-outline-secondary scroll-to-comments" @if($interactionsDisabled) disabled aria-disabled="true" data-disabled="true" title="Interactions désactivées pour ce statut" @endif>
                                    <i class="fa fa-comment"></i>
                                    <span>{{ $directCommentsCount }}</span>
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4" id="comments-section">
                <div class="card-body p-4">
                    <h4 class="mb-4">Commentaires</h4>

                    @auth
                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                {{ Auth::user()->initials }}
                            </div>
                            <div class="flex-grow-1">
                                <form class="comment-form" data-blog-id="{{ $blog->id }}" data-parent-id="">
                                    <div class="input-group">
                                        <input type="text" class="form-control comment-input"
                                            placeholder="Ajouter un commentaire..." required @if($interactionsDisabled) disabled aria-disabled="true" title="Commentaires désactivés pour ce statut" @endif>
                                        <button type="submit" class="btn btn-primary" @if($interactionsDisabled) disabled aria-disabled="true" title="Commentaires désactivés pour ce statut" @endif>
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <a href="{{ route('login') }}">Connectez-vous</a> pour commenter.
                        </div>
                    @endauth

                    <div id="comments-list">
                        @include('blogs.partials.comments', ['comments' => $blog->comments, 'level' => 0])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
