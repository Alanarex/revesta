@props(['address' => null, 'action' => '', 'method' => 'POST'])

<div class="row">
    <div class="col-lg-10 mx-auto">

        <!-- Breadcrumbs -->
        <div class="mb-4">
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
        </div>

        <x-forms.form :title="$title" :action="$action" :method="$method" formId="addressForm">

            <!-- Libellé -->
            <x-inputs.text-input name="label" label="Libellé" :value="$address?->label"
                placeholder="Auto-généré à partir de l'adresse" icon="fa-tag" readonly
                muted="Généré automatiquement à partir de (Rue, Numéro, Postal, Ville)" />

            <!-- Street -->
            <x-inputs.text-input name="street" label="Rue" :value="$address?->street" placeholder="Nom de la rue"
                icon="fa-road" required maxlength="255" />

            <!-- Number and Complement Row -->
            <div class="row">
                <div class="col-md-3">
                    <x-inputs.text-input name="number" label="Numéro" :value="$address?->number" placeholder="Ex: 123, 45bis"
                        icon="fa-hashtag" maxlength="50" />
                </div>
                <div class="col-md-9">
                    <x-inputs.text-input name="complement" label="Complément" :value="$address?->complement"
                        placeholder="Ex: Appartement 5, Bloc A" icon="fa-building" maxlength="255" />
                </div>
            </div>

            <!-- Postal Code, City, Department Row -->
            <div class="row">
                <div class="col-md-4">
                    <x-inputs.text-input name="postal_code" label="Code Postal" :value="$address?->postal_code"
                        placeholder="Ex: 75001" icon="fa-envelope" required maxlength="10" />
                </div>
                <div class="col-md-4">
                    <x-inputs.text-input name="city" label="Ville" :value="$address?->city" placeholder="Ex: Paris"
                        icon="fa-map-pin" required maxlength="255" />
                </div>
                <div class="col-md-4">
                    <x-inputs.text-input name="departement" label="Département" :value="$address?->departement"
                        placeholder="Ex: 75 - Paris" icon="fa-map-location-dot" maxlength="50" />
                </div>
            </div>

            <!-- INSEE Code -->
            <x-inputs.text-input name="insee_code" label="Code INSEE" :value="$address?->insee_code"
                placeholder="Code INSEE optionnel" icon="fa-barcode" maxlength="10"
                muted="Code INSEE de la commune (optionnel)" />

            <!-- Coordinates Row -->
            <div class="row">
                <div class="col-md-6">
                    <x-inputs.text-input name="lat" label="Latitude" :value="$address?->lat" placeholder="Ex: 48.8566"
                        icon="fa-location-dot" muted="Entre -90 et 90" />
                </div>
                <div class="col-md-6">
                    <x-inputs.text-input name="lng" label="Longitude" :value="$address?->lng" placeholder="Ex: 2.3522"
                        icon="fa-location-dot" muted="Entre -180 et 180" />
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2 justify-content-end pt-4">
                <x-buttons.button-text text="Annuler" href="{{ route('admin.addresses.index') }}" class="text-muted" />
                <x-buttons.button-primary text="{{ $address ? 'Mettre à jour' : 'Créer' }}" type="submit" />
            </div>

        </x-forms.form>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/admin/addresses/form.js'])
@endpush
