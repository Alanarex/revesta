@extends('admin.addresses.layouts')

@section('content')
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Breadcrumbs -->
        <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />

        <!-- Form and Related Items -->
        <div class="row">
            <!-- Address Edit Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">
                            <i class="fa fa-map-marker-alt"></i>
                            Modifier l'adresse
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Validation Errors Summary -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Erreurs de validation</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close close" data-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="addressForm" action="{{ route('admin.addresses.update', $address) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Libellé -->
                            <div class="mb-3">
                                <label for="label" class="form-label">
                                    Libellé <span class="badge bg-info">Auto-généré</span>
                                </label>
                                <input type="text" class="form-control @error('label') is-invalid @enderror"
                                    id="label" name="label" placeholder="Auto-généré à partir des champs adresse"
                                    value="{{ old('label', $address->label ?? '') }}" maxlength="255" readonly>
                                <small class="text-muted d-block mt-1">
                                    <i class="fa fa-lock"></i> Généré automatiquement à partir des champs adresse (Rue, Numéro, Postal, Ville)
                                </small>
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Street -->
                            <div class="mb-3">
                                <label for="street" class="form-label">Rue *</label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror"
                                    id="street" name="street" placeholder="Nom de la rue"
                                    value="{{ old('street', $address->street ?? '') }}" required maxlength="255">
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Number and Complement Row -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="number" class="form-label">Numéro</label>
                                        <input type="text" class="form-control @error('number') is-invalid @enderror"
                                            id="number" name="number" placeholder="Ex: 123, 45bis"
                                            value="{{ old('number', $address->number ?? '') }}" maxlength="50">
                                        @error('number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <label for="complement" class="form-label">Complément</label>
                                        <input type="text" class="form-control @error('complement') is-invalid @enderror"
                                            id="complement" name="complement"
                                            placeholder="Ex: Appartement 5, Bloc A"
                                            value="{{ old('complement', $address->complement ?? '') }}" maxlength="255">
                                        @error('complement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Postal Code, City, Department Row -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="postal_code" class="form-label">Code Postal *</label>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                            id="postal_code" name="postal_code" placeholder="Ex: 75001"
                                            value="{{ old('postal_code', $address->postal_code ?? '') }}" required maxlength="10">
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">Ville *</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city" placeholder="Ex: Paris"
                                            value="{{ old('city', $address->city ?? '') }}" required maxlength="255">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="departement" class="form-label">Département</label>
                                        <input type="text" class="form-control @error('departement') is-invalid @enderror"
                                            id="departement" name="departement" placeholder="Ex: 75 - Paris"
                                            value="{{ old('departement', $address->departement ?? '') }}" maxlength="50">
                                        @error('departement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- INSEE Code -->
                            <div class="mb-3">
                                <label for="insee_code" class="form-label">Code INSEE</label>
                                <input type="text" class="form-control @error('insee_code') is-invalid @enderror"
                                    id="insee_code" name="insee_code" placeholder="Code INSEE optionnel"
                                    value="{{ old('insee_code', $address->insee_code ?? '') }}" maxlength="10">
                                <small class="text-muted">Code INSEE de la commune (optionnel)</small>
                                @error('insee_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Coordinates Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lat" class="form-label">Latitude</label>
                                        <input type="number" step="0.0000001" class="form-control @error('lat') is-invalid @enderror"
                                            id="lat" name="lat" placeholder="Ex: 48.8566"
                                            value="{{ old('lat', $address->lat ?? '') }}">
                                        <small class="text-muted">Entre -90 et 90</small>
                                        @error('lat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lng" class="form-label">Longitude</label>
                                        <input type="number" step="0.0000001" class="form-control @error('lng') is-invalid @enderror"
                                            id="lng" name="lng" placeholder="Ex: 2.3522"
                                            value="{{ old('lng', $address->lng ?? '') }}">
                                        <small class="text-muted">Entre -180 et 180</small>
                                        @error('lng')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Related Items Sidebar -->
            <div class="col-lg-4">
                <!-- Address Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fa fa-info-circle"></i>
                            {{ "Détails de l'adresse" }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">ID</small>
                            <strong>{{ $address->id }}</strong>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">Code INSEE</small>
                            <strong>{{ $address->insee_code ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">Latitude</small>
                            <strong>{{ $address->lat ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">Longitude</small>
                            <strong>{{ $address->lng ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted d-block">Créée le</small>
                            <strong>{{ $address->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Users Card -->
                @if($address->users->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fa fa-users"></i>
                                Utilisateurs ({{ $address->users->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($address->users as $user)
                                    <div class="list-group-item px-3 py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="d-block">{{ $user->first_name }} {{ $user->last_name }}</strong>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            @if(Route::has('admin.users.show'))
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm mb-4 bg-light">
                        <div class="card-body text-center py-3">
                            <small class="text-muted">
                                <i class="fa fa-users"></i>
                                Aucun utilisateur associé
                            </small>
                        </div>
                    </div>
                @endif

                <!-- Housings Card -->
                @if($address->housings->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fa fa-home"></i>
                                Logements ({{ $address->housings->count() }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($address->housings as $housing)
                                    <div class="list-group-item px-3 py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="d-block">{{ $housing->label ?? 'Logement #' . $housing->id }}</strong>
                                                <small class="text-muted">{{ $housing->address_type ?? 'Type non défini' }}</small>
                                            </div>
                                            @if(Route::has('admin.housings.show'))
                                                <a href="{{ route('admin.housings.show', $housing) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm mb-4 bg-light">
                        <div class="card-body text-center py-3">
                            <small class="text-muted">
                                <i class="fa fa-home"></i>
                                Aucun logement associé
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @vite(['resources/js/admin/addresses/form-auto-label.js'])
@endsection
