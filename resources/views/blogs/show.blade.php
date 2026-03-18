@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <article class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="display-6 fw-semibold mb-3">{{ $blog->title }}</h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-4">
                        <span>
                            <i class="fas fa-user me-1"></i>
                            {{ $blog->user?->full_name ?? trim(($blog->user?->first_name ?? '') . ' ' . ($blog->user?->last_name ?? '')) }}
                        </span>
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $blog->published_at?->format('d/m/Y H:i') ?? $blog->created_at?->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <p class="lead text-muted">{{ $blog->short_description }}</p>

                    <hr class="my-4">

                    <div class="blog-content">
                        {!! $blog->content !!}
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour au tableau de bord
                        </a>
                        <span class="text-muted small">
                            <i class="fas fa-comments me-1"></i> {{ $blog->allComments()->count() }} commentaires
                        </span>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection
