@extends('layouts.app')

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
                                    'canAuthor' => false,
                                    'canBookmark' => true,
                                    'canShare' => true,
                                    'canEdit' => false,
                                    'canDelete' => false,
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
