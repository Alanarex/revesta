@extends('layouts.blogs')

@section('content')
    @if (!$blog->isPublished())
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            @if ($blog->isDraft())
                Ce blog est un brouillon.
                @if ($canEdit)
                    <a href="#" class="publish-draft-link ms-2 text-primary" data-blog-id="{{ $blog->id }}"
                        data-publish-url="{{ route('admin.blogs.publish', $blog) }}" data-csrf-token="{{ csrf_token() }}">
                        <small>Soumettre pour approbation</small>
                    </a>
                @endif
            @elseif($blog->isPending())
                Ce blog est en attente d'approbation par <strong><a
                        href="{{ route('profile.show', ['userId' => $blog->user_id]) }}">{{ $blog->user->full_name }}</a></strong>.
            @elseif($blog->isRejected())
                Ce blog a été rejeté.
                @if($blog->rejection_reason)
                    <div class="mt-2">
                        <strong>Raison :</strong> {{ $blog->rejection_reason }}
                    </div>
                @endif
            @endif
        </div>
    @endif
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <meta name="blog-id" content="{{ $blog->id }}">
                    <h1 class="mb-3">{{ $blog->title }}</h1>
                    <p class="lead text-muted mb-4">{{ $blog->short_description }}</p>

                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}" class="text-decoration-none">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ $blog->user->initials }}
                            </div>
                        </a>
                        <div>
                            <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                class="text-decoration-none">
                                <h6 class="mb-0">{{ $blog->user->full_name }}</h6>
                            </a>
                            <small class="text-muted">{{ $blog->time_ago }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div class="d-flex gap-3">
                            @if ($canLike)
                                @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-primary like-btn" data-likeable-id="{{ $blog->id }}"
                                    data-likeable-type="App\Models\Blog" data-liked="{{ $blogLiked ? 'true' : 'false' }}">
                                    <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart"></i>
                                    <span class="likes-count">{{ $blogLikesCount }}</span>
                                </button>
                            @else
                                <button class="btn btn-outline-primary" @if ($interactionsDisabled) disabled @endif
                                    data-auth-required>
                                    <i class="far fa-heart"></i>
                                    <span>{{ $blogLikesCount }}</span>
                                </button>
                            @endif
                            <button class="btn btn-outline-secondary scroll-to-comments"
                                @if ($interactionsDisabled) disabled @endif>
                                <i class="fa fa-comment"></i>
                                <span>{{ $directCommentsCount }}</span>
                            </button>
                        </div>

                        <div class="d-flex gap-2">
                            @if ($canBookmark)
                                @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-secondary bookmark-btn" type="button"
                                    data-blog-id="{{ $blog->id }}"
                                    data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}">
                                    <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-secondary" type="button"
                                    @if ($interactionsDisabled) disabled @endif data-auth-required>
                                    <i class="far fa-bookmark"></i>
                                </button>
                            @endif

                            @if ($canShare)
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item copy-link" href="#"
                                                data-url="{{ route('blogs.show', $blog) }}">
                                                <i class="fa fa-copy"></i> Copier le lien
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary" type="button"
                                        @if ($interactionsDisabled) disabled @endif data-bs-toggle="dropdown">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item copy-link" href="#"
                                                data-url="{{ route('blogs.show', $blog) }}">
                                                <i class="fa fa-copy"></i> Copier le lien
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            @if ($canEdit)
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-outline-secondary"
                                    title="Modifier">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif

                            @if ($canDelete)
                                <button class="btn btn-outline-danger delete-blog-btn" data-blog-id="{{ $blog->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="blog-content mb-5">
                        {!! $blog->content !!}
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            @if ($canLike)
                                @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                                <button class="btn btn-outline-primary like-btn" data-likeable-id="{{ $blog->id }}"
                                    data-likeable-type="App\Models\Blog"
                                    data-liked="{{ $blogLiked ? 'true' : 'false' }}">
                                    <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart"></i>
                                    <span class="likes-count">{{ $blogLikesCount }}</span>
                                </button>
                            @else
                                <button class="btn btn-outline-primary" @if ($interactionsDisabled) disabled @endif
                                    data-auth-required>
                                    <i class="far fa-heart"></i>
                                    <span>{{ $blogLikesCount }}</span>
                                </button>
                            @endif
                            <button class="btn btn-outline-secondary scroll-to-comments"
                                @if ($interactionsDisabled) disabled @endif>
                                <i class="fa fa-comment"></i>
                                <span>{{ $directCommentsCount }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4" id="comments-section">
                <div class="card-body p-4">
                    <h4 class="mb-4">Commentaires</h4>

                    @if ($interactionsDisabled)
                        <div class="alert alert-warning">
                            <i class="fa fa-info-circle"></i>
                            Les commentaires sont désactivés pour ce blog.
                        </div>
                    @else
                        @if ($canComment)
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                    {{ Auth::user()->initials }}
                                </div>
                                <div class="flex-grow-1">
                                    <form class="comment-form" data-blog-id="{{ $blog->id }}" data-parent-id="">
                                        <div class="input-group">
                                            <input type="text" class="form-control comment-input"
                                                placeholder="Ajouter un commentaire..." required>
                                            <button type="submit" class="btn btn-primary">
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
                        @endif

                        <div id="comments-list">
                            @include('blogs.partials.comments', [
                                'comments' => $blog->comments,
                                'level' => 0,
                            ])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($canLike)
        @vite('resources/js/blogs/components/like.js')
    @endif

    @if ($canBookmark)
        @vite('resources/js/blogs/components/bookmark.js')
    @endif

    @if ($canShare)
        @vite('resources/js/blogs/components/share.js')
    @endif

    @if ($canDelete)
        @vite('resources/js/blogs/components/delete.js')
    @endif

    @if (!$interactionsDisabled)
        @vite('resources/js/blogs/components/comments.js')
    @endif

    @if ($blog->isDraft() && $canEdit)
        @vite('resources/js/blogs/pages/edit.js')
    @endif
@endpush
