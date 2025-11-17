<!-- Sidebar -->
<div class="col-12 col-xl-4">
    <div class="card modern-card sticky-top" style="top: 90px;">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar-md me-3 shadow-sm">
                    <span class="fw-bold">{{ $user->initials }}</span>
                </div>
                <div>
                    <div class="fw-semibold">{{ $user->full_name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>
            </div>
            <hr>
            <ul class="list-unstyled mb-0 small text-muted">
                <li class="mb-2">
                    <i class="fa-regular fa-envelope me-2"></i>{{ __('Email') }}:
                    <span class="text-body">{{ $user->email }}</span>
                </li>
                @if ($user->phone)
                    <li class="mb-2">
                        <i class="fa-solid fa-phone me-2"></i>{{ __('Phone') }}:
                        <span class="text-body">{{ $user->phone }}</span>
                    </li>
                @endif
                <li class="mb-2">
                    <i class="fa-solid fa-shield-halved me-2"></i>{{ __('2FA') }}:
                    <span class="badge bg-light text-secondary">{{ __('Disabled') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
