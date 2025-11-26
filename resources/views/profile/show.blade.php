@php
    // Use the authenticated app layout when the viewer is authenticated,
    // otherwise use the guest blog layout so guests don't see authenticated UI.
    $useAuthLayout = isset($isAuthenticated) ? (bool) $isAuthenticated : auth()->check();
    $layout = $useAuthLayout ? 'layouts.app' : 'layouts.blog-guest';
@endphp

@extends($layout)

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{-- Optional alerts could be shown here if needed --}}

            <div class="row g-4">
                <!-- Main Content (blogs) -->
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa fa-newspaper"></i> Blogs publiés
                                <span class="badge bg-primary ms-2">{{ $publishedBlogs->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @forelse($publishedBlogs as $blog)
                                <div class="mb-4 pb-4 border-bottom">
                                    <a href="{{ route('blogs.show', $blog) }}" class="text-decoration-none">
                                        <h5 class="text-dark">{{ $blog->title }}</h5>
                                        <p class="text-muted">{{ Str::limit($blog->short_description, 150) }}</p>
                                    </a>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fa fa-clock"></i> {{ $blog->time_ago }}</span>
                                        <span><i class="fas fa-heart"></i> {{ $blog->likes_count ?? $blog->likes->count() }}</span>
                                        <span><i class="fa fa-comment"></i> {{ $blog->comments_count ?? $blog->comments->count() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun blog publié pour le moment.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar (user info) --}}
                @include('profile.partials.sidebar')
            </div>
        </div>
    </div>
@endsection

@include('profile.partials.scripts')
@include('profile.partials.styles')
