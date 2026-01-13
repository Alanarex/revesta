@extends('layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid px-0">
            {{-- Modern Cover + Profile Header --}}
            <div class="profile-header-modern mb-4">
                {{-- Cover Image --}}
                <div class="profile-cover">
                    <div class="cover-gradient"></div>
                </div>

                {{-- Profile Info Bar --}}
                <div class="container-fluid px-0">
                    <div class="profile-info-bar">
                        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                            {{-- Avatar --}}
                            <div class="profile-avatar-wrapper">
                                <div class="profile-avatar">
                                    <span class="avatar-text">{{ $user->initials }}</span>
                                </div>
                            </div>

                            {{-- User Info --}}
                            <div class="flex-grow-1 text-center text-md-start">
                                <h2 class="profile-name mb-1">{{ $user->full_name }}</h2>
                                @if ($user->bio)
                                    <p class="profile-bio mb-2">{{ $user->bio }}</p>
                                @endif
                                <div class="profile-meta small">
                                    <span><i class="fa fa-calendar me-1"></i>Membre depuis
                                        {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="profile-actions">
                                @auth
                                    @can('create', App\Models\Blog::class)
                                        <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                                            <i class="fa fa-pen me-2"></i>Écrire un blog
                                        </a>
                                    @endcan
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            @include('profile.partials.alerts')

            {{-- Stats Cards --}}
            <div class="container-fluid mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon stats-icon-primary">
                                <i class="fa fa-newspaper"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-value">{{ $publishedBlogsCount ?? 0 }}</div>
                                <div class="stats-label">Blogs Publiés</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon stats-icon-danger">
                                <i class="fa fa-heart"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-value">{{ $totalLikes ?? 0 }}</div>
                                <div class="stats-label">Likes Reçus</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon stats-icon-info">
                                <i class="fa fa-comment"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-value">{{ $totalComments ?? 0 }}</div>
                                <div class="stats-label">Commentaires</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="container-fluid">
                <div class="row g-4">
                    <div class="col-12">
                        {{-- Modern Tab Pills --}}
                        <div class="modern-tabs-wrapper mb-4">
                            <ul class="nav modern-tabs" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-info" data-bs-toggle="tab"
                                        data-bs-target="#info-tab-pane" type="button" role="tab">
                                        <i class="fa fa-user me-2"></i>
                                        <span>Informations</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-blogs" data-bs-toggle="tab"
                                        data-bs-target="#blogs-tab-pane" type="button" role="tab">
                                        <i class="fa fa-newspaper me-2"></i>
                                        <span>Mes Blogs</span>
                                        <span
                                            class="badge bg-primary ms-2">{{ $publishedBlogs->count() + $draftBlogs->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-bookmarks" data-bs-toggle="tab"
                                        data-bs-target="#bookmarks-tab-pane" type="button" role="tab">
                                        <i class="fa fa-bookmark me-2"></i>
                                        <span>Signets</span>
                                        <span class="badge bg-warning ms-2">{{ $bookmarks->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {{-- Tab Content --}}
                        <div class="tab-content">
                            {{-- Info Tab --}}
                            <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        @include('profile.partials.info-tab')
                                    </div>
                                </div>
                            </div>

                            {{-- Blogs Tab --}}
                            <div class="tab-pane fade show" id="blogs-tab-pane" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        @include('profile.partials.blogs-tab')
                                    </div>
                                </div>
                            </div>

                            {{-- Bookmarks Tab --}}
                            <div class="tab-pane fade show" id="bookmarks-tab-pane" role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        @include('profile.partials.bookmarks-tab')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Modern Profile Header */
        .profile-header-modern {
            position: relative;
            margin: -2rem 0 0;
        }

        .profile-cover {
            height: 200px;
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cover-gradient {
            position: absolute;
            inset: 0;
            /* Add stronger gradient at bottom for text visibility */
            background:
                linear-gradient(to bottom,
                    transparent 0%,
                    transparent 40%,
                    rgba(0, 0, 0, 0.3) 70%,
                    rgba(0, 0, 0, 0.5) 100%),
                linear-gradient(135deg,
                    rgba(99, 102, 241, 0.9) 0%,
                    rgba(139, 92, 246, 0.9) 50%,
                    rgba(236, 72, 153, 0.9) 100%);
        }

        .profile-info-bar {
            position: relative;
            margin-top: -60px;
            padding: 0 2rem 1.5rem;
            background: linear-gradient(to bottom,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.7) 30%,
                    rgba(255, 255, 255, 0.95) 60%,
                    rgba(255, 255, 255, 1) 80%);
        }

        .profile-avatar-wrapper {
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
        }

        .avatar-text {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 0.5rem;
            color: #1a1a1a;
        }

        .profile-bio {
            font-size: 0.95rem;
            max-width: 600px;
            color: #4a5568;
        }

        .profile-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            color: #6b7280;
            font-weight: 500;
        }

        .profile-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stats-icon-primary {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(99, 102, 241, 0.2));
            color: var(--bs-primary);
        }

        .stats-icon-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.2));
            color: var(--bs-danger);
        }

        .stats-icon-info {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(14, 165, 233, 0.2));
            color: var(--bs-info);
        }

        .stats-content {
            flex: 1;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stats-label {
            font-size: 0.875rem;
            color: var(--bs-secondary);
            font-weight: 500;
        }

        /* Modern Tabs */
        .modern-tabs-wrapper {
            background: white;
            border-radius: 1rem;
            padding: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .modern-tabs {
            border: none;
            gap: 0.5rem;
        }

        .modern-tabs .nav-item {
            flex: 1;
        }

        .modern-tabs .nav-link {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--bs-secondary);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: transparent;
        }

        .modern-tabs .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--bs-primary);
        }

        .modern-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
            color: white;
        }

        .modern-tabs .nav-link.active .badge {
            background: rgba(255, 255, 255, 0.3) !important;
            color: white;
        }

        color: white;
        }

        .modern-tabs .nav-link.active .badge {
            background: rgba(255, 255, 255, 0.3) !important;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header-modern {
                margin: -1rem 0 0;
            }

            .profile-cover {
                height: 150px;
            }

            .profile-info-bar {
                padding: 0 1rem 1rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .avatar-text {
                font-size: 2rem;
            }

            .profile-name {
                font-size: 1.5rem;
            }

            .modern-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            .modern-tabs .nav-link span:not(.badge) {
                display: none;
            }

            .modern-tabs .nav-link i {
                margin: 0;
            }

            .stats-card {
                padding: 1rem;
            }

            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }

            .stats-value {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@include('profile.partials.scripts')
@include('profile.partials.styles')
