@extends('layouts.blog')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Révision du blog</h4>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body p-5">
                    <meta name="admin-blogs-index" content="{{ route('admin.blogs.index') }}">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Ce blog est en attente d'approbation par <strong><a
                                                    href="{{ route('profile.show', ['userId' => $blog->user_id]) }}">{{ $blog->user->full_name }}</a></strong>.
                    </div>

                    <h1 class="mb-3">{{ $blog->title }}</h1>
                    <p class="lead text-muted mb-4">{{ $blog->short_description }}</p>

                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                            class="text-decoration-none me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ $blog->user->initials }}
                            </div>
                        </a>
                        <div>
                            <h6 class="mb-0"><a href="{{ route('profile.show', ['userId' => $blog->user_id]) }}"
                                       class="text-decoration-none">{{ $blog->user->full_name }}</a></h6>
                            <small class="text-muted">{{ $blog->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="blog-content mb-5">
                        {!! $blog->content !!}
                    </div>

                    <hr>

                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-danger" onclick="rejectBlog({{ $blog->id }})">
                            <i class="fa fa-times"></i> Refuser
                        </button>
                        <button class="btn btn-success" onclick="approveBlog({{ $blog->id }})">
                            <i class="fa fa-check"></i> Approuver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/blogs/show.js')
@endpush
