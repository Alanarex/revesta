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
            {{-- Hero section --}}
            @include('profile.partials.hero')

            <div class="row g-4">
                {{-- Main Content --}}
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Blogs Publiés</h5>
                            
                            @forelse($publishedBlogs as $blog)
                                @include('blogs.partials.card', [
                                    'blog' => $blog,
                                    'showAuthor' => false,
                                    'showBookmark' => true,
                                    'showShare' => true,
                                    'showEdit' => false,
                                    'showDelete' => false,
                                ])
                            @empty
                                <div class="text-center py-5">
                                    <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Cet utilisateur n'a pas encore publié de blog.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                @include('profile.partials.sidebar')
            </div>
        </div>
    </div>
@endsection

@include('profile.partials.scripts')
@include('profile.partials.styles')
