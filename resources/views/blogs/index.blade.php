@php
    $layout = Auth::check() ? 'layouts.blog' : 'layouts.blog-guest';
@endphp

@extends($layout)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="flex-grow-1" style="max-width: 500px;">
                    <form action="{{ route('blogs.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un blog..."
                            value="{{ $search ?? '' }}">
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
                <div>
                    @auth
                        <a href="{{ route('blogs.create') }}" class="btn btn-success">
                            <i class="fa fa-pen"></i> Écrire un blog
                        </a>
                    @else
                        <button class="btn btn-success" data-auth-required>
                            <i class="fa fa-pen"></i> Écrire un blog
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($blogs as $blog)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex align-items-start mb-3">
                                    <a href="{{ route('users.profile.show', ['userId' => $blog->user_id]) }}"
                                        class="text-decoration-none">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                            {{ $blog->user->initials }}
                                        </div>
                                    </a>
                                    <div class="flex-grow-1">
                                        <a href="{{ route('users.profile.show', ['userId' => $blog->user_id]) }}"
                                            class="text-decoration-none">
                                            <h6 class="mb-0 text-dark">{{ $blog->user->full_name }}</h6>
                                        </a>
                                        <a href="{{ route('blogs.show', $blog) }}" class="text-decoration-none">
                                            <h5 class="mt-2 mb-2 text-dark">{{ $blog->title }}</h5>
                                            <p class="text-muted mb-2">{{ Str::limit($blog->short_description, 150) }}</p>
                                        </a>
                                        <div class="d-flex gap-3 text-muted small">
                                            <span><i class="fa fa-clock"></i> {{ $blog->time_ago }}</span>
                                            <span><i class="fas fa-heart"></i>
                                                {{ $blog->likes_count ?? $blog->likes->count() }}</span>
                                            <span><i class="fa fa-comment"></i>
                                                {{ $blog->comments_count ?? $blog->comments->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center justify-content-end gap-2">
                                @auth
                                    @php $bookmarked = ($blog->bookmarked_by_auth ?? 0) > 0; @endphp
                                    <button class="btn btn-outline-secondary bookmark-btn" data-blog-id="{{ $blog->id }}"
                                        data-bookmarked="{{ $bookmarked ? 'true' : 'false' }}">
                                        <i class="{{ $bookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary" data-auth-required>
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                @endauth

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary" data-bs-toggle="dropdown">
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

                                @auth
                                    @if (Auth::user()->isAdmin() || Auth::id() === $blog->user_id)
                                        <div class="d-flex gap-2">
                                            @if (Auth::id() === $blog->user_id)
                                                <a href="{{ route('blogs.edit', $blog) }}" class="btn btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif

                                            <button class="btn btn-outline-danger delete-blog-btn"
                                                data-blog-id="{{ $blog->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun blog trouvé.</p>
                    </div>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $blogs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/blogs/app.js')

        @auth
            @if (Auth::user()->isAdmin())
                @vite('resources/js/blogs/admin.js')
            @endif
        @endauth
    @endpush
@endsection
