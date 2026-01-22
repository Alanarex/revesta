@extends('admin.addresses.layouts')

@section('content')
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (!$loop->last)
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                                {{ $breadcrumb['label'] }}
                            </a>
                        </li>
                    @else
                        <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <!-- Form Card -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">
                            <i class="fa fa-map-marker-alt"></i>
                            {{ isset($address) ? 'Modifier l\'adresse' : 'Créer une nouvelle adresse' }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Validation Errors Summary -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Erreurs de validation
                                </h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @php
                            $isEdit = isset($address);
                            $action = $isEdit
                                ? route('admin.addresses.update', $address)
                                : route('admin.addresses.store');
                        @endphp

                        <form id="addressForm" action="{{ $action }}" method="POST">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <!-- Libellé -->
                            <div class="mb-3">
                                <label for="label" class="form-label">
                                    Libellé <span class="badge bg-info">Auto-généré</span>
                                </label>
                                <input type="text" class="form-control @error('label') is-invalid @enderror"
                                    id="label" name="label" placeholder="Auto-généré à partir des champs adresse"
                                    value="{{ old('label', '') }}" maxlength="255" readonly>
                                <small class="text-muted d-block mt-1">
                                    <i class="fa fa-lock"></i> Généré automatiquement à partir des champs adresse (Rue,
                                    Numéro, Postal, Ville)
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
                                            id="complement" name="complement" placeholder="Ex: Appartement 5, Bloc A"
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
                                        <input type="text"
                                            class="form-control @error('postal_code') is-invalid @enderror"
                                            id="postal_code" name="postal_code" placeholder="Ex: 75001"
                                            value="{{ old('postal_code', $address->postal_code ?? '') }}" required
                                            maxlength="10">
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
                                        <input type="text"
                                            class="form-control @error('departement') is-invalid @enderror"
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
                                        <input type="number" step="0.0000001"
                                            class="form-control @error('lat') is-invalid @enderror" id="lat"
                                            name="lat" placeholder="Ex: 48.8566"
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
                                        <input type="number" step="0.0000001"
                                            class="form-control @error('lng') is-invalid @enderror" id="lng"
                                            name="lng" placeholder="Ex: 2.3522"
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
                                    <i class="fa fa-save"></i>
                                    {{ isset($address) ? 'Mettre à jour' : 'Créer' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/admin/addresses/form-auto-label.js'])
@endsection
