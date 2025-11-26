<!-- Change Password Card -->
<div class="card modern-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="fa-solid fa-key me-2"></i>Changer le Mot de Passe
        </h5>
        <span class="badge bg-warning text-dark">Sécurité</span>
    </div>
    <div class="card-body">
        <form id="passwordForm" method="POST" action="{{ route('profile.password.update') }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label for="current_password"
                        class="form-label">{{ __('Current Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            id="current_password" name="current_password" required
                            autocomplete="current-password">
                        <button class="btn btn-outline-secondary toggle-visibility" type="button"
                            data-target="#current_password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <label for="new_password" class="form-label">{{ __('New Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password"
                            class="form-control @error('new_password') is-invalid @enderror"
                            id="new_password" name="new_password" required minlength="8"
                            autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-visibility" type="button"
                            data-target="#new_password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="password-verdict d-block mt-1"></small>
                    </div>
                    <small class="text-muted">
                        {{ __('Use 8+ characters with a mix of letters, numbers & symbols.') }}
                    </small>
                </div>

                <div class="col-12">
                    <label for="new_password_confirmation"
                        class="form-label">{{ __('Confirm New Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password"
                            class="form-control @error('new_password_confirmation') is-invalid @enderror"
                            id="new_password_confirmation" name="new_password_confirmation" required
                            autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-visibility" type="button"
                            data-target="#new_password_confirmation">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-warning btn-action text-dark"
                        data-loading-text="{{ __('Updating...') }}">
                        <i class="fa-solid fa-key me-2"></i>{{ __('Change Password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
