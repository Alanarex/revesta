@php
    $user = $simulation->user;
    $ad = $simulation->ad;

    $annonce = (array) data_get($apiPayload ?? [], 'annonce', []);
    $utilisateur = (array) data_get($apiPayload ?? [], 'utilisateur', []);
    $simulationPayload = (array) data_get($apiPayload ?? [], 'simulation', []);

    $images = (array) data_get($annonce, 'images', []);
    $adImage = $images[0] ?? ($ad?->images?->first()?->url);

    $formatMoney = static function ($value): string {
        if ($value === null || $value === '' || !is_numeric($value)) {
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
            return \Illuminate\Support\Carbon::parse($value)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return (string) $value;
        }
    };

    $mapWork = static function ($value): string {
        $label = mb_strtolower(trim((string) $value));

        return match ($label) {
            'isolation' => 'Isolation thermique',
            'chauffage' => 'Changement du système de chauffage',
            'menuiserie' => 'Menuiseries (fenêtres/portes)',
            'renovation', 'rénovation' => 'Rénovation énergétique globale',
            'energie', 'énergie' => 'Énergies renouvelables',
            default => ucfirst((string) $value),
        };
    };

    $mapAidType = static function ($value): string {
        $type = mb_strtolower(trim((string) $value));

        return match ($type) {
            'pret', 'prêt' => 'Prêt',
            'remboursement', 'subvention' => 'Subvention',
            default => ucfirst((string) $value ?: 'N/A'),
        };
    };

    $dpeCurrent = strtoupper((string) data_get($utilisateur, 'dpe_actuel', data_get($annonce, 'dpe', 'N/A')));
    $dpeTarget = strtoupper((string) data_get($utilisateur, 'dpe_vise', 'N/A'));

    $acquisitionPrice = (float) data_get($annonce, 'prix', $ad->prix ?? 0);
    $workBudget = (float) data_get($utilisateur, 'budget_travaux', $user->budget_travaux ?? 0);
    $totalProjectCost = $acquisitionPrice + $workBudget;
    $totalAids = (float) data_get($simulationPayload, 'montant_total_aides', $simulation->montant_total_aides ?? 0);
    $finalCost = max(0, $totalProjectCost - $totalAids);
    $remainingWorkCost = max(0, $workBudget - $totalAids);
    $workCoverage = $workBudget > 0 ? min(100, ($totalAids / $workBudget) * 100) : 0;

    $aidesDetails = (array) data_get($simulationPayload, 'aides_details', []);
    $travaux = (array) data_get($simulationPayload, 'travaux', []);

    $energyGain = (float) data_get($simulationPayload, 'gain_energetique', data_get($utilisateur, 'gain_energetique', 0));
    $estimatedEnergySavings = $energyGain > 0 ? (int) round($energyGain * 625) : null;
    $estimatedCO2Savings = $energyGain > 0 ? (int) round($energyGain * 118.5) : null;

    $costChartConfig = [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['Prix acquisition', 'Budget travaux'],
            'datasets' => [[
                'data' => [$acquisitionPrice, $workBudget],
                'backgroundColor' => ['#0b1c13', '#a4c520'],
            ]],
        ],
        'options' => [
            'plugins' => ['legend' => ['position' => 'bottom']],
        ],
    ];

    $impactChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => ['Avant aides', 'Aides', 'Après aides'],
            'datasets' => [[
                'data' => [$totalProjectCost, $totalAids, $finalCost],
                'backgroundColor' => ['#0b1c13', '#a4c520', '#666666'],
            ]],
        ],
        'options' => [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ],
    ];

    $costChartUrl = 'https://quickchart.io/chart?width=620&height=320&c=' . rawurlencode(json_encode($costChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $impactChartUrl = 'https://quickchart.io/chart?width=620&height=320&c=' . rawurlencode(json_encode($impactChartConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    $cardsBaseStyle = 'display:flex;flex-direction:column;background-color:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;';
    $h2Style = 'color:#0b1c13;font-size:22px;font-weight:800;padding-bottom:6px;margin:0 0 10px 0;border-bottom:2px solid #0b1c13;';
    $rowStyle = 'display:flex;justify-content:space-between;align-items:center;gap:12px;border-bottom:1px solid #f9f9f9;padding:10px 0;';
    $labelStyle = 'color:#666;font-size:14px;font-weight:500;';
    $valueStyle = 'color:#222;font-size:14px;font-weight:600;text-align:right;';
@endphp

@extends('layouts.email', ['title' => 'Compte-rendu simulation - ' . $appName])

@section('content')
    <div style="max-width:650px;margin:0 auto;background:#fffcf1;padding:14px 0;font-family:Montserrat,-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Oxygen','Ubuntu','Cantarell','Fira Sans','Droid Sans','Helvetica Neue',sans-serif;color:#222;">

        <section style="display:flex;flex-direction:column;align-items:center;gap:4px;margin:0 16px 16px 16px;">
            <p style="font-size:15px;font-weight:600;text-align:center;margin:0;">Hello {{ data_get($utilisateur, 'prenom', $user->first_name ?? 'Utilisateur') }} ! 👋</p>
            <h2 style="font-size:32px;font-weight:800;text-align:center;line-height:1.2;margin:0;color:#0b1c13;">Votre projet d'achat immobilier</h2>
            <p style="font-size:14px;font-weight:500;text-align:center;line-height:1.4;margin:0;">Voici le compte-rendu détaillé des aides disponibles pour votre projet.</p>
        </section>

        <section style="display:flex;flex-direction:row;margin:0 16px 16px 16px;background:#fff;border:1px solid #E3E3E1;border-radius:24px;overflow:hidden;">
            <div style="width:50%;position:relative;background:#f9f9f9;min-height:260px;">
                @if (!empty($adImage))
                    <img src="{{ $adImage }}" alt="Image du bien" style="display:block;object-fit:cover;width:100%;height:100%;min-height:260px;">
                @else
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;min-height:260px;color:#666;">Image indisponible</div>
                @endif
                <div style="position:absolute;left:0;right:0;bottom:0;background:#0b1c13;color:#fff;padding:10px 12px;text-align:center;">
                    <h3 style="font-size:18px;font-weight:700;margin:0 0 4px 0;">{{ data_get($annonce, 'titre', $ad->titre ?? 'Annonce immobilière') }}</h3>
                    @if (!empty(data_get($annonce, 'url', $ad->url)))
                        <a href="{{ data_get($annonce, 'url', $ad->url) }}" target="_blank" rel="noopener" style="font-size:13px;color:#a4c520;text-decoration:underline;">Voir l'annonce</a>
                    @endif
                </div>
            </div>

            <div style="width:50%;padding:20px;background:#fff;">
                <h2 style="{{ $h2Style }}font-size:21px;">🏠 Le bien immobilier</h2>
                <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">📍 Commune</span><span style="{{ $valueStyle }}">{{ data_get($annonce, 'code_postal', $ad->code_postal ?? 'N/A') }} - {{ data_get($annonce, 'ville', $ad->ville ?? 'N/A') }}</span></div>
                <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">🏢 Type</span><span style="{{ $valueStyle }}">{{ ucfirst((string) data_get($annonce, 'type_logement', $ad->housing_type_id ?? 'N/A')) }}</span></div>
                <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">🚪 Nombre de pièces</span><span style="{{ $valueStyle }}">{{ data_get($annonce, 'pieces', $ad->pieces ?? 'N/A') }} pièces</span></div>
                <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">📐 Surface</span><span style="{{ $valueStyle }}">{{ data_get($annonce, 'surface', $ad->surface ?? 'N/A') }} m²</span></div>
                <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">🔥 DPE actuel</span><span style="{{ $valueStyle }}">{{ $dpeCurrent }}</span></div>
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">⏳ Date extraction</span><span style="{{ $valueStyle }}">{{ $formatDate(data_get($annonce, 'date_extraction')) }}</span></div>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">👤 Mon profil</h2>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">🏡 Mon futur statut</span><span style="{{ $valueStyle }}">{{ ucfirst((string) data_get($utilisateur, 'statut', 'N/A')) }}</span></div>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">👪 Composition de mon ménage</span><span style="{{ $valueStyle }}">{{ data_get($utilisateur, 'nombre_personnes', 'N/A') }} personnes</span></div>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">💸 Mes revenus annuels</span><span style="{{ $valueStyle }}">{{ $formatMoney(data_get($utilisateur, 'revenus')) }} / an</span></div>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">🏠 Résidence principale</span><span style="{{ $valueStyle }}">{{ $formatBool(data_get($utilisateur, 'residence_principale')) }}</span></div>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">📅 Période de construction du logement</span><span style="{{ $valueStyle }}">{{ ucfirst((string) data_get($utilisateur, 'periode_construction', 'N/A')) }}</span></div>
        </section>

        <section style="display:flex;flex-direction:row;gap:16px;margin:0 16px 16px 16px;">
            <div style="width:50%;display:flex;flex-direction:column;gap:16px;">
                <div style="{{ $cardsBaseStyle }}margin:0;">
                    <h2 style="{{ $h2Style }}">💰 Plan financier</h2>
                    <div style="display:flex;flex-direction:column;border:1px solid #E3E3E1;border-radius:16px;overflow:hidden;">
                        <h3 style="font-size:16px;text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:10px;margin:0;">Coûts de votre projet</h3>
                        <div style="padding:10px;">
                            <div style="background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:8px;text-align:center;">
                                <div style="font-size:13px;color:#666;">🏠 Prix d'acquisition de votre bien</div>
                                <div style="font-size:24px;font-weight:800;color:#222;">{{ $formatMoney($acquisitionPrice) }}</div>
                            </div>
                            <div style="background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:8px;text-align:center;">
                                <div style="font-size:13px;color:#666;">🔨 Votre budget travaux réno'</div>
                                <div style="font-size:24px;font-weight:800;color:#222;">{{ $formatMoney($workBudget) }}</div>
                            </div>
                            <div style="background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                                <div style="font-size:13px;color:#666;">📊 Coût total de votre projet</div>
                                <div style="font-size:24px;font-weight:800;color:#222;">{{ $formatMoney($totalProjectCost) }}</div>
                                <div style="font-size:12px;color:#666;">(acquisition + travaux)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="{{ $cardsBaseStyle }}margin:0;">
                    <h3 style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Répartition de ces coûts</h3>
                    <img src="{{ $costChartUrl }}" alt="Répartition des coûts" style="display:block;width:100%;border:1px solid #E3E3E1;border-radius:12px;">
                </div>
            </div>

            <div style="width:50%;display:flex;flex-direction:column;gap:16px;">
                <div style="{{ $cardsBaseStyle }}margin:0;">
                    <h3 style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Coût <u>net</u> de mon projet après aides</h3>
                    <div style="display:flex;gap:8px;">
                        <div style="width:50%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                            <div style="font-size:13px;color:#666;">🎉 Subventions déduites</div>
                            <div style="font-size:24px;font-weight:800;color:#a4c520;">- {{ $formatMoney($totalAids) }}</div>
                        </div>
                        <div style="width:50%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                            <div style="font-size:13px;color:#666;">✨ Coût après déduction</div>
                            <div style="font-size:24px;font-weight:800;color:#222;"><u>{{ $formatMoney($finalCost) }}</u></div>
                        </div>
                    </div>
                </div>

                <div style="{{ $cardsBaseStyle }}margin:0;">
                    <h3 style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Impact des aides sur votre projet</h3>
                    <img src="{{ $impactChartUrl }}" alt="Impact des aides" style="display:block;width:100%;border:1px solid #E3E3E1;border-radius:12px;">
                </div>

                <div style="{{ $cardsBaseStyle }}margin:0;">
                    <h3 style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Vos aides financières</h3>
                    <div style="display:flex;gap:8px;">
                        <div style="width:33.33%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                            <div style="font-size:12px;color:#666;">💳 Reste à charge travaux</div>
                            <div style="font-size:20px;font-weight:800;color:#222;">{{ $formatMoney($remainingWorkCost) }}</div>
                        </div>
                        <div style="width:33.33%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                            <div style="font-size:12px;color:#666;">📈 Couverture des travaux</div>
                            <div style="font-size:20px;font-weight:800;color:#222;">{{ number_format($workCoverage, 1, ',', ' ') }}%</div>
                        </div>
                        <div style="width:33.33%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                            <div style="font-size:12px;color:#666;">✅ Aides éligibles</div>
                            <div style="font-size:20px;font-weight:800;color:#222;">{{ count($aidesDetails) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">🏦 Aides disponibles pour votre projet</h2>
            <div style="display:flex;flex-direction:column;gap:12px;padding-top:8px;">
                @forelse ($aidesDetails as $aide)
                    <article style="display:flex;flex-direction:column;border:1px solid #E3E3E1;border-radius:14px;padding:14px;background:#fff;">
                        <div style="display:flex;align-items:center;justify-content:space-between;color:#0b1c13;padding-bottom:6px;margin-bottom:8px;border-bottom:2px solid #0b1c13;">
                            <h3 style="font-size:19px;font-weight:800;margin:0;">{{ data_get($aide, 'nom', 'Aide') }}</h3>
                            @if (!empty(data_get($aide, 'url')))
                                <a href="{{ data_get($aide, 'url') }}" target="_blank" rel="noopener" style="font-size:13px;font-weight:500;text-decoration:underline;color:#a4c520;">En savoir plus ↗</a>
                            @endif
                        </div>
                        <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">📄 Type</span><span style="{{ $valueStyle }}"><u>{{ $mapAidType(data_get($aide, 'type')) }}</u></span></div>
                        <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">💰 Montant estimé</span><span style="{{ $valueStyle }}">{{ $formatMoney(data_get($aide, 'montant', data_get($aide, 'valeur'))) }}</span></div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">📖 Description</span><span style="{{ $valueStyle }}">{{ data_get($aide, 'description', data_get($aide, 'detail', 'N/A')) }}</span></div>
                    </article>
                @empty
                    <p style="margin:0;color:#666;">Aucune aide détaillée transmise dans simulation.aides_details.</p>
                @endforelse
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">🍃 Économies d'énergie estimées</h2>
            <div style="display:flex;flex-direction:row;align-items:stretch;gap:8px;padding-top:8px;">
                <div style="width:48%;border:1px solid #E3E3E1;border-radius:14px;overflow:hidden;">
                    <h3 style="width:100%;text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:8px;margin:0;font-size:16px;">DPE Actuel</h3>
                    <div style="padding:10px;">
                        @foreach (['A','B','C','D','E','F','G'] as $letter)
                            @php $active = $dpeCurrent === $letter; @endphp
                            <div style="display:flex;align-items:center;justify-content:space-between;border:{{ $active ? '2px solid #a4c520' : '1px solid #E3E3E1' }};border-radius:10px;padding:6px 8px;margin-bottom:5px;background:{{ $active ? '#fffcf1' : '#fff' }};">
                                <span style="font-size:18px;font-weight:800;min-width:26px;text-align:center;">{{ $letter }}</span>
                                @if ($active)
                                    <span style="font-size:13px;font-weight:700;color:#222;">Classe actuelle ▶</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="width:4%;display:flex;align-items:center;justify-content:center;font-size:26px;color:#a4c520;font-weight:700;">→</div>

                <div style="width:48%;border:1px solid #E3E3E1;border-radius:14px;overflow:hidden;">
                    <h3 style="width:100%;text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:8px;margin:0;font-size:16px;">DPE Visé</h3>
                    <div style="padding:10px;">
                        @foreach (['A','B','C','D','E','F','G'] as $letter)
                            @php $active = $dpeTarget === $letter; @endphp
                            <div style="display:flex;align-items:center;justify-content:space-between;border:{{ $active ? '2px solid #a4c520' : '1px solid #E3E3E1' }};border-radius:10px;padding:6px 8px;margin-bottom:5px;background:{{ $active ? '#fffcf1' : '#fff' }};">
                                <span style="font-size:18px;font-weight:800;min-width:26px;text-align:center;">{{ $letter }}</span>
                                @if ($active)
                                    <span style="font-size:13px;font-weight:700;color:#222;">Classe visée ▶</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;border:1px solid #E3E3E1;border-radius:14px;margin-top:10px;overflow:hidden;">
                <h3 style="width:100%;text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:10px;margin:0;font-size:16px;">Impacts positifs de votre rénovation</h3>
                <div style="display:flex;gap:8px;padding:10px;">
                    <div style="width:50%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                        <div style="font-size:13px;color:#666;">💳 Économies sur vos factures d'énergie</div>
                        <div style="font-size:22px;font-weight:800;color:#222;">{{ $estimatedEnergySavings ? '~ ' . number_format($estimatedEnergySavings, 0, ',', ' ') . ' € / an' : 'N/A' }}</div>
                    </div>
                    <div style="width:50%;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:12px;text-align:center;">
                        <div style="font-size:13px;color:#666;">🍃 Réduction de vos émissions de CO₂</div>
                        <div style="font-size:22px;font-weight:800;color:#222;">{{ $estimatedCO2Savings ? '~ ' . number_format($estimatedCO2Savings, 0, ',', ' ') . ' kg / an' : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">🔨 Types de travaux envisagés</h2>
            <div style="padding:8px;border:1px solid #E3E3E1;border-radius:14px;">
                <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px;">
                    @forelse ($travaux as $travail)
                        <li style="display:flex;justify-content:space-between;align-items:center;border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;">
                            <span style="font-size:14px;font-weight:500;color:#222;">{{ $mapWork($travail) }}</span>
                            <span style="font-size:14px;font-weight:600;color:#a4c520;">↗</span>
                        </li>
                    @empty
                        <li style="color:#666;">Aucun type de travaux transmis.</li>
                    @endforelse
                </ul>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">📊 Informations complémentaires</h2>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <article style="display:flex;flex-direction:column;border:1px solid #E3E3E1;border-radius:14px;padding:14px;background:#fff;">
                    <div style="display:flex;align-items:center;justify-content:space-between;color:#0b1c13;padding-bottom:6px;margin-bottom:8px;border-bottom:2px solid #0b1c13;">
                        <h3 style="font-size:19px;font-weight:800;margin:0;">Fiscalité</h3>
                    </div>
                    <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">💸 Taxe foncière annuelle</span><span style="{{ $valueStyle }}">{{ $formatMoney(data_get($utilisateur, 'taxe_fonciere')) }}</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">💰 Exonération de taxe foncière possible</span><span style="{{ $valueStyle }}">À confirmer selon commune</span></div>
                </article>

                <article style="display:flex;flex-direction:column;border:1px solid #E3E3E1;border-radius:14px;padding:14px;background:#fff;">
                    <div style="display:flex;align-items:center;justify-content:space-between;color:#0b1c13;padding-bottom:6px;margin-bottom:8px;border-bottom:2px solid #0b1c13;">
                        <h3 style="font-size:19px;font-weight:800;margin:0;">Parcours d'aide</h3>
                    </div>
                    <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">📄 Type de parcours MaPrimeRénov'</span><span style="{{ $valueStyle }}">{{ ucfirst((string) data_get($simulationPayload, 'parcours_aide', data_get($utilisateur, 'parcours_aide', 'N/A'))) }}</span></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">❓ Accompagnement nécessaire</span><span style="{{ $valueStyle }}">{{ $formatBool(data_get($simulationPayload, 'condition_depenses', data_get($utilisateur, 'condition_depenses'))) }}</span></div>
                </article>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">📝 Prochaines étapes recommandées</h2>
            <div style="padding:8px;border:1px solid #E3E3E1;border-radius:14px;">
                <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px;">
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">1. Faire <u>réaliser un audit énergétique complet</u> par un professionnel RGE. <span style="color:#a4c520;">↗</span></a></li>
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://france-renov.gouv.fr/preparer-projet/trouver-conseiller" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">2. <u>Consulter un conseiller France Rénov'</u> afin de valider votre éligibilité aux aides. <span style="color:#a4c520;">↗</span></a></li>
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.service-public.gouv.fr/particuliers/vosdroits/R39412" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">3. <u>Comparer plusieurs devis</u> d'artisans RGE. <span style="color:#a4c520;">↗</span></a></li>
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.anah.gouv.fr/document/maprimerenov-mode-emploi" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">4. <u>Constituer votre dossier MaPrimeRénov'</u> avant le début des travaux. <span style="color:#a4c520;">↗</span></a></li>
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/faire-des-economies-denergie/renovation-energetique-les-aides-auxquelles-vous-pouvez-pretendre#" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">5. <u>Vérifier les conditions d'éligibilité</u> auprès des organismes concernés. <span style="color:#a4c520;">↗</span></a></li>
                    <li style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener" style="display:block;color:#222;text-decoration:none;">6. <u>Planifier vos travaux</u> selon les recommandations de l'audit énergétique. <span style="color:#a4c520;">↗</span></a></li>
                </ul>
            </div>
        </section>

        <section style="{{ $cardsBaseStyle }}">
            <h2 style="{{ $h2Style }}">📧 Vos coordonnées</h2>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">👤 NOM & Prénom</span><span style="{{ $valueStyle }}">{{ data_get($utilisateur, 'nom', $user->last_name ?? '') }} {{ data_get($utilisateur, 'prenom', $user->first_name ?? '') }}</span></div>
            <div style="{{ $rowStyle }}"><span style="{{ $labelStyle }}">📬 Adresse mail</span><span style="{{ $valueStyle }}">{{ data_get($utilisateur, 'email', $user->email ?? 'N/A') }}</span></div>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:10px;"><span style="{{ $labelStyle }}">📞 Téléphone</span><span style="{{ $valueStyle }}">{{ data_get($utilisateur, 'telephone', $user->telephone ?? $user->phone ?? 'N/A') }}</span></div>
        </section>

        <section style="{{ $cardsBaseStyle }}margin-bottom:0;">
            <h2 style="{{ $h2Style }}">ℹ️ Informations importantes</h2>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;font-weight:500;color:#0b1c13;line-height:1.45;border:1px solid #E3E3E1;padding:14px;border-radius:12px;">
                <p style="margin:0;"><u>Avertissement</u> : Ces estimations sont basées sur votre profil et les caractéristiques du bien renseignées. Les montants indiqués peuvent varier selon les conditions d'éligibilité officielles.</p>
                <p style="margin:0;">Afin d'obtenir des montants précis et démarrer vos démarches officielles, nous vous recommandons de :</p>
                <ul style="margin:0;padding-left:18px;">
                    <li>Consulter un conseiller <a href="https://france-renov.gouv.fr/" target="_blank" rel="noopener" style="color:#666;text-decoration:underline;">France Rénov'</a>,</li>
                    <li>Visiter <a href="https://mesaidesreno.beta.gouv.fr/" target="_blank" rel="noopener" style="color:#666;text-decoration:underline;">Mes Aides Réno</a>,</li>
                    <li>Contacter l'<a href="https://www.anah.gouv.fr/" target="_blank" rel="noopener" style="color:#666;text-decoration:underline;">ANAH</a>.</li>
                </ul>
            </div>
        </section>
    </div>

    <hr style="border:none;border-top:1px solid #E3E3E1;margin:0;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
