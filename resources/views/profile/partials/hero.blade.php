<!-- Hero Section -->
<div class="page-hero rounded-4 p-4 p-md-5 mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h1 class="display-6 fw-semibold mb-1">{{ __('My Profile') }}</h1>
        <p class="text-white-50 mb-0">{{ __('Manage your personal information and security settings') }}</p>
    </div>
    <div class="avatar-lg d-none d-md-flex shadow-sm">
        <span class="fw-bold">{{ $user->initials }}</span>
    </div>
</div>
