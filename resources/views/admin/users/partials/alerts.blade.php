<!-- Success Messages -->
@if (session('status') === 'user-updated')
    <div class="alert alert-success alert-with-icon alert-dismissible fade show" role="alert">
        <i class="fa-regular fa-circle-check me-2"></i>{{ __('User updated successfully.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif (session('status') === 'password-updated')
    <div class="alert alert-success alert-with-icon alert-dismissible fade show" role="alert">
        <i class="fa-regular fa-circle-check me-2"></i>{{ __('Password updated successfully.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
