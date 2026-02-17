@extends('layouts.blogs')

@section('content')



    <div class="row">
        <div class="col-lg-10 mx-auto">

            {{-- Status Alert --}}
            @if ($statusAlert)
                <x-forms.alert :type="$statusAlert['type'] ?? 'info'" :title="null" :dismissible="false">
                    {{ $statusAlert['message'] }}

                    @if ($statusAlert['action'])
                        <a href="#" class="publish-draft-link ms-2 text-primary" data-blog-id="{{ $blog->id }}"
                            data-publish-url="{{ $statusAlert['action']['url'] }}"
                            data-csrf-token="{{ $statusAlert['action']['csrfToken'] }}">
                            <small>{{ $statusAlert['action']['label'] }}</small>
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                            {{ $blog->user->initials }}
                        </div>
            <!-- Breadcrumbs -->
            <div class="mb-4">
                <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
            </div>

            <meta name="blog-id" content="{{ $blog->id }}">
            <h1 class="mb-3">{{ $blog->title }}</h1>
            <p class="lead text-muted mb-4">{{ $blog->short_description }}</p>

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                        {{ $blog->user->initials }}
                    </div>
                </a>
                <div>
                    <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                        <h6 class="mb-0">{{ $blog->user->full_name }}</h6>
                    </a>
                    <div>
                        <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                            <h6 class="mb-0">{{ $blog->user->full_name }}</h6>
                        </a>
                    @endif

                    @if ($statusAlert['reason'])
                        <div class="mt-2">
                            <strong>Raison :</strong> {{ $statusAlert['reason'] }}
                        </div>
                    @endif
                </x-forms.alert>
            @endif

            <!-- Breadcrumbs -->
            <div class="mb-4">
                <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
            </div>

            <meta name="blog-id" content="{{ $blog->id }}">
            <h1 class="mb-3">{{ $blog->title }}</h1>
            <p class="lead text-muted mb-4">{{ $blog->short_description }}</p>

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                        {{ $blog->user->initials }}
                    </div>
                </a>
                <div>
                    <a href="{{ route('admin.users.show', ['user' => $blog->user_id]) }}" class="text-decoration-none">
                        <h6 class="mb-0">{{ $blog->user->full_name }}</h6>
                    </a>
                    <small class="text-muted">{{ $blog->time_ago }}</small>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex gap-4 align-items-center text-muted">
                    @if ($canLike)
                        @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                        <button
                            class="btn btn-link p-0 like-btn text-decoration-none text-danger d-flex flex-column align-items-center"
                            data-likeable-id="{{ $blog->id }}" data-likeable-type="Blog"
                            data-liked="{{ $blogLiked ? 'true' : 'false' }}">
                            <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart" style="font-size: 1.5rem;"></i>
                            <span class="likes-count mt-2 small">{{ $blog->likes_count }}</span>
                        </button>
                    @else
                        <button
                            class="btn btn-link p-0 text-decoration-none text-danger d-flex flex-column align-items-center"
                            @if ($interactionsDisabled) disabled @endif data-auth-required>
                            <i class="far fa-heart" style="font-size: 1.5rem;"></i>
                            <span class="mt-2 small">{{ $blog->likes_count }}</span>
                        </button>
                    @endif
                    <button
                        class="btn btn-link p-0 scroll-to-comments text-decoration-none text-muted d-flex flex-column align-items-center"
                        @if ($interactionsDisabled) disabled @endif>
                        <i class="fa fa-comment" style="font-size: 1.5rem;"></i>
                        <span class="mt-2 small">{{ $blog->comments_count }}</span>
                    </button>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    @if ($canBookmark)
                        @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                        <button class="btn btn-link p-0 bookmark-btn text-decoration-none text-muted" type="button"
                            data-blog-id="{{ $blog->id }}" data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}"
                            title="Ajouter aux signets">
                            <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark" style="font-size: 1.5rem;"></i>
                        </button>
                    @else
                        <button class="btn btn-link p-0 text-decoration-none text-muted" type="button"
                            @if ($interactionsDisabled) disabled @endif data-auth-required title="Connexion requise">
                            <i class="far fa-bookmark" style="font-size: 1.5rem;"></i>
                        </button>
                    @endif

                    <!-- More Options Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-decoration-none text-muted" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Plus d'options">
                            <i class="fas fa-ellipsis-v" style="font-size: 1.5rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if ($canShare)
                                <li>
                                    <a class="dropdown-item copy-link" href="#"
                                        data-url="{{ route('admin.blogs.show', $blog) }}">
                                        <i class="fa fa-copy me-2"></i> Copier le lien
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif

                            @if ($canEdit)
                                <li>
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="dropdown-item">
                                        <i class="fa fa-edit me-2"></i> Modifier
                                    </a>
                                </li>
                            @endif

                            @if ($canDelete)
                                <li>
                                    <button class="dropdown-item text-danger delete-blog-btn" type="button"
                                        data-blog-id="{{ $blog->id }}"
                                        data-delete-url="{{ route('admin.blogs.destroy', $blog) }}">
                                        <i class="fa fa-trash me-2"></i> Supprimer
                                    </button>
                                </li>
                            @endif

                            @if ($canApprove || $canReject)
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif

                            @if ($canApprove)
                                <li>
                                    <button type="button" class="dropdown-item text-success approve-single-btn"
                                        data-blog-id="{{ $blog->id }}">
                                        <i class="fa fa-check me-2"></i> Approuver
                                    </button>
                                </li>
                            @endif

                            @if ($canReject)
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

            <hr>

            <div class="blog-content mb-5">
                {!! $blog->content !!}
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="d-flex gap-4 text-muted">
                    @if ($canLike)
                        @php $blogLiked = ($blog->liked_by_auth ?? 0) > 0; @endphp
                        <button
                            class="btn btn-link p-0 like-btn text-decoration-none text-danger d-flex flex-column align-items-center"
                            data-likeable-id="{{ $blog->id }}" data-likeable-type="Blog"
                            data-liked="{{ $blogLiked ? 'true' : 'false' }}">
                            <i class="{{ $blogLiked ? 'fas' : 'far' }} fa-heart" style="font-size: 1.5rem;"></i>
                            <span class="likes-count mt-2 small">{{ $blog->likes_count }}</span>
                        </button>
                    @else
                        <button
                            class="btn btn-link p-0 text-decoration-none text-danger d-flex flex-column align-items-center"
                            @if ($interactionsDisabled) disabled @endif data-auth-required>
                            <i class="far fa-heart" style="font-size: 1.5rem;"></i>
                            <span class="mt-2 small">{{ $blog->likes_count }}</span>
                        </button>
                    @endif
                    <button
                        class="btn btn-link p-0 scroll-to-comments text-decoration-none text-muted d-flex flex-column align-items-center"
                        @if ($interactionsDisabled) disabled @endif>
                        <i class="fa fa-comment" style="font-size: 1.5rem;"></i>
                        <span class="mt-2 small">{{ $blog->comments_count }}</span>
                    </button>
                </div>
            </div>

            <div class="mt-4" id="comments-section">
                <h4 class="mb-4">Commentaires</h4>
                @if ($interactionsDisabled)
                    <x-forms.alert type="warning" :dismissible="false">
                        Les commentaires sont désactivés pour ce blog.
                    </x-forms.alert>
                @else
                    @if ($canComment)
                        @include('admin.blogs.partials.comments.form', [
                            'blogId' => $blog->id,
                            'parentId' => '',
                            'placeholder' => 'Ajouter un commentaire...',
                            'showAvatar' => true,
                            'userInitials' => Auth::user()->initials,
                        ])
                    @else
                        <x-forms.alert type="info" :dismissible="false">
                            <a href="{{ route('login') }}">Connectez-vous</a> pour commenter.
                        </x-forms.alert>
                    @endif

                    <div id="comments-list">
                        @include('admin.blogs.partials.comments.list', [
                            'comments' => $blog->comments,
                            'level' => 0,
                        ])
                    </div>
                @endif

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
