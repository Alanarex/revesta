@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Mes signets</h1>
        <a href="{{ route('blogs.index') }}" class="btn btn-outline-secondary">Découvrir des articles</a>
    </div>

    @if($bookmarks->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted">
                Vous n'avez aucun signet pour le moment. Découvrez des articles et ajoutez des signets.
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($bookmarks as $bookmark)
                @php $b = $bookmark->blog; @endphp
                @if($b)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1"><a href="{{ route('blogs.show', $b->slug ?? $b->id) }}">{{ $b->title }}</a></h5>
                                <p class="text-muted mb-1">par {{ $b->user?->full_name ?? 'Auteur' }} — {{ $b->created_at?->diffForHumans() }}</p>
                                <p class="mb-0 text-truncate" style="max-height:3.6em;overflow:hidden">{!! Str::limit(strip_tags($b->short_description ?? $b->content), 220) !!}</p>
                            </div>
                                <div class="ms-3 text-end">
                                <button class="btn btn-sm btn-outline-danger bookmark-toggle-btn" type="button" data-blog-id="{{ $b->id }}" data-toggle-url="{{ route('blogs.bookmarks.toggle') }}">Retirer</button>
                                <a href="{{ route('blogs.show', $b->slug ?? $b->id) }}" class="btn btn-sm btn-primary mt-2">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4">
            {{ $bookmarks->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
@vite('resources/js/blogs/bookmarks.fixed.js')
@endpush
