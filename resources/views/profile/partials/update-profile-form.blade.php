<!-- Profile Information Card -->
<div class="card modern-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="fa-regular fa-user me-2"></i>Informations du Profil
        </h5>
        <span class="badge bg-primary">Informations de base</span>
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
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                            id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                            required autocomplete="given-name">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-regular fa-id-badge"></i></span>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                            id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+33 6 12 34 56 78">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="civil_status" class="form-label">{{ __('Civil Status') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <select class="form-select @error('civil_status') is-invalid @enderror" id="civil_status"
                            name="civil_status">
                            <option value="">{{ __('Select title') }}</option>
                            <option value="monsieur" {{ old('civil_status', $user->civil_status) == 'monsieur' ? 'selected' : '' }}>{{ __('Monsieur') }}</option>
                            <option value="madame" {{ old('civil_status', $user->civil_status) == 'madame' ? 'selected' : '' }}>{{ __('Madame') }}</option>
                        </select>
                        @error('civil_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="family_status" class="form-label">{{ __('Family Status') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-heart"></i></span>
                        <select class="form-select @error('family_status') is-invalid @enderror" id="family_status"
                            name="family_status">
                            <option value="">{{ __('Select family status') }}</option>
                            <option value="married" {{ old('family_status', $user->family_status) == 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                            <option value="civil_partnership" {{ old('family_status', $user->family_status) == 'civil_partnership' ? 'selected' : '' }}>{{ __('Civil Partnership') }}</option>
                            <option value="divorced" {{ old('family_status', $user->family_status) == 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                            <option value="separated" {{ old('family_status', $user->family_status) == 'separated' ? 'selected' : '' }}>{{ __('Separated') }}</option>
                            <option value="single" {{ old('family_status', $user->family_status) == 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                            <option value="widowed" {{ old('family_status', $user->family_status) == 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                        </select>
                        @error('family_status')
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
