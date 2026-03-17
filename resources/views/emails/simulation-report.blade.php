@php
    $user = $simulation->user;
    $ad = $simulation->ad;
    $works = $simulation->works;
    $aids = $simulation->aids;

    $fullName = trim((string) ($user?->full_name ?? 'Utilisateur'));
    $firstName = trim((string) ($user?->first_name ?? '')) ?: $fullName;

    $price = (float) ($ad?->prix ?? 0);
    $surface = (float) ($ad?->surface ?? 0);
    $pricePerSquareMeter = $surface > 0 ? round($price / $surface) : null;
    $budgetTravaux = (float) ($user?->budget_travaux ?? 0);
    $totalProjectCost = $price + $budgetTravaux;

    $totalAids = (float) ($simulation->montant_total_aides ?? $aids->sum(fn($aid) => (float) ($aid->pivot?->amount ?? 0)));
    $remainingCost = max(0, $totalProjectCost - $totalAids);
    $remainingWorkCost = max(0, $budgetTravaux - $totalAids);
    $coverage = $budgetTravaux > 0 ? round(($totalAids / $budgetTravaux) * 100, 1) : (float) ($simulation->pourcentage_bien ?? 0);

    $propertyTypeCode = (string) ($ad?->housing_type_id ?? $user?->housing_type_id ?? '');
    $propertyTypeLabel = config('housing.housing_types.' . $propertyTypeCode)
        ?? ucfirst(str_replace('_', ' ', $propertyTypeCode ?: 'Indéfini'));

    $statusCode = (string) ($user?->user_status_id ?? '');
    $statusLabel = config('users.simulation_statuses.' . $statusCode)
        ?? ucfirst(str_replace('_', ' ', $statusCode ?: 'Indéfini'));

    $periodCode = (string) ($user?->construction_period_id ?? '');
    $periodLabel = config('housing.construction_periods.' . $periodCode)
        ?? ucfirst(str_replace('_', ' ', $periodCode ?: 'Indéfinie'));

    $aidPathCode = (string) ($simulation->aid_path_id ?? $user?->aid_path_id ?? '');
    $aidPathLabel = config('aid.parcours_aide.' . $aidPathCode)
        ?? ucfirst(str_replace('_', ' ', $aidPathCode ?: 'Non précisé'));

    $resolveAidType = static function ($aid): string {
        $pivotType = $aid->pivot?->details['type'] ?? null;
        $rawType = is_string($pivotType) && trim($pivotType) !== ''
            ? trim($pivotType)
            : (string) ($aid->type ?? 'subvention');

        $normalized = mb_strtolower(trim($rawType));
        $ascii = str_replace(' ', '', Illuminate\Support\Str::ascii($normalized));

        return (str_contains($ascii, 'pret') || str_contains($ascii, 'loan'))
            ? 'pret'
            : 'subvention';
    };

    $subventions = $aids->filter(fn($aid) => $resolveAidType($aid) !== 'pret');
    $prets = $aids->filter(fn($aid) => $resolveAidType($aid) === 'pret');

    $adImage = $ad?->images?->sortBy('order')->first()?->url;
    $fallbackImage = 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1200&q=80&auto=format&fit=crop';
    $adImageUrl = $adImage ?: $fallbackImage;

    $dpeActuel = strtoupper((string) ($user?->dpe_actuel_id ?? $ad?->dpe_class_id ?? 'E'));
    $dpeVise = strtoupper((string) ($user?->dpe_vise_id ?? 'C'));

    $rawTaxeFonciere = $user?->taxe_fonciere;
    $hasTaxeFonciere = is_numeric($rawTaxeFonciere) && (float) $rawTaxeFonciere > 0;
    $taxeFonciere = $hasTaxeFonciere ? (float) $rawTaxeFonciere : null;
    $possibleTaxExemption = $hasTaxeFonciere
        ? round(min((float) $taxeFonciere, (float) $taxeFonciere * 0.4), 0)
        : null;

    $costChartLabels = ($price > 0 || $budgetTravaux > 0)
        ? ['Prix d\'acquisition', 'Budget travaux']
        : ['Aucune donnée'];
    $costChartValues = ($price > 0 || $budgetTravaux > 0)
        ? [round($price, 2), round($budgetTravaux, 2)]
        : [1];
    $costChartBackground = ($price > 0 || $budgetTravaux > 0)
        ? ['rgba(11, 28, 19, 0.8)', 'rgba(164, 197, 32, 0.8)']
        : ['rgba(200, 200, 200, 0.6)'];
    $costChartBorder = ($price > 0 || $budgetTravaux > 0)
        ? ['rgba(11, 28, 19, 1)', 'rgba(164, 197, 32, 1)']
        : ['rgba(160, 160, 160, 1)'];

    $impactBefore = round(max($totalProjectCost, 0), 2);
    $impactAfter = round(max($remainingCost, 0), 2);
    $impactSavings = round(max($totalAids, 0), 2);

    $costChartConfig = [
        'type' => 'doughnut',
        'data' => [
            'labels' => $costChartLabels,
            'datasets' => [[
                'data' => $costChartValues,
                'backgroundColor' => $costChartBackground,
                'borderColor' => $costChartBorder,
                'borderWidth' => 2,
            ]],
        ],
        'options' => [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ],
    ];

    $impactChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => ['Coût du projet'],
            'datasets' => [
                [
                    'label' => 'Sans les aides',
                    'data' => [$impactBefore],
                    'backgroundColor' => 'rgba(102, 102, 102, 0.7)',
                    'borderColor' => 'rgba(102, 102, 102, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Avec les aides',
                    'data' => [$impactAfter],
                    'backgroundColor' => 'rgba(11, 28, 19, 0.7)',
                    'borderColor' => 'rgba(11, 28, 19, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Économie réalisée',
                    'data' => [$impactSavings],
                    'backgroundColor' => 'rgba(164, 197, 32, 0.7)',
                    'borderColor' => 'rgba(164, 197, 32, 1)',
                    'borderWidth' => 2,
                ],
            ],
        ],
        'options' => [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ],
    ];

    $costChartImageUrl = 'https://quickchart.io/chart?' . http_build_query([
        'width' => 600,
        'height' => 380,
        'format' => 'png',
        'c' => json_encode($costChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);

    $impactChartImageUrl = 'https://quickchart.io/chart?' . http_build_query([
        'width' => 700,
        'height' => 420,
        'format' => 'png',
        'c' => json_encode($impactChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);

    $generatedAt = now()->format('d/m/Y');
    $logoSvgPath = base_path('resources/images/Revesta_LogoTextuel-Blanc.svg');
    $logoSrc = file_exists($logoSvgPath)
        ? 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($logoSvgPath))
        : rtrim($appUrl, '/') . '/images/Revesta_LogoTextuel-Blanc.png';
@endphp

<!DOCTYPE html>
<html lang="fr">
    <head>
        @php
            $emailCssPath = base_path('resources/css/emails.css');
            $emailCssContent = is_readable($emailCssPath) ? (string) file_get_contents($emailCssPath) : '';
            $emailCssChunks = [];
            $maxStyleChunkSize = 6000;

            if ($emailCssContent !== '') {
                $currentCssChunk = '';
                $cssLines = preg_split('/\R/', $emailCssContent) ?: [];

                foreach ($cssLines as $cssLine) {
                    $lineWithBreak = $cssLine . "\n";

                    if (strlen($currentCssChunk . $lineWithBreak) > $maxStyleChunkSize && $currentCssChunk !== '') {
                        $emailCssChunks[] = $currentCssChunk;
                        $currentCssChunk = $lineWithBreak;
                        continue;
                    }

                    $currentCssChunk .= $lineWithBreak;
                }

                if ($currentCssChunk !== '') {
                    $emailCssChunks[] = $currentCssChunk;
                }
            }
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ rtrim($appUrl, '/') }}/images/icon-128.png" type="image/png">
        <title>REVESTA - Compte-rendu de votre projet immobilier</title>
        @forelse ($emailCssChunks as $emailCssChunk)
            <style>
                {!! $emailCssChunk !!}
            </style>
        @empty
            <style>
                body { margin: 0; padding: 0; font-family: Arial, sans-serif; color: #222; }
            </style>
        @endforelse
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <header>
            <a href="https://www.revesta.fr" target="_blank" rel="noopener">
                <img src="{{ $logoSrc }}" alt="REVESTA Logo" class="logo">
            </a>
            <div class="social-links">
                <a href="https://www.facebook.com/revesta.fr" target="_blank" rel="noopener" title="Facebook">
                    <i class='bx bxl-facebook-circle'></i>
                </a>
                <a href="https://www.instagram.com/revesta.fr" target="_blank" rel="noopener" title="Instagram">
                    <i class='bx bxl-instagram-alt'></i>
                </a>
                <a href="https://www.linkedin.com/company/re-vesta/" target="_blank" rel="noopener" title="LinkedIn">
                    <i class='bx bxl-linkedin-square'></i>
                </a>
            </div>
        </header>

        <section class="welcome-section">
            <p>Bonjour {{ $firstName }} !&ensp;&#x1F44B;</p>
            <h2>Votre projet d'achat immobilier</h2>
            <p>Voici le compte-rendu détaillé des aides disponibles pour votre projet.</p>
        </section>

        <section class="property-section">
            <div class="property-card">
                <a href="{{ $ad?->url ?? '#' }}" target="_blank" rel="noopener" title="Voir l'annonce du bien">
                    <img src="{{ $adImageUrl }}" alt="Photo du bien immobilier">
                </a>
                <div class="property-info">
                    <h2>{{ number_format($price, 0, ',', ' ') }} €</h2>
                    <p>{{ $propertyTypeLabel }} {{ $ad?->pieces ? $ad->pieces . ' pièces' : '' }} &bull; {{ $pricePerSquareMeter ? number_format($pricePerSquareMeter, 0, ',', ' ') . ' €/m²' : 'N/A' }}</p>
                </div>
            </div>

            <div class="analysis-card">
                <h2>&#x1F3E0;&ensp;Le bien immobilier</h2>
                <div class="analysis-item">
                    <span class="analysis-label">&#x1F4CD;&ensp;Commune</span>
                    <span class="analysis-value">{{ $ad?->code_postal ?? 'N/A' }} - {{ $ad?->ville ?? 'N/A' }}</span>
                </div>
                <div class="analysis-item">
                    <span class="analysis-label">&#x1F3E2;&ensp;Type</span>
                    <span class="analysis-value">{{ $propertyTypeLabel }}</span>
                </div>
                <div class="analysis-item">
                    <span class="analysis-label">&#x1F6AA;&ensp;Nombre de pièces</span>
                    <span class="analysis-value">{{ $ad?->pieces ?? 'N/A' }} pièces</span>
                </div>
                <div class="analysis-item">
                    <span class="analysis-label">&#x1F4D0;&ensp;Surface</span>
                    <span class="analysis-value">{{ $ad?->surface ?? 'N/A' }} m²</span>
                </div>
                <div class="analysis-item">
                    <span class="analysis-label">&#x1F525;&ensp;Mode de chauffage</span>
                    <span class="analysis-value">{{ $ad?->type_travaux ?? 'Non renseigné' }}</span>
                </div>
                <div class="analysis-item">
                    <span class="analysis-label">&#x231B;&ensp;Année de construction</span>
                    <span class="analysis-value">{{ $periodLabel }}</span>
                </div>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F464;&ensp;Mon profil</h2>
            <div class="list-item">
                <span class="list-label">&#x1F3E1;&ensp;Mon futur statut</span>
                <span class="list-value">{{ $statusLabel }}</span>
            </div>
            <div class="list-item">
                <span class="list-label">&#x1F46A;&ensp;Composition de mon ménage</span>
                <span class="list-value">{{ $user?->nombre_personnes ?? 'N/A' }} personnes</span>
            </div>
            <div class="list-item">
                <span class="list-label">&#x1F4B8;&ensp;Mes revenus annuels</span>
                <span class="list-value">{{ number_format((float) ($user?->revenus ?? 0), 0, ',', ' ') }} € / an</span>
            </div>
            <div class="list-item">
                <span class="list-label">&#x1F3E0;&ensp;Résidence principale</span>
                <span class="list-value">{{ $user?->residence_principale ? 'Oui' : 'Non' }}</span>
            </div>
            <div class="list-item">
                <span class="list-label">&#x1F4C5;&ensp;Période de construction du logement</span>
                <span class="list-value">{{ $periodLabel }}</span>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F4B0;&ensp;Mon plan financier</h2>

            <div class="financial-summary">
                <div class="financial-details">
                    <h3>Coûts de votre projet</h3>
                    <ul>
                        <li>
                            <span class="financial-label">&#x1F3E0;&ensp;Prix d'acquisition de votre bien</span>
                            <span class="financial-value">{{ number_format($price, 0, ',', ' ') }} €</span>
                        </li>
                        <li>
                            <span class="financial-label">&#x1F528;&ensp;Votre budget travaux réno'</span>
                            <span class="financial-value">{{ number_format($budgetTravaux, 0, ',', ' ') }} €</span>
                        </li>
                        <li>
                            <span class="financial-label">&#x1F4CA;&ensp;Coût total de votre projet</span>
                            <span class="financial-value">{{ number_format($totalProjectCost, 0, ',', ' ') }} €</span>
                            <span class="financial-label">(acquisition + travaux)</span>
                        </li>
                    </ul>
                </div>
                <div class="financial-details">
                    <h3>Répartition de ces coûts</h3>
                    <img src="{{ $costChartImageUrl }}" alt="Répartition des coûts" style="display:block;width:100%;max-width:560px;height:auto;margin-top:12px;">
                </div>
            </div>

            <div class="financial-summary-alt">
                <div class="financial-details-alt">
                    <h3>Coût <u>net</u> de mon projet après aides</h3>
                    <ul>
                        <li>
                            <span class="financial-label">&#x1F389;&ensp;Subvensions déduites</span>
                            <span class="financial-value" id="savings">- {{ number_format($totalAids, 0, ',', ' ') }} €</span>
                        </li>
                        <li>
                            <span class="financial-label">&#x2728;&ensp;Coût total après déduction des aides</span>
                            <span class="financial-value" id="final-cost"><u>{{ number_format($remainingCost, 0, ',', ' ') }}</u> €</span>
                        </li>
                    </ul>
                </div>

                <div class="financial-details-alt">
                    <h3>Impact des aides sur votre projet</h3>
                    <img src="{{ $impactChartImageUrl }}" alt="Impact des aides" style="display:block;width:100%;max-width:620px;height:auto;margin-top:12px;">
                </div>

                <div class="financial-details-alt">
                    <h3>Vos aides financières</h3>
                    <ul>
                        <li>
                            <span class="financial-label">&#x1F4B3;&ensp;Reste à charge travaux</span>
                            <span class="financial-value">{{ number_format($remainingWorkCost, 0, ',', ' ') }} €</span>
                        </li>
                        <li>
                            <span class="financial-label">&#x1F4C8;&ensp;Couverture des travaux</span>
                            <span class="financial-value">{{ $coverage }}%</span>
                        </li>
                        <li>
                            <span class="financial-label">&#x2705;&ensp;Aides éligibles</span>
                            <span class="financial-value">{{ $aids->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F3E6;&ensp;Aides disponibles pour votre projet</h2>

            <div class="aide-list">
                <div class="aide-subvention">
                    @forelse ($subventions as $aid)
                        <article class="aide-info">
                            <div class="aide-name">
                                <h3>{{ $aid->name }}</h3>
                                <a href="{{ $aid->pivot?->details['url'] ?? '#' }}">En savoir plus <i class='bx bx-link-external'></i></a>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4C4;&ensp;Type</span>
                                <span class="list-value"><u>{{ ucfirst((string) ($aid->pivot?->details['type'] ?? $aid->type ?? 'subvention')) }}</u></span>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4B0;&ensp;Montant estimé</span>
                                <span class="list-value">{{ number_format((float) ($aid->pivot?->amount ?? 0), 0, ',', ' ') }} €</span>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4D6;&ensp;Description</span>
                                <span class="list-value">{{ $aid->pivot?->details['detail'] ?? $aid->pivot?->details['description'] ?? $aid->description ?? 'Description non disponible' }}</span>
                            </div>
                        </article>
                    @empty
                        <article class="aide-info">
                            <div class="aide-name">
                                <h3>Aucune subvention identifiée</h3>
                            </div>
                            <div class="list-item">
                                <span class="list-value">Vos données ne contiennent pas de subvention détaillée.</span>
                            </div>
                        </article>
                    @endforelse
                </div>

                <div class="aide-pret">
                    @forelse ($prets as $aid)
                        <article class="aide-info">
                            <div class="aide-name">
                                <h3>{{ $aid->name }}</h3>
                                <a href="{{ $aid->pivot?->details['url'] ?? '#' }}">En savoir plus <i class='bx bx-link-external'></i></a>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4C4;&ensp;Type</span>
                                <span class="list-value"><u>{{ ucfirst((string) ($aid->pivot?->details['type'] ?? $aid->type ?? 'prêt')) }}</u></span>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4B0;&ensp;Montant estimé</span>
                                <span class="list-value">{{ number_format((float) ($aid->pivot?->amount ?? 0), 0, ',', ' ') }} €</span>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4D6;&ensp;Description</span>
                                <span class="list-value">{{ $aid->pivot?->details['detail'] ?? $aid->pivot?->details['description'] ?? $aid->description ?? 'Description non disponible' }}</span>
                            </div>
                        </article>
                    @empty
                        <article class="aide-info">
                            <div class="aide-name">
                                <h3>Aucun prêt identifié</h3>
                            </div>
                            <div class="list-item">
                                <span class="list-value">Aucun prêt spécifique n'a été retourné.</span>
                            </div>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F343;&ensp;Économies d'énergie estimées</h2>

            <div class="dpe-comparison">
                <div class="dpe-container">
                    <h3>DPE Actuel</h3>
                    <div class="dpe-scale">
                        <div class="dpe-item dpe-a {{ $dpeActuel === 'A' ? 'active' : '' }}"><span class="dpe-letter">A</span><span class="dpe-range">≤ 50</span></div>
                        <div class="dpe-item dpe-b {{ $dpeActuel === 'B' ? 'active' : '' }}"><span class="dpe-letter">B</span><span class="dpe-range">51 à 90</span></div>
                        <div class="dpe-item dpe-c {{ $dpeActuel === 'C' ? 'active' : '' }}"><span class="dpe-letter">C</span><span class="dpe-range">91 à 150</span></div>
                        <div class="dpe-item dpe-d {{ $dpeActuel === 'D' ? 'active' : '' }}"><span class="dpe-letter">D</span><span class="dpe-range">151 à 230</span></div>
                        <div class="dpe-item dpe-e {{ $dpeActuel === 'E' ? 'active' : '' }}"><span class="dpe-letter">E</span><span class="dpe-range">231 à 330</span></div>
                        <div class="dpe-item dpe-f {{ $dpeActuel === 'F' ? 'active' : '' }}"><span class="dpe-letter">F</span><span class="dpe-range">331 à 450</span></div>
                        <div class="dpe-item dpe-g {{ $dpeActuel === 'G' ? 'active' : '' }}"><span class="dpe-letter">G</span><span class="dpe-range">&gt; 450</span></div>
                    </div>
                </div>

                <div class="dpe-arrow">&#x2192;</div>

                <div class="dpe-container">
                    <h3>DPE Visé</h3>
                    <div class="dpe-scale">
                        <div class="dpe-item dpe-a {{ $dpeVise === 'A' ? 'active' : '' }}"><span class="dpe-letter">A</span><span class="dpe-range">≤ 50</span></div>
                        <div class="dpe-item dpe-b {{ $dpeVise === 'B' ? 'active' : '' }}"><span class="dpe-letter">B</span><span class="dpe-range">51 à 90</span></div>
                        <div class="dpe-item dpe-c {{ $dpeVise === 'C' ? 'active' : '' }}"><span class="dpe-letter">C</span><span class="dpe-range">91 à 150</span></div>
                        <div class="dpe-item dpe-d {{ $dpeVise === 'D' ? 'active' : '' }}"><span class="dpe-letter">D</span><span class="dpe-range">151 à 230</span></div>
                        <div class="dpe-item dpe-e {{ $dpeVise === 'E' ? 'active' : '' }}"><span class="dpe-letter">E</span><span class="dpe-range">231 à 330</span></div>
                        <div class="dpe-item dpe-f {{ $dpeVise === 'F' ? 'active' : '' }}"><span class="dpe-letter">F</span><span class="dpe-range">331 à 450</span></div>
                        <div class="dpe-item dpe-g {{ $dpeVise === 'G' ? 'active' : '' }}"><span class="dpe-letter">G</span><span class="dpe-range">&gt; 450</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F4CA;&ensp;Informations complémentaires</h2>
            <div class="aide-list">
                <div class="aide-subvention">
                    @if ($hasTaxeFonciere)
                        <article class="aide-info">
                            <div class="aide-name">
                                <h3>Fiscalité</h3>
                                <a href="https://www.service-public.fr/particuliers/vosdroits/F59" target="_blank" rel="noopener">En savoir plus <i class='bx bx-link-external'></i></a>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4B8;&ensp;Taxe foncière anuelle</span>
                                <span class="list-value">{{ number_format((float) $taxeFonciere, 0, ',', ' ') }} €</span>
                            </div>
                            <div class="list-item">
                                <span class="list-label">&#x1F4B0;&ensp;Exonération de taxe foncière possible</span>
                                <span class="list-value">{{ number_format((float) $possibleTaxExemption, 0, ',', ' ') }} €</span>
                            </div>
                        </article>
                    @endif
                    <article class="aide-info">
                        <div class="aide-name">
                            <h3>Parcours d'aide</h3>
                            <a href="https://www.economie.gouv.fr/particuliers/faire-des-economies-denergie/maprimerenov-renovation-dampleur-tout-savoir-sur-cette-aide" target="_blank" rel="noopener">En savoir plus <i class='bx bx-link-external'></i></a>
                        </div>
                        <div class="list-item">
                            <span class="list-label">&#x1F4C4;&ensp;Type de parcours MaPrimeRénov'</span>
                            <span class="list-value">{{ $aidPathLabel }}</span>
                        </div>
                        <div class="list-item">
                            <span class="list-label">&#x2753;&ensp;Accompagnement nécessaire</span>
                            <span class="list-value">{{ str_contains(mb_strtolower($aidPathLabel), 'accompagn') ? 'Oui' : 'Non' }}</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F4DD;&ensp;Prochaines étapes recommandées</h2>
            <div class="list-item-alt">
                <ul>
                    <li>
                        <a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener">
                            <span class="list-label-alt">1. Faire <u>réaliser un audit énergétique complet</u> par un professionnel RGE.</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://france-renov.gouv.fr/preparer-projet/trouver-conseiller" target="_blank" rel="noopener">
                            <span class="list-label-alt">2. <u>Consulter un conseiller France Rénov'</u> afin de valider votre éligibilité aux aides.</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.service-public.gouv.fr/particuliers/vosdroits/R39412" target="_blank" rel="noopener">
                            <span class="list-label-alt">3. <u>Comparer plusieurs devis</u> d'artisans RGE (Reconnu Garant de l'Environnement).</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.anah.gouv.fr/document/maprimerenov-mode-emploi" target="_blank" rel="noopener">
                            <span class="list-label-alt">4. <u>Constituer votre dossier MaPrimeRénov'</u> avant le début des travaux.</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.economie.gouv.fr/particuliers/faire-des-economies-denergie/renovation-energetique-les-aides-auxquelles-vous-pouvez-pretendre#" target="_blank" rel="noopener">
                            <span class="list-label-alt">5. <u>Vérifier les conditions d'éligibilité aux aides</u> auprès des organismes concernés.</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener">
                            <span class="list-label-alt">6. <u>Planifier vos travaux</u> selon les recommandations de l'audit énergétique.</span>
                            <span class="list-link-alt"><i class='bx bx-link-external'></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x1F4E7;&ensp;Vos coordonnées</h2>
            <div class="analysis-item">
                <span class="analysis-label">&#x1F464;&ensp;NOM & Prénom</span>
                <span class="analysis-value">{{ $fullName }}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">&#x1F4EC;&ensp;Adresse mail</span>
                <span class="analysis-value">{{ $user?->email ?? 'N/A' }}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">&#x1F4DE;&ensp;Téléphone</span>
                <span class="analysis-value">{{ $user?->telephone ?? $user?->phone ?? 'N/A' }}</span>
            </div>
        </section>

        <section class="infos-section">
            <h2>&#x2139;&#xFE0F;&ensp;Informations importantes</h2>
            <div class="important-info">
                <p>
                    <u>Avertissement</u> : Ces estimations sont basées sur votre profil et les caractéristiques du bien renseignées.
                    Les montants indiqués sont des estimations et peuvent varier en fonction de nombreux facteurs (revenus exacts, nature précise des travaux, date de réalisation, etc.).
                </p>
                <p>
                    Afin d'obtenir des montants précis et démarrer vos démarches officielles, nous vous recommandons vivement de :
                </p>
                <ul>
                    <li>Consulter un conseiller <a href="https://france-renov.gouv.fr/" target="_blank" rel="noopener">France Rénov'</a> (service public gratuit),</li>
                    <li>Visiter le site officiel <a href="https://mesaidesreno.beta.gouv.fr/" target="_blank" rel="noopener">Mes Aides Réno</a>,</li>
                    <li>Contacter l'<a href="https://www.anah.gouv.fr/" target="_blank" rel="noopener">ANAH</a> (Agence Nationale de l'Habitat).</li>
                </ul>
            </div>
        </section>

        <footer>
            <h3>Merci d'avoir utilisé REVESTA !</h3>
            <p>
                Ce compte-rendu a été généré le <u>{{ $generatedAt }}</u>.
                <br>
                REVESTA&copy; v1.0.0 &bull; Compte-rendu créé et élevé en France ! &#x1F1EB;&#x1F1F7; &bull; Circa {{ now()->year }}
            </p>
        </footer>
        <p class="privacy-note">
            &#x1F512; Vos données personnelles sont sécurisées et ne sont jamais partagées sans votre accord explicite.
            <br> Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données.
        </p>

    </body>
</html>
