<!-- Delete Account Card -->
<div class="card modern-card border-danger mb-4">
    <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="fa-solid fa-user-xmark me-2"></i>Supprimer le Compte
        </h5>
        <span class="badge bg-white text-danger">Irréversible</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <p class="text-muted mb-0">
                    {{ __('Once you delete your account, all of your data will be permanently deleted. Please be certain.') }}
                </p>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <button type="button" id="deleteAccountBtn" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden form for delete account -->
        <form id="deleteAccountForm" method="POST" action="{{ route('profile.destroy') }}" style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="password" value="">
        </form>

        @if ($errors->userDeletion->any())
            <div class="alert alert-danger mt-3 mb-0">
                <strong>{{ __('Error:') }}</strong>
                <ul class="mb-0">
                    @foreach ($errors->userDeletion->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
