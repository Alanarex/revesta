{{-- Read-only view of profile information --}}
<div class="row g-3">
    {{-- Basic Information --}}
    <div class="col-12">
        <h5 class="mb-3"><i class="fa fa-user me-2"></i>Informations Personnelles</h5>
    </div>

    <div class="col-md-6">
        <div class="info-item">
            <label class="text-muted small mb-1">Prénom</label>
            <div class="fw-semibold">{{ $user->first_name ?? 'Non renseigné' }}</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-item">
            <label class="text-muted small mb-1">Nom</label>
            <div class="fw-semibold">{{ $user->last_name ?? 'Non renseigné' }}</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-item">
            <label class="text-muted small mb-1">Email</label>
            <div class="fw-semibold">{{ $user->email }}</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-item">
            <label class="text-muted small mb-1">Téléphone</label>
            <div class="fw-semibold">{{ $user->phone ?? 'Non renseigné' }}</div>
        </div>
    </div>

    <div class="col-12">
        <div class="info-item">
            <label class="text-muted small mb-1">Bio</label>
            <div class="fw-semibold">{{ $user->bio ?? 'Aucune biographie' }}</div>
        </div>
    </div>

    {{-- Security Section --}}
    <div class="col-12 mt-4">
        <h5 class="mb-3"><i class="fa fa-lock me-2"></i>Sécurité</h5>
    </div>

    <div class="col-12">
        <div class="info-item">
            <label class="text-muted small mb-1">Mot de passe</label>
            <div class="fw-semibold">••••••••</div>
        </div>
    </div>

    <div class="col-12">
        <div class="info-item">
            <label class="text-muted small mb-1">Compte créé le</label>
            <div class="fw-semibold">{{ $user->created_at->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 0.75rem;
    background: var(--bs-light);
    border-radius: 0.375rem;
}
</style>
