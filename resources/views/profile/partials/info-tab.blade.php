{{-- Info Tab: Profile forms with clean sections --}}
@can('update', $user)
    {{-- Profile Information Form --}}
    @include('profile.partials.update-profile-form')

    {{-- Password Update Form --}}
    @include('profile.partials.update-password-form')

    {{-- Delete Account Form --}}
    @include('profile.partials.delete-account-form')
@else
    <div class="alert alert-warning">
        <i class="fa fa-lock me-2"></i>Vous n'avez pas la permission de modifier ce profil.
    </div>
@endcan
