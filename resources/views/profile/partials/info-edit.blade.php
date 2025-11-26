{{-- Edit form for profile information --}}

{{-- Profile Information Form --}}
<div class="mb-4">
    <h5 class="mb-3"><i class="fa fa-user me-2"></i>Modifier mes Informations</h5>
    
    @include('profile.partials.update-profile-form')
</div>

{{-- Password Update Form --}}
<div class="mb-4">
    <h5 class="mb-3"><i class="fa fa-lock me-2"></i>Changer mon Mot de Passe</h5>
    
    @include('profile.partials.update-password-form')
</div>

{{-- Delete Account Form --}}
<div class="mb-4">
    <h5 class="mb-3 text-danger"><i class="fa fa-trash me-2"></i>Zone Dangereuse</h5>
    
    @include('profile.partials.delete-account-form')
</div>
