@extends('layouts.app')


@section('content')
    <div class="main-content">
        <div class="container-fluid">
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

            <!-- Success Messages -->
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-with-icon alert-dismissible fade show" role="alert">
                    <i class="fa-regular fa-circle-check me-2"></i>{{ __('Profile updated successfully.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif (session('status') === 'password-updated')
                <div class="alert alert-success alert-with-icon alert-dismissible fade show" role="alert">
                    <i class="fa-regular fa-circle-check me-2"></i>{{ __('Password updated successfully.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Content Row -->
            <div class="row g-4 mt-1">
                <!-- Main Content -->
                <div class="col-12 col-xl-8">
                    <!-- Profile Information Card -->
                    <div class="card modern-card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="fa-regular fa-user me-2"></i>{{ __('Profile Information') }}
                            </h5>
                            <span class="badge bg-light text-secondary">{{ __('Basic Info') }}</span>
                        </div>
                        <div class="card-body">
                            <form id="profileForm" method="POST" action="{{ route('profile.update') }}" novalidate>
                                @csrf
                                @method('PATCH')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-regular fa-id-badge"></i></span>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name"
                                                value="{{ old('first_name', $user->first_name) }}" required
                                                autocomplete="given-name">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-regular fa-id-badge"></i></span>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                                name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                                autocomplete="family-name">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email', $user->email) }}"
                                                required autocomplete="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                                placeholder="+33 6 12 34 56 78">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary btn-action"
                                            data-loading-text="{{ __('Saving...') }}">
                                            <i class="fa-regular fa-floppy-disk me-2"></i>{{ __('Update Profile') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="card modern-card">
                        <div class="card-header d-flex align-items-center justify-content-between bg-warning-subtle">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-key me-2"></i>{{ __('Change Password') }}
                            </h5>
                            <span class="badge bg-warning text-dark">{{ __('Security') }}</span>
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
                                            <div class="strength-bar"></div>
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
                </div>

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
            </div>
        </div>
    </div>
@endsection

@include('profile.partials.scripts')
@include('profile.partials.styles')
