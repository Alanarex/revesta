@extends('layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <h1 class="display-6 mb-3">Détails simulation #{{ $simulation->id }}</h1>
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />

            @php
                $adImages = ($simulation->ad?->images ?? collect())->pluck('url')->filter()->values();
                $coverImage = $adImages->first();
                $adTitle = $simulation->ad?->titre ?: 'Simulation #' . $simulation->id;
                $carouselId = 'simulationAdCarousel' . $simulation->id;
            @endphp

            <x-layout.card class="shadow-sm overflow-hidden">
                <div class="position-relative" style="height: 280px;">
                    @if ($coverImage)
                        <div id="{{ $carouselId }}" class="carousel slide h-100" data-bs-ride="carousel">
                            @if ($adImages->count() > 1)
                                <div class="carousel-indicators mb-2">
                                    @foreach ($adImages as $index => $imageUrl)
                                        <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ $index }}"
                                            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-label="Slide {{ $index + 1 }}"></button>
                                    @endforeach
                                </div>
                            @endif

                            <div class="carousel-inner h-100">
                                @foreach ($adImages as $index => $imageUrl)
                                    <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $adTitle }} - image {{ $index + 1 }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                @endforeach
                            </div>

                            @if ($adImages->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="w-100 h-100 bg-secondary"></div>
                    @endif

                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.45);"></div>
                    <div class="position-absolute bottom-0 start-0 p-4 text-white">
                        <h3 class="mb-1 text-white">{{ $adTitle }}</h3>
                        <div>{{ $simulation->ad?->ville ?: $simulation->ad?->localisation ?: 'Localisation non renseignée' }}</div>
                    </div>
                </div>
            </x-layout.card>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Informations simulation</x-slot>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            <li><strong>ID :</strong> {{ $simulation->id }}</li>
                            <li><strong>Date :</strong> {{ $simulation->date?->format('d/m/Y') ?? '-' }}</li>
                            <li><strong>Créée le :</strong> {{ $simulation->created_at?->format('d/m/Y H:i') ?? '-' }}</li>
                            <li><strong>Gain énergétique :</strong> {{ $simulation->gain_energetique ?? '-' }}</li>
                            <li><strong>Parcours aide :</strong> {{ $simulation->aid_path_id ?? '-' }}</li>
                            <li><strong>Montant total aides :</strong> {{ $simulation->montant_total_aides ?? '-' }}</li>
                            <li><strong>Pourcentage bien :</strong> {{ $simulation->pourcentage_bien ?? '-' }}</li>
                            <li><strong>Condition dépenses :</strong> {{ is_null($simulation->condition_depenses) ? '-' : ($simulation->condition_depenses ? 'Oui' : 'Non') }}</li>
                        </ul>
                    </x-layout.card>
                </div>

                <div class="col-12 col-lg-6">
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Annonce associée</x-slot>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            <li><strong>Titre :</strong> {{ $simulation->ad?->titre ?? '-' }}</li>
                            <li><strong>Ville :</strong> {{ $simulation->ad?->ville ?? '-' }}</li>
                            <li><strong>Code postal :</strong> {{ $simulation->ad?->code_postal ?? '-' }}</li>
                            <li><strong>Prix :</strong> {{ $simulation->ad?->prix ?? '-' }}</li>
                            <li><strong>Surface :</strong> {{ $simulation->ad?->surface ?? '-' }}</li>
                            <li><strong>Pièces :</strong> {{ $simulation->ad?->pieces ?? '-' }}</li>
                            <li><strong>URL source :</strong>
                                @if ($simulation->ad?->url)
                                    <a href="{{ $simulation->ad->url }}" target="_blank" rel="noopener noreferrer">Voir l'annonce</a>
                                @else
                                    -
                                @endif
                            </li>
                        </ul>
                    </x-layout.card>
                </div>

                <div class="col-12 col-lg-6">
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Utilisateur</x-slot>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            <li><strong>Nom :</strong> {{ $simulation->user?->full_name ?? '-' }}</li>
                            <li><strong>Email :</strong> {{ $simulation->user?->email ?? '-' }}</li>
                        </ul>
                    </x-layout.card>
                </div>

                <div class="col-12 col-lg-6">
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Travaux</x-slot>
                        @if ($simulation->works->isEmpty())
                            <div class="text-muted">Aucun travaux enregistré.</div>
                        @else
                            <ul class="mb-0">
                                @foreach ($simulation->works as $work)
                                    <li>{{ $work->label }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </x-layout.card>
                </div>

                <div class="col-12">
                    <x-layout.card class="shadow-sm">
                        <x-slot name="header">Aides</x-slot>
                        @if ($simulation->aids->isEmpty())
                            <div class="text-muted">Aucune aide associée.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Montant</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Lien</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($simulation->aids as $aid)
                                            @php
                                                $details = is_array($aid->pivot?->details) ? $aid->pivot->details : [];
                                            @endphp
                                            <tr>
                                                <td>{{ $aid->pivot?->raw_name ?: $aid->name }}</td>
                                                <td>{{ $aid->pivot?->amount ?? '-' }}</td>
                                                <td>{{ $details['type'] ?? '-' }}</td>
                                                <td>{{ $details['description'] ?? '-' }}</td>
                                                <td>
                                                    @if (!empty($details['url']))
                                                        <a href="{{ $details['url'] }}" target="_blank" rel="noopener noreferrer">Voir</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-layout.card>
                </div>
            </div>
        </div>
    </div>
@endsection
