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
                    @if($user->bio)
                        <div class="text-muted small">{{ $user->bio }}</div>
                    @endif
                </div>
            </div>
            <hr>
            <ul class="list-unstyled mb-0 small text-muted">
                <li class="mb-2">
                    <i class="fa fa-newspaper me-2"></i>{{ __('Blogs publiés') }}:
                    <span class="text-body fw-semibold">{{ $publishedBlogsCount ?? 0 }}</span>
                </li>
                <li class="mb-2">
                    <i class="fa fa-heart me-2"></i>{{ __('Likes reçus') }}:
                    <span class="text-body fw-semibold">{{ $totalLikes ?? 0 }}</span>
                </li>
                <li class="mb-2">
                    <i class="fa fa-comment me-2"></i>{{ __('Commentaires reçus') }}:
                    <span class="text-body fw-semibold">{{ $totalComments ?? 0 }}</span>
                </li>
            </ul>

            {{-- Contact section: separate with a divider --}}
            <hr class="mt-3">
            <ul class="list-unstyled mb-0 small text-muted">
                @if ($user->email)
                    <li class="mb-2">
                        <i class="fa fa-envelope me-2"></i>{{ __('Email') }}:
                        <span class="text-body">{{ $user->email }}</span>
                    </li>
                @endif

                @if ($user->phone)
                    <li class="mb-2">
                        <i class="fa-solid fa-phone me-2"></i>{{ __('Phone') }}:
                        <span class="text-body">{{ $user->phone }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
