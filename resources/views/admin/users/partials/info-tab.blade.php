{{-- Info Tab: User forms with clean sections --}}
@can('update', $user)
    {{-- User Information Form --}}
    @include('admin.users.partials.update-user-form')

    {{-- Password Update Form --}}
    @include('admin.users.partials.update-password-form')

    {{-- Delete Account Form --}}
    @include('admin.users.partials.delete-account-form')
@else
    <div class="alert alert-warning">
        <i class="fa fa-lock me-2"></i>Vous n'avez pas la permission de modifier cet utilisateur.
    </div>
@endcan
