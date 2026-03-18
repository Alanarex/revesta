@extends('layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <h1 class="display-6 mb-3">Gestion des simulations</h1>
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />

            @if ($isAdmin)
                <x-layout.card class="shadow-sm">
                    <x-slot name="header">Filtres administrateur</x-slot>
                    <x-forms.form action="{{ route('simulations.index') }}" method="GET" formId="simulationFiltersForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-5">
                                <x-inputs.text-input label="Prénom utilisateur" name="first_name" icon="fa-user"
                                    :value="$filters['first_name'] ?? ''" placeholder="Ex: Jean" />
                            </div>
                            <div class="col-12 col-md-5">
                                <x-inputs.text-input label="Nom utilisateur" name="last_name" icon="fa-user"
                                    :value="$filters['last_name'] ?? ''" placeholder="Ex: Dupont" />
                            </div>
                            <div class="col-12 col-md-2 d-flex gap-2 pb-4">
                                <x-buttons.button-primary type="submit" text="Filtrer" class="w-100" />
                                <a href="{{ route('simulations.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </x-forms.form>
                </x-layout.card>
            @endif

            <x-layout.card class="shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Liste des simulations</span>
                        <span class="badge bg-secondary">{{ $simulations->total() }}</span>
                    </div>
                </x-slot>

                @if ($simulations->isEmpty())
                    <div class="text-muted">Aucune simulation trouvée.</div>
                @else
                    <div class="row g-4">
                        @foreach ($simulations as $simulation)
                            @php
                                $coverImage = $simulation->ad?->images?->first()?->url;
                                $adTitle = $simulation->ad?->titre ?: 'Simulation #' . $simulation->id;
                            @endphp
                            <div class="col-12 col-md-6 col-xl-4">
                                <a href="{{ route('simulations.show', $simulation) }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm overflow-hidden h-100">
                                        <div class="position-relative" style="height: 220px;">
                                            @if ($coverImage)
                                                <img src="{{ $coverImage }}" alt="{{ $adTitle }}"
                                                    class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 bg-secondary"></div>
                                            @endif

                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                style="background-color: rgba(0, 0, 0, 0.5);"></div>

                                            <div
                                                class="position-absolute bottom-0 start-0 w-100 p-3 text-white d-flex flex-column gap-1">
                                                <h5 class="mb-0 text-white">{{ \Illuminate\Support\Str::limit($adTitle, 70) }}</h5>
                                                <small>{{ $simulation->created_at?->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            @if ($isAdmin)
                                                <div class="small text-muted mb-1">Utilisateur</div>
                                                <div class="fw-semibold mb-2">{{ $simulation->user?->full_name ?? '-' }}</div>
                                            @endif
                                            <div class="small text-muted">
                                                {{ $simulation->ad?->ville ?: $simulation->ad?->localisation ?: 'Localisation non renseignée' }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($simulations->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $simulations->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @endif
            </x-layout.card>
        </div>
    </div>
@endsection
