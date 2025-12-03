<!-- Hero Section -->
<div class="page-hero rounded-4 p-4 p-md-5 mb-4 d-flex align-items-center justify-content-between">
    <div>
        @if($isViewingOwnProfile ?? false)
            <h1 class="display-6 fw-semibold mb-1">{{ __('Mon Profil') }}</h1>
            <p class="text-white-50 mb-0">{{ __('Gérez vos informations personnelles et paramètres de sécurité') }}</p>
        @else
            <h1 class="display-6 fw-semibold mb-1">{{ $user->full_name }}</h1>
            @if($user->bio)
                <p class="text-white-50 mb-0">{{ $user->bio }}</p>
            @endif
        @endif
    </div>
    <div class="avatar-lg d-none d-md-flex shadow-sm">
        <span class="fw-bold">{{ $user->initials }}</span>
    </div>
</div>
