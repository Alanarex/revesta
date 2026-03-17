@php
    $user = $simulation->user;
    $ad = $simulation->ad;
    $works = $simulation->works;
    $aids = $simulation->aids;
    $adImage = $ad?->images?->first()?->url;
    $annoncePayload = (array) data_get($apiPayload ?? [], 'annonce', []);
    $userPayload = (array) data_get($apiPayload ?? [], 'utilisateur', []);
    $simulationPayload = (array) data_get($apiPayload ?? [], 'simulation', []);

    $formatMoney = static function ($value): string {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return number_format((float) $value, 0, ',', ' ') . ' €';
    };

    $formatBool = static function ($value): string {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 'Oui' : 'Non';
    };

    $formatDate = static function ($value): string {
        if (empty($value)) {
            return 'N/A';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('d/m/Y');
        } catch (Throwable) {
            return (string) $value;
        }
    };

    $apiPrice = (float) ($annoncePayload['prix'] ?? $ad->prix ?? 0);
    $apiWorkBudget = (float) ($userPayload['budget_travaux'] ?? $user->budget_travaux ?? 0);
    $projectCost = $apiPrice + $apiWorkBudget;
    $totalAids = (float) ($simulationPayload['montant_total_aides'] ?? $simulation->montant_total_aides ?? 0);
    $coverage = (float) ($simulationPayload['pourcentage_bien'] ?? $simulation->pourcentage_bien ?? 0);
    $netCost = $projectCost - $totalAids;
    $coverageRemainder = max(0, 100 - $coverage);

    $costChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => ['Prix bien', 'Budget travaux', 'Aides', 'Coût net'],
            'datasets' => [[
                'data' => [$apiPrice, $apiWorkBudget, $totalAids, max(0, $netCost)],
                'backgroundColor' => ['#0b1c13', '#2f6f4f', '#a4c520', '#1f2937'],
            ]],
        ],
        'options' => [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ],
    ];

    $coverageChartConfig = [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['Couverture aides', 'Reste projet'],
            'datasets' => [[
                'data' => [$coverage, $coverageRemainder],
                'backgroundColor' => ['#a4c520', '#E3E3E1'],
            ]],
        ],
        'options' => [
            'plugins' => ['legend' => ['position' => 'bottom']],
        ],
    ];

    $costChartUrl = 'https://quickchart.io/chart?width=720&height=320&c=' . rawurlencode(json_encode($costChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $coverageChartUrl = 'https://quickchart.io/chart?width=520&height=320&c=' . rawurlencode(json_encode($coverageChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
@endphp

@extends('layouts.email', ['title' => 'Compte-rendu simulation - ' . $appName])

@section('content')
    <div style="background: #0b1c13; color: #ffffff; padding: 32px 20px; text-align: center; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
        <h1 style="font-size: 28px; margin: 0 0 10px 0; font-weight: 600; line-height: 1.3;">🏡 Votre projet d'achat immobilier</h1>
        <p style="font-size: 14px; margin: 0; opacity: 0.95;">Compte-rendu REVESTA</p>
    </div>

    <div style="padding: 30px; background-color: #ffffff; color: #222222; font-size: 14px; line-height: 1.6; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
        <div style="font-size: 16px; margin-bottom: 20px; color: #222222;">
            Bonjour <strong style="color: #a4c520;">{{ $user->full_name }}</strong>,
        </div>

        <p style="margin: 0 0 16px 0;">Voici les données envoyées à l'API, présentées section par section.</p>

        @if (!empty($adImage))
            <div style="margin: 0 0 20px 0; border: 1px solid #E3E3E1; border-radius: 8px; overflow: hidden; background-color: #f9f9f9;">
                <img src="{{ $adImage }}" alt="Image du bien" style="display: block; width: 100%; max-height: 260px; object-fit: cover;">
            </div>
        @endif

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">1) Section annonce</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Titre : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'titre', $ad->titre ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Site : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'site', $ad->site ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Type logement : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'type_logement', $ad->housing_type_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Prix : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($annoncePayload, 'prix', $ad->prix)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Surface : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'surface') ? number_format((float) data_get($annoncePayload, 'surface'), 0, ',', ' ') . ' m²' : ($ad->surface ? number_format((float) $ad->surface, 0, ',', ' ') . ' m²' : 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Pièces : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'pieces', $ad->pieces ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">DPE : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'dpe', $ad->dpe_class_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Ville / CP : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'ville', $ad->ville ?? 'N/A') }} {{ data_get($annoncePayload, 'code_postal', $ad->code_postal ?? '') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Étage : <span style="color: #222222; font-weight: 600;">{{ data_get($annoncePayload, 'etage', $ad->etage ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; color: #666666;">URL annonce :
                @if (!empty($ad->url))
                    <a href="{{ $ad->url }}" target="_blank" rel="noopener" style="color: #a4c520; text-decoration: underline;">Voir l'annonce</a>
                @elseif (!empty(data_get($annoncePayload, 'url')))
                    <a href="{{ data_get($annoncePayload, 'url') }}" target="_blank" rel="noopener" style="color: #a4c520; text-decoration: underline;">Voir l'annonce</a>
                @else
                    <span style="color: #222222; font-weight: 600;">N/A</span>
                @endif
            </div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">2) Section utilisateur</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Nom : <span style="color: #222222; font-weight: 600;">{{ trim((data_get($userPayload, 'prenom', $user->first_name ?? '') . ' ' . data_get($userPayload, 'nom', $user->last_name ?? ''))) ?: $user->full_name }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Email : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'email', $user->email ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Téléphone : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'telephone', $user->telephone ?? $user->phone ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Code postal : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'code_postal', $user->code_postal ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Revenus : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($userPayload, 'revenus', $user->revenus)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Nombre de personnes : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'nombre_personnes', $user->nombre_personnes ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Résidence principale : <span style="color: #222222; font-weight: 600;">{{ $formatBool(data_get($userPayload, 'residence_principale', $user->residence_principale)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Budget achat : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($userPayload, 'budget_achat', $user->budget_achat)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Budget travaux : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($userPayload, 'budget_travaux', $user->budget_travaux)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Taxe foncière : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($userPayload, 'taxe_fonciere', $user->taxe_fonciere)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Statut : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'statut', $user->user_status_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">DPE actuel / visé : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'dpe_actuel', $user->dpe_actuel_id ?? 'N/A') }} → {{ data_get($userPayload, 'dpe_vise', $user->dpe_vise_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Période construction : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'periode_construction', $user->construction_period_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Type logement : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'type_logement', $user->housing_type_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Parcours aide : <span style="color: #222222; font-weight: 600;">{{ data_get($userPayload, 'parcours_aide', $user->aid_path_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; color: #666666;">Notifications / consentements : <span style="color: #222222; font-weight: 600;">Aides: {{ $formatBool(data_get($userPayload, 'notifications_aides', $user->notifications_aides)) }} • Prix: {{ $formatBool(data_get($userPayload, 'notifications_prix', $user->notifications_prix)) }} • Analytics: {{ $formatBool(data_get($userPayload, 'accept_analytics', $user->accept_analytics)) }}</span></div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">3) Section simulation</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Date simulation : <span style="color: #222222; font-weight: 600;">{{ $formatDate(data_get($simulationPayload, 'date', $simulation->date)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Gain énergétique : <span style="color: #222222; font-weight: 600;">{{ data_get($simulationPayload, 'gain_energetique', $simulation->gain_energetique ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Parcours aide : <span style="color: #222222; font-weight: 600;">{{ data_get($simulationPayload, 'parcours_aide', $simulation->aid_path_id ?? 'N/A') }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Condition dépenses : <span style="color: #222222; font-weight: 600;">{{ $formatBool(data_get($simulationPayload, 'condition_depenses', $simulation->condition_depenses)) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Montant total aides : <span style="color: #a4c520; font-weight: 700;">{{ $formatMoney($totalAids) }}</span></div>
            <div style="padding: 10px 12px; color: #666666;">Pourcentage couverture : <span style="color: #222222; font-weight: 600;">{{ number_format($coverage, 2, ',', ' ') }}%</span></div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">Graphiques (images)</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px; padding: 12px; background-color: #f9f9f9;">
            <p style="margin: 0 0 10px 0; color: #0b1c13; font-weight: 700;">Répartition financière</p>
            <img src="{{ $costChartUrl }}" alt="Graphique des montants du projet" style="display: block; width: 100%; max-width: 100%; border: 1px solid #E3E3E1; border-radius: 6px; margin-bottom: 12px;">
            <p style="margin: 0 0 10px 0; color: #0b1c13; font-weight: 700;">Couverture des aides</p>
            <img src="{{ $coverageChartUrl }}" alt="Graphique de couverture des aides" style="display: block; width: 100%; max-width: 520px; border: 1px solid #E3E3E1; border-radius: 6px;">
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">Travaux envoyés à l'API</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            @forelse ((array) data_get($simulationPayload, 'travaux', $works->pluck('label')->all()) as $work)
                <li style="margin: 0 0 6px 0;">{{ is_array($work) ? (data_get($work, 'type', data_get($work, 'label', 'N/A'))) : $work }}</li>
            @empty
                <li style="margin: 0 0 6px 0;">Aucun travail précisé.</li>
            @endforelse
        </ul>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">Aides envoyées à l'API</h3>
        @php
            $apiAids = (array) data_get($simulationPayload, 'aides_details', []);
        @endphp

        @forelse ($apiAids as $aidDetail)
            <div style="border: 1px solid #E3E3E1; border-radius: 6px; margin-bottom: 10px; overflow: hidden;">
                <div style="padding: 10px 12px; background-color: #f9f9f9; border-bottom: 1px solid #E3E3E1; color: #0b1c13; font-weight: 700;">
                    {{ data_get($aidDetail, 'nom', 'Aide') }}
                </div>
                <div style="padding: 10px 12px; color: #666666; border-bottom: 1px solid #E3E3E1;">
                    Type : <span style="color: #222222; font-weight: 600;">{{ data_get($aidDetail, 'type', 'N/A') }}</span>
                </div>
                <div style="padding: 10px 12px; color: #666666; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9;">
                    Montant estimé : <span style="color: #222222; font-weight: 600;">{{ $formatMoney(data_get($aidDetail, 'montant')) }}</span>
                </div>
                <div style="padding: 10px 12px; color: #666666;">
                    Détail : <span style="color: #222222;">{{ data_get($aidDetail, 'description', 'Information non disponible') }}</span>
                </div>
                @if (!empty(data_get($aidDetail, 'url')))
                    <div style="padding: 0 12px 10px 12px; color: #666666;">
                        Lien : <a href="{{ data_get($aidDetail, 'url') }}" target="_blank" rel="noopener" style="color: #a4c520; text-decoration: underline;">Consulter</a>
                    </div>
                @endif
            </div>
        @empty
            @forelse ($aids as $aid)
                <div style="border: 1px solid #E3E3E1; border-radius: 6px; margin-bottom: 10px; overflow: hidden;">
                    <div style="padding: 10px 12px; background-color: #f9f9f9; border-bottom: 1px solid #E3E3E1; color: #0b1c13; font-weight: 700;">
                        {{ $aid->name }}
                    </div>
                    <div style="padding: 10px 12px; color: #666666; border-bottom: 1px solid #E3E3E1;">
                        Type : <span style="color: #222222; font-weight: 600;">{{ data_get($aid, 'pivot.details.type', 'N/A') }}</span>
                    </div>
                    <div style="padding: 10px 12px; color: #666666; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9;">
                        Montant estimé : <span style="color: #222222; font-weight: 600;">{{ $formatMoney($aid->pivot?->amount) }}</span>
                    </div>
                    <div style="padding: 10px 12px; color: #666666;">
                        Détail : <span style="color: #222222;">{{ data_get($aid, 'pivot.details.description', data_get($aid, 'pivot.details.detail', 'Information non disponible')) }}</span>
                    </div>
                </div>
            @empty
                <p style="margin: 0 0 16px 0; color: #666666;">Aucune aide détaillée fournie.</p>
            @endforelse
        @endforelse

        <div style="background-color: #f9f9f9; border-left: 4px solid #a4c520; padding: 14px 16px; margin: 20px 0 0 0; border-radius: 4px; color: #666666; border-top: 1px solid #E3E3E1; border-right: 1px solid #E3E3E1; border-bottom: 1px solid #E3E3E1;">
            <strong style="color: #0b1c13;">ℹ️ Information :</strong>
            Les graphiques sont intégrés sous forme d'images pour un affichage compatible email.
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid #E3E3E1; margin: 0;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
