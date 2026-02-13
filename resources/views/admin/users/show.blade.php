@extends('layouts.app')

@section('content')
    @php
        // Calculate routes first for meta tags
        $canEdit =
            auth()->check() &&
            (auth()->id() === $user->id || (auth()->user()->role && auth()->user()->role->name === 'admin'));
        $isAdmin = auth()->check() && (auth()->user()->role && auth()->user()->role->name === 'admin');
        $updateRoute = $isAdmin && !$isViewingOwn ? route('admin.users.update', $user) : route('users.update', $user);
        $resetRoute =
            $isAdmin && !$isViewingOwn
                ? route('admin.users.reset-password', $user)
                : route('users.reset-password', $user);

        $destroyRoute =
            $isAdmin && !$isViewingOwn ? route('admin.users.destroy', $user) : route('users.destroy', $user);

        // Load user config options
        $civilStatuses = config('users.civil_statuses');
        $familyStatuses = config('users.family_statuses');
    @endphp

    <div class="main-content">
        <div class="container-fluid px-0">
            @include('admin.users.partials.alerts')

            {{-- Meta tags for AJAX routes --}}
            <meta name="update-route" content="{{ $updateRoute ?? '' }}">

            <meta name="reset-route" content="{{ $resetRoute ?? '' }}">
            <meta name="destroy-route" content="{{ $destroyRoute ?? '' }}">

            {{-- Three-column layout: left profile, middle content, right activity --}}
            <div class="row g-4" style="height: fit-content;">
                {{-- First column --}}
                <div class="col-12 col-lg-3">
                    {{-- Profile header card --}}
                    <x-layout.card class="shadow-sm" body-class="d-flex align-items-center gap-3">
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
                    </x-layout.card>

                    {{-- General info card --}}
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Informations générales</x-slot>
                        <x-forms.form formId="updateInfoForm" action="{{ $updateRoute }}" method="PUT">
                                <x-inputs.text-input label="Prénom" name="first_name" value="{{ $user->first_name }}"
                                    icon="fa-solid fa-user" placeholder="Entrez votre prénom" :readonly="!$canEdit" />
                                <x-inputs.text-input label="Nom" name="last_name" value="{{ $user->last_name }}"
                                    icon="fa-solid fa-user" placeholder="Entrez votre nom" :readonly="!$canEdit" />
                                <x-inputs.email-input label="Email" name="email" value="{{ $user->email }}"
                                    icon="fa-solid fa-envelope" placeholder="exemple@email.com" :readonly="!$canEdit" />
                                <x-inputs.text-input label="Téléphone" name="phone" value="{{ $user->phone }}"
                                    icon="fa-solid fa-phone" placeholder="Votre numéro de téléphone" :readonly="!$canEdit" />
                                @if ($isAdmin && !empty($rolesOptions))
                                    <x-inputs.select-input label="Rôle" name="role_id"
                                        value="{{ $user->role_id }}" icon="fa-solid fa-user-shield"
                                        placeholder="Selectionner un role" :options="$rolesOptions" :required="true"
                                        :readonly="!$canEdit" />
                                @endif
                                <x-inputs.select-input label="Statut civil" name="civil_status"
                                    value="{{ $user->civil_status }}" icon="fa-solid fa-heart" :options="$civilStatuses"
                                    :readonly="!$canEdit" />
                                <x-inputs.select-input label="Statut familial" name="family_status"
                                    value="{{ $user->family_status }}" icon="fa-solid fa-home" :options="$familyStatuses"
                                    :readonly="!$canEdit" />
                                <x-inputs.textarea-input label="Biographie" name="bio" value="{{ $user->bio ?? '' }}"
                                    icon="fa-solid fa-pen" rows="4" placeholder="Entrez votre biographie..."
                                    :readonly="!$canEdit" />

                                @if ($canEdit)
                                    <div class="d-flex gap-2 justify-content-end pt-4">
                                        <x-buttons.button-primary text="Enregistrer" />
                                    </div>
                                @endif
                        </x-forms.form>
                    </x-layout.card>

                    {{-- Address card if exists --}}
                    @if ($user->address)
                        <x-layout.card class="shadow-sm" body-class="small text-muted">
                            <x-slot name="header">Adresse</x-slot>
                            <div>{{ $user->address->street ?? '-' }}</div>
                            <div>{{ $user->address->city ?? '' }} {{ $user->address->postal_code ?? '' }}</div>
                            <div>{{ $user->address->country ?? '' }}</div>
                        </x-layout.card>
                    @endif

                    {{-- Password reinitialization card (Admin only) --}}
                    @if ($isAdmin && auth()->id() !== $user->id)
                        <x-layout.card class="shadow-sm">
                            <x-slot name="header">Réinitialisation du mot de passe</x-slot>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-muted small mb-0">Réinitialiser le mot de passe de cet utilisateur</p>
                                <x-buttons.button-warning text="Réinitialiser" id="resetPwdBtn" />
                            </div>
                        </x-layout.card>
                    @endif

                    {{-- Change password card (Own profile) --}}
                    @if ($canEdit && !($isAdmin && auth()->id() !== $user->id))
                        <x-layout.card class="shadow-sm">
                            <x-slot name="header">Modifier le mot de passe</x-slot>
                            <x-forms.form formId="changePasswordForm"
                                action="{{ route('users.update-password', $user) }}" method="PUT">
                                    <x-inputs.password-input label="Ancien mot de passe" name="current_password" 
                                        icon="fa-solid fa-key" placeholder="Entrez votre mot de passe" required />
                                    <x-inputs.password-input label="Nouveau mot de passe" name="password" 
                                        icon="fa-solid fa-lock" placeholder="Entrez un nouveau mot de passe" required />
                                    <x-inputs.password-input label="Confirmer le mot de passe" name="password_confirmation"
                                        icon="fa-solid fa-circle-check" placeholder="Confirmez le mot de passe" required />
                                <div class="d-flex justify-content-end mt-2">
                                    <x-buttons.button-primary text="Mettre à jour" />
                                </div>
                            </x-forms.form>
                        </x-layout.card>
                    @endif

                    {{-- Delete account card --}}
                    @if ($canEdit)
                        <x-layout.card class="shadow-sm border-danger" header-class="bg-danger text-white">
                            <x-slot name="header">Supprimer le compte</x-slot>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-muted small mb-0">Cette action est irréversible. Toutes les données
                                    seront supprimées.</p>
                                <x-buttons.button-danger text="Supprimer" id="deleteAccountBtn" />
                            </div>
                        </x-layout.card>
                    @endif
                </div>

                {{-- Right column col-9 --}}
                <div class="col-12 col-lg-9">
                    {{-- Simulations section (full width) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            {{-- Simulations section --}}
                            <x-layout.card class="shadow-sm" header-class="d-flex justify-content-between align-items-center">
                                <x-slot name="header">
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
                                </x-slot>
                                @if (($simulations ?? collect())->isEmpty())
                                    <div class="text-muted">Aucune simulation trouvée.</div>
                                @else
                                    <div class="simulations-carousel-wrapper position-relative overflow-hidden">
                                        <div class="simulations-carousel d-flex gap-3 transition-transform"
                                            style="transition: transform 0.3s ease;">
                                            @foreach ($simulations as $sim)
                                                <div class="simulation-card flex-shrink-0" style="width: 200px;">
                                                    <x-layout.card class="h-100 shadow-sm" body-class="d-flex flex-column justify-content-between p-3"
                                                        style="aspect-ratio: 1/1;">
                                                        <div>
                                                            <div class="fw-bold mb-2" style="font-size: 0.9rem;">
                                                                {{ Str::limit($sim->title ?? 'Simulation', 40) }}
                                                            </div>
                                                            <div class="small text-muted"
                                                                style="font-size: 0.75rem;">
                                                                {{ Str::limit($sim->summary ?? '', 60) }}</div>
                                                        </div>
                                                        <div class="mt-auto">
                                                            <div class="small text-muted" style="font-size: 0.7rem;">
                                                                <i class="far fa-calendar"></i>
                                                                {{ $sim->created_at?->format('d/m/Y') }}
                                                            </div>
                                                        </div>
                                                    </x-layout.card>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </x-layout.card>

                            {{-- Blogs and Activity section --}}
                            <div class="row g-4">
                                {{-- Blogs column --}}
                                <div class="col-12 col-lg-8">
                                    {{-- Blogs list and status filters --}}
                                    <x-layout.card class="shadow-sm" body-id="blogs-container">
                                        <x-slot name="header">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span>Blogs</span>
                                                    @if ($isViewingOwn)
                                                        <span class="badge bg-secondary"
                                                            id="blogs-count">{{ ($blogsList ?? collect())->count() }}</span>
                                                    @endif
                                                </div>

                                                @if ($isViewingOwn && $isAdmin)
                                                    @auth
                                                        @can('create', App\Models\Blog::class)
                                                            <a href="{{ route('admin.blogs.create') }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fas fa-pen me-1"></i> Écrire un blog
                                                            </a>
                                                        @endcan
                                                    @endauth
                                                @endif
                                            </div>

                                            @if ($isViewingOwn)
                                                <div class="mt-3 flex-wrap" id="blog-filters">
                                                    <button type="button" class="blog-filter-btn is-active" data-tag="all" style="--filter-color: #0d6efd;">Tous</button>
                                                    <button type="button" class="blog-filter-btn" data-tag="published" style="--filter-color: #198754;">Publie</button>
                                                    <button type="button" class="blog-filter-btn" data-tag="draft" style="--filter-color: #6c757d;">Brouillon</button>
                                                    <button type="button" class="blog-filter-btn" data-tag="pending" style="--filter-color: #ffc107;">En attente</button>
                                                    <button type="button" class="blog-filter-btn" data-tag="rejected" style="--filter-color: #dc3545;">Rejete</button>
                                                    <button type="button" class="blog-filter-btn" data-tag="bookmarked" style="--filter-color: #0dcaf0;">
                                                        <i class="fas fa-bookmark"></i> Signets
                                                    </button>
                                                </div>
                                            @endif
                                        </x-slot>
                                        @if (($blogsList ?? collect())->isEmpty())
                                            <div class="text-muted">Aucun blog disponible.</div>
                                        @else
                                            <div class="blogs-list">
                                                @foreach ($blogsList as $index => $blog)
                                                    @php
                                                        $isBookmarked =
                                                            $blog->bookmarked_by_auth ??
                                                            ($blog->is_bookmarked ?? false)
                                                                ? true
                                                                : false;
                                                        $tags = $blog->tags ?? ($blog->status ?? 'draft');
                                                    @endphp
                                                    <div class="user-blog-wrapper blog-item" data-index="{{ $index }}"
                                                        data-tags="{{ $tags }}"
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
                                        @endif
                                    </x-layout.card>
                                </div>

                                {{-- Activity column --}}
                                <div class="col-12 col-lg-4">
                                    <x-layout.card class="shadow-sm" body-class="small text-muted" body-id="activity-container">
                                        <x-slot name="header">Activité dernière semaine</x-slot>
                                        @if (($recentActivity ?? collect())->isEmpty())
                                            <div class="text-muted">Aucune activité récente.</div>
                                        @else
                                            <ul class="list-unstyled mb-0 activity-list">
                                                @foreach ($recentActivity as $index => $act)
                                                    <li class="mb-2 activity-item">
                                                        <a href="{{ $act['url'] ?? '#' }}"
                                                            class="text-decoration-none">{{ $act['label'] }}</a>
                                                        <div class="small text-muted">
                                                            {{ optional($act['created_at'])->diffForHumans() ?? '' }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </x-layout.card>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @include('admin.users.partials.scripts')
        @include('admin.users.partials.styles')
