@extends('layouts.app')

@section('content')
    @php
        // Calculate routes first for meta tags
        $canEdit =
            auth()->check() &&
            (auth()->id() === $user->id ||
                (auth()->user()->role && auth()->user()->role->name === 'admin'));
        $isAdmin = auth()->check() && (auth()->user()->role && auth()->user()->role->name === 'admin');
        $updateRoute =
            $isAdmin && !$isViewingOwn
                ? route('admin.users.update', $user)
                : route('users.update', $user);
        $resetRoute =
            $isAdmin && !$isViewingOwn
                ? route('admin.users.reset-password', $user)
                : route('users.reset-password', $user);

        $destroyRoute =
            $isAdmin && !$isViewingOwn
                ? route('admin.users.destroy', $user)
                : route('users.destroy', $user);
    @endphp

    <div class="main-content">
        <div class="container-fluid px-0">
            @include('admin.users.partials.alerts')

            {{-- Meta tags for AJAX routes --}}
            <meta name="update-route" content="{{ $updateRoute ?? '' }}">

            <meta name="reset-route" content="{{ $resetRoute ?? '' }}">
            <meta name="destroy-route" content="{{ $destroyRoute ?? '' }}">

            {{-- Three-column layout: left profile, middle content, right activity --}}
            <div class="row g-4">
                {{-- First column --}}
                <div class="col-12 col-lg-3">
                    {{-- Profile header card --}}
                    <div class="card rounded shadow-sm mb-3 ">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:64px;height:64px;font-size:20px;">
                                {{ $user->initials ?? strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold fs-5">{{ $user->full_name }}</div>
                                    @if ($user->role?->name)
                                        <span class="badge bg-info text-dark small">{{ $user->role->name }}</span>
                                    @endif
                                    @if (method_exists($user, 'trashed') && $user->trashed())
                                        <span class="badge bg-danger small">Désactivé</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">Membre depuis {{ $user->created_at?->format('M Y') }}
                                </div>

                                {{-- Profile completion score --}}
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between small text-muted">
                                        <div>Profile: {{ $profileScore ?? 0 }}%</div>
                                        <div>{{ $profileScore ?? 0 }}%</div>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $profileScore ?? 0 }}%;"
                                            aria-valuenow="{{ $profileScore ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- General info card --}}
                    <div class="card rounded shadow-sm mb-3 ">
                        <div class="card-header">Informations générales</div>
                        <div class="card-body">
                            <form id="updateInfoForm">
                                <div class="mb-2">
                                    <label class="form-label">Prénom</label>
                                    <input class="form-control" name="first_name"
                                        value="{{ old('first_name', $user->first_name) }}"
                                        {{ $canEdit ? '' : 'readonly' }}>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Nom</label>
                                    <input class="form-control" name="last_name"
                                        value="{{ old('last_name', $user->last_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" name="email" value="{{ old('email', $user->email) }}"
                                        {{ $canEdit ? '' : 'readonly' }}>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Téléphone</label>
                                    <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}"
                                        {{ $canEdit ? '' : 'readonly' }}>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Statut civil</label>
                                    <input class="form-control" name="civil_status"
                                        value="{{ old('civil_status', $user->civil_status) }}"
                                        {{ $canEdit ? '' : 'readonly' }}>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Statut familial</label>
                                    <input class="form-control" name="family_status"
                                        value="{{ old('family_status', $user->family_status) }}"
                                        {{ $canEdit ? '' : 'readonly' }}>
                                </div>

                                @if ($canEdit)
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary btn-sm" type="submit"
                                            id="saveInfoBtn">Enregistrer</button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- Address card if exists --}}
                    @if ($user->address)
                        <div class="card rounded shadow-sm mb-3 ">
                            <div class="card-header">Adresse</div>
                            <div class="card-body small text-muted">
                                <div>{{ $user->address->street ?? '-' }}</div>
                                <div>{{ $user->address->city ?? '' }} {{ $user->address->postal_code ?? '' }}</div>
                                <div>{{ $user->address->country ?? '' }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Password reinitialization card (Admin only) --}}
                    @if ($isAdmin && auth()->id() !== $user->id)
                        <div class="card rounded shadow-sm mb-3 ">
                            <div class="card-header">Réinitialisation du mot de passe</div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted small mb-0">Réinitialiser le mot de passe de cet utilisateur</p>
                                    <button class="btn btn-warning btn-sm" id="resetPasswordAdminBtn">
                                        Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Change password card (Own profile) --}}
                    @if ($canEdit && !($isAdmin && auth()->id() !== $user->id))
                        <div class="card rounded shadow-sm mb-3 ">
                            <div class="card-header">Modifier le mot de passe</div>
                            <div class="card-body">
                                <form id="changePasswordForm">
                                    <div class="mb-2">
                                        <label class="form-label small">Ancien mot de passe</label>
                                        <input type="password" name="current_password" class="form-control form-control-sm"
                                            required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Nouveau mot de passe</label>
                                        <input type="password" name="password" class="form-control form-control-sm"
                                            required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Confirmer le mot de passe</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control form-control-sm" required>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button class="btn btn-primary btn-sm" type="submit"
                                            id="changePasswordBtn">Mettre à jour</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Delete account card --}}
                    @if ($canEdit)
                        <div class="card rounded shadow-sm mb-3  border-danger">
                            <div class="card-header bg-danger text-white">Supprimer le compte</div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted small mb-0">Cette action est irréversible. Toutes les données
                                        seront supprimées.</p>
                                    <button type="button" class="btn btn-danger btn-sm text-white"
                                        id="deleteAccountBtn">Supprimer</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right column col-9 --}}
                <div class="col-12 col-lg-9">
                    {{-- Simulations section (full width) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            {{-- Simulations section --}}
                            <div class="card rounded shadow-sm mb-3 ">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Simulations</span>
                                    @if (($simulations ?? collect())->count() > 3)
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-secondary simulations-prev"
                                                type="button">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary simulations-next"
                                                type="button">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if (($simulations ?? collect())->isEmpty())
                                        <div class="text-muted">Aucune simulation trouvée.</div>
                                    @else
                                        <div class="simulations-carousel-wrapper position-relative overflow-hidden">
                                            <div class="simulations-carousel d-flex gap-3 transition-transform"
                                                style="transition: transform 0.3s ease;">
                                                @foreach ($simulations as $sim)
                                                    <div class="simulation-card flex-shrink-0" style="width: 200px;">
                                                        <div class="card h-100 shadow-sm" style="aspect-ratio: 1/1;">
                                                            <div
                                                                class="card-body d-flex flex-column justify-content-between p-3">
                                                                <div>
                                                                    <div class="fw-bold mb-2" style="font-size: 0.9rem;">
                                                                        {{ Str::limit($sim->title ?? 'Simulation', 40) }}
                                                                    </div>
                                                                    <div class="small text-muted"
                                                                        style="font-size: 0.75rem;">
                                                                        {{ Str::limit($sim->summary ?? '', 60) }}</div>
                                                                </div>
                                                                <div class="mt-auto">
                                                                    <div class="small text-muted"
                                                                        style="font-size: 0.7rem;">
                                                                        <i class="far fa-calendar"></i>
                                                                        {{ $sim->created_at?->format('d/m/Y') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Blogs and Activity section --}}
                            <div class="row g-4">
                                {{-- Blogs column --}}
                                <div class="col-12 col-lg-8">
                                    {{-- Blogs list and status filters --}}
                                    <div class="card rounded shadow-sm ">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span>Blogs</span>
                                                    @if ($isViewingOwn)
                                                        <span class="badge bg-secondary" id="blogs-count">{{ ($blogsList ?? collect())->count() }}</span>
                                                    @endif
                                                </div>
                                                
                                                @if ($isViewingOwn && $isAdmin)
                                                    @auth
                                                        @can('create', App\Models\Blog::class)
                                                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-pen me-1"></i> Écrire un blog
                                                            </a>
                                                        @endcan
                                                    @endauth
                                                @endif
                                            </div>
                                            
                                            @if ($isViewingOwn)
                                                <ul class="nav nav-pills mt-3 flex-wrap" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" data-bs-toggle="pill" data-filter="all" type="button">Tous</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="pill" data-filter="published" type="button">Publié</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="pill" data-filter="draft" type="button">Brouillon</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="pill" data-filter="pending" type="button">En attente</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="pill" data-filter="rejected" type="button">Rejeté</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="pill" data-filter="bookmarked" type="button">
                                                            <i class="fas fa-bookmark"></i> Signets
                                                        </button>
                                                    </li>
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="card-body" id="blogs-container">
                                            @if (($blogsList ?? collect())->isEmpty())
                                                <div class="text-muted">Aucun blog disponible.</div>
                                            @else
                                                <div class="blogs-list">
                                                    @foreach ($blogsList as $index => $blog)
                                                        @php
                                                            $isBookmarked = ($blog->bookmarked_by_auth ?? $blog->is_bookmarked ?? false) ? true : false;
                                                        @endphp
                                                        <div class="user-blog-wrapper" data-index="{{ $index }}" data-blog-status="{{ $blog->status }}"
                                                            data-is-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                                                            data-blog-id="{{ $blog->id }}">
                                                            @include('admin.blogs.partials.card', [
                                                                'blog' => $blog,
                                                                'canBookmark' => $blog->isPublished(),
                                                                'canShare' => $blog->isPublished(),
                                                                'canEdit' => $canEdit,
                                                                'canDelete' => $canEdit,
                                                                'canCheckbox' => false,
                                                                'canApproveReject' => false,
                                                                'canAuthor' => true,
                                                            ])
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if (($blogsList ?? collect())->count() > 4)
                                                    <div class="d-grid mt-3">
                                                        <button class="btn btn-sm btn-outline-primary" id="showAllBlogsBtn">Voir tous les blogs ({{ ($blogsList ?? collect())->count() - 4 }} restants)</button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Activity column --}}
                                <div class="col-12 col-lg-4">
                                    <div class="card rounded shadow-sm ">
                                        <div class="card-header">Activité dernière semaine</div>
                                        <div class="card-body small text-muted">
                                            @if (($recentActivity ?? collect())->isEmpty())
                                                <div class="text-muted">Aucune activité récente.</div>
                                            @else
                                                <ul class="list-unstyled mb-0 activity-list">
                                                    @foreach ($recentActivity as $index => $act)
                                                        <li class="mb-2 activity-item"
                                                            style="{{ $index >= 10 ? 'display: none;' : '' }}">
                                                            <a href="{{ $act['url'] ?? '#' }}"
                                                                class="text-decoration-none">{{ $act['label'] }}</a>
                                                            <div class="small text-muted">
                                                                {{ optional($act['created_at'])->diffForHumans() ?? '' }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if (($recentActivity ?? collect())->count() > 10)
                                                    <button
                                                        class="btn btn-sm btn-outline-primary w-100 mt-3 activity-expand-btn">
                                                        Voir plus d'activités
                                                        ({{ ($recentActivity ?? collect())->count() - 10 }}
                                                        restantes)
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @include('admin.users.partials.scripts')
        @include('admin.users.partials.styles')
