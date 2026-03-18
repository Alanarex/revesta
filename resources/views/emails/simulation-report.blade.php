@php
    $user = $simulation->user;
    $ad = $simulation->ad;

    $annonce = (array) data_get($apiPayload ?? [], 'annonce', []);
    $utilisateur = (array) data_get($apiPayload ?? [], 'utilisateur', []);
    $simulationPayload = (array) data_get($apiPayload ?? [], 'simulation', []);

    $adImage = data_get($annonce, 'images.0', $ad?->images?->first()?->url);
    $logoUrl = asset('images/Revesta_LogoTextuel-Blanc.svg');

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
        'options' => ['plugins' => ['legend' => ['position' => 'bottom']]],
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

    $dpeColors = [
        'A' => '#009900',
        'B' => '#66cc00',
        'C' => '#cccc00',
        'D' => '#ffcc00',
        'E' => '#ff9900',
        'F' => '#ff6600',
        'G' => '#ff0000',
    ];
@endphp

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ rtrim($appUrl, '/') }}/images/icon-128.png" type="image/png">
        <title>REVESTA - Compte-rendu de votre projet immobilier</title>
        <style>
            {!! file_get_contents(base_path('resources/css/emails.css')) !!}
        </style>
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

@section('content')
<div style="max-width:650px;margin:0 auto;background:#fffcf1;padding:14px 0;font-family:Montserrat,-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Oxygen','Ubuntu','Cantarell','Fira Sans','Droid Sans','Helvetica Neue',sans-serif;color:#222;">

    <section style="background:#0b1c13;color:#fff;padding:16px 24px;margin:0 16px 16px 16px;border-radius:48px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr>
                <td align="left" valign="middle">
                    <a href="https://www.revesta.fr" target="_blank" rel="noopener" style="text-decoration:none;display:inline-block;">
                        <img src="{{ $logoUrl }}" alt="REVESTA Logo" style="height:24px;display:block;border:0;">
                    </a>
                </td>
                <td align="right" valign="middle" style="white-space:nowrap;">
                    <a href="https://www.facebook.com/revesta.fr" target="_blank" rel="noopener" title="Facebook" style="text-decoration:none;display:inline-block;margin-left:8px;">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/facebook--v1.png" alt="Facebook" style="width:18px;height:18px;display:block;border:0;">
                    </a>
                    <a href="https://www.instagram.com/revesta.fr" target="_blank" rel="noopener" title="Instagram" style="text-decoration:none;display:inline-block;margin-left:8px;">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/instagram-new--v1.png" alt="Instagram" style="width:18px;height:18px;display:block;border:0;">
                    </a>
                    <a href="https://www.linkedin.com/company/re-vesta/" target="_blank" rel="noopener" title="LinkedIn" style="text-decoration:none;display:inline-block;margin-left:8px;">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/linkedin.png" alt="LinkedIn" style="width:18px;height:18px;display:block;border:0;">
                    </a>
                </td>
            </tr>
        </table>
    </section>

    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td align="center" style="font-size:15px;font-weight:600;padding-bottom:4px;">Hello {{ data_get($utilisateur, 'prenom', $user->first_name ?? 'Utilisateur') }} ! 👋</td></tr>
            <tr><td align="center" style="font-size:32px;font-weight:800;line-height:1.2;color:#0b1c13;padding-bottom:4px;">Votre projet d'achat immobilier</td></tr>
            <tr><td align="center" style="font-size:14px;font-weight:500;line-height:1.4;">Voici le compte-rendu détaillé des aides disponibles pour votre projet.</td></tr>
        </table>
    </section>

    <section style="margin:0 16px 16px 16px;background:#fff;border:1px solid #E3E3E1;border-radius:24px;overflow:hidden;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;table-layout:fixed;">
            <tr>
                <td width="50%" valign="top" style="background:#f9f9f9;">
                    @if (!empty($adImage))
                        <img src="{{ $adImage }}" alt="Image du bien" style="display:block;width:100%;height:auto;min-height:260px;object-fit:cover;">
                    @else
                        <div style="padding:120px 10px;text-align:center;color:#666;">Image indisponible</div>
                    @endif
                    <div style="background:#0b1c13;color:#fff;padding:10px 12px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;margin-bottom:4px;">{{ data_get($annonce, 'titre', $ad->titre ?? 'Annonce immobilière') }}</div>
                        @if (!empty(data_get($annonce, 'url', $ad->url)))
                            <a href="{{ data_get($annonce, 'url', $ad->url) }}" target="_blank" rel="noopener" style="font-size:13px;color:#a4c520;text-decoration:underline;">Voir l'annonce</a>
                        @endif
                    </div>
                </td>
                <td width="50%" valign="top" style="padding:20px;background:#fff;">
                    <div style="color:#0b1c13;font-size:21px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">🏠 Le bien immobilier</div>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                        <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📍 Commune</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($annonce, 'code_postal', $ad->code_postal ?? 'N/A') }} - {{ data_get($annonce, 'ville', $ad->ville ?? 'N/A') }}</td></tr>
                        <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🏢 Type</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ ucfirst((string) data_get($annonce, 'type_logement', $ad->housing_type_id ?? 'N/A')) }}</td></tr>
                        <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🚪 Nombre de pièces</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($annonce, 'pieces', $ad->pieces ?? 'N/A') }} pièces</td></tr>
                        <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📐 Surface</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($annonce, 'surface', $ad->surface ?? 'N/A') }} m²</td></tr>
                        <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🔥 DPE actuel</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $dpeCurrent }}</td></tr>
                        <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">⏳ Date extraction</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ $formatDate(data_get($annonce, 'date_extraction')) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">👤 Mon profil</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🏡 Mon futur statut</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ ucfirst((string) data_get($utilisateur, 'statut', 'N/A')) }}</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">👪 Composition de mon ménage</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($utilisateur, 'nombre_personnes', 'N/A') }} personnes</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">💸 Mes revenus annuels</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney(data_get($utilisateur, 'revenus')) }} / an</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🏠 Résidence principale</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatBool(data_get($utilisateur, 'residence_principale')) }}</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">📅 Période de construction du logement</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ ucfirst((string) data_get($utilisateur, 'periode_construction', 'N/A')) }}</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">💰 Plan financier</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🏠 Prix d'acquisition de votre bien</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney($acquisitionPrice) }}</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🔨 Votre budget travaux réno'</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney($workBudget) }}</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">📊 Coût total de votre projet</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ $formatMoney($totalProjectCost) }}</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">Coût net de mon projet après aides</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">🎉 Subventions déduites</td><td align="right" style="padding:8px 0;color:#a4c520;font-size:14px;font-weight:700;border-bottom:1px solid #f1f1f1;">- {{ $formatMoney($totalAids) }}</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">✨ Coût après déduction</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:700;"><u>{{ $formatMoney($finalCost) }}</u></td></tr>
        </table>
    </section>

    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:12px 0;table-layout:fixed;">
            <tr>
                <td width="50%" valign="top" style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;">
                    <div style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Répartition de ces coûts</div>
                    <img src="{{ $costChartUrl }}" alt="Répartition des coûts" style="display:block;width:100%;border:1px solid #E3E3E1;border-radius:12px;">
                </td>
                <td width="50%" valign="top" style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;">
                    <div style="font-size:16px;text-align:center;background:#f9f9f9;border:1px solid #E3E3E1;border-radius:12px;padding:10px;margin:0 0 10px 0;">Impact des aides sur votre projet</div>
                    <img src="{{ $impactChartUrl }}" alt="Impact des aides" style="display:block;width:100%;border:1px solid #E3E3E1;border-radius:12px;">
                </td>
            </tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">Vos aides financières</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">💳 Reste à charge travaux</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney($remainingWorkCost) }}</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📈 Couverture des travaux</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ number_format($workCoverage, 1, ',', ' ') }}%</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">✅ Aides éligibles</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ count($aidesDetails) }}</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">🏦 Aides disponibles pour votre projet</div>
        @forelse ($aidesDetails as $aide)
            <div style="border:1px solid #E3E3E1;border-radius:14px;padding:14px;background:#fff;margin-bottom:12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding-bottom:8px;border-bottom:2px solid #0b1c13;font-size:19px;font-weight:800;color:#0b1c13;">{{ data_get($aide, 'nom', 'Aide') }}</td>
                        <td align="right" style="padding-bottom:8px;border-bottom:2px solid #0b1c13;">
                            @if (!empty(data_get($aide, 'url')))
                                <a href="{{ data_get($aide, 'url') }}" target="_blank" rel="noopener" style="font-size:13px;font-weight:500;text-decoration:underline;color:#a4c520;">En savoir plus ↗</a>
                            @endif
                        </td>
                    </tr>
                    <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📄 Type</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;"><u>{{ $mapAidType(data_get($aide, 'type')) }}</u></td></tr>
                    <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">💰 Montant estimé</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney(data_get($aide, 'montant', data_get($aide, 'valeur'))) }}</td></tr>
                    <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">📖 Description</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ data_get($aide, 'description', data_get($aide, 'detail', 'N/A')) }}</td></tr>
                </table>
            </div>
        @empty
            <p style="margin:0;color:#666;">Aucune aide détaillée transmise dans simulation.aides_details.</p>
        @endforelse
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">🍃 Économies d'énergie estimées</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:10px 0;table-layout:fixed;">
            <tr>
                <td width="48%" valign="top" style="border:1px solid #E3E3E1;border-radius:14px;overflow:hidden;">
                    <div style="text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:8px;font-size:16px;">DPE Actuel</div>
                    <table role="presentation" width="100%" cellpadding="4" cellspacing="0" border="0" style="padding:8px;border-collapse:separate;">
                        @foreach (['A','B','C','D','E','F','G'] as $letter)
                            @php $active = $dpeCurrent === $letter; @endphp
                            <tr>
                                <td align="center" style="background:{{ $dpeColors[$letter] }};color:#111;font-size:16px;font-weight:800;border-radius:8px;width:28px;">{{ $letter }}</td>
                                <td align="right" style="font-size:13px;font-weight:700;color:{{ $active ? '#0b1c13' : '#999' }};">{{ $active ? 'Classe actuelle ▶' : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td width="4%" align="center" valign="middle" style="font-size:24px;color:#a4c520;font-weight:700;">→</td>
                <td width="48%" valign="top" style="border:1px solid #E3E3E1;border-radius:14px;overflow:hidden;">
                    <div style="text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:8px;font-size:16px;">DPE Visé</div>
                    <table role="presentation" width="100%" cellpadding="4" cellspacing="0" border="0" style="padding:8px;border-collapse:separate;">
                        @foreach (['A','B','C','D','E','F','G'] as $letter)
                            @php $active = $dpeTarget === $letter; @endphp
                            <tr>
                                <td align="center" style="background:{{ $dpeColors[$letter] }};color:#111;font-size:16px;font-weight:800;border-radius:8px;width:28px;">{{ $letter }}</td>
                                <td align="right" style="font-size:13px;font-weight:700;color:{{ $active ? '#0b1c13' : '#999' }};">{{ $active ? 'Classe visée ▶' : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        <div style="border:1px solid #E3E3E1;border-radius:14px;margin-top:10px;overflow:hidden;">
            <div style="text-align:center;background:#f9f9f9;border-bottom:1px solid #E3E3E1;padding:10px;font-size:16px;">Impacts positifs de votre rénovation</div>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:10px;border-collapse:collapse;">
                <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">💳 Économies sur vos factures d'énergie</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $estimatedEnergySavings ? '~ ' . number_format($estimatedEnergySavings, 0, ',', ' ') . ' € / an' : 'N/A' }}</td></tr>
                <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">🍃 Réduction de vos émissions de CO₂</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ $estimatedCO2Savings ? '~ ' . number_format($estimatedCO2Savings, 0, ',', ' ') . ' kg / an' : 'N/A' }}</td></tr>
            </table>
        </div>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">📊 Informations complémentaires</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">💸 Taxe foncière annuelle</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ $formatMoney(data_get($utilisateur, 'taxe_fonciere')) }}</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📄 Type de parcours MaPrimeRénov'</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ ucfirst((string) data_get($simulationPayload, 'parcours_aide', data_get($utilisateur, 'parcours_aide', 'N/A'))) }}</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">❓ Accompagnement nécessaire</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ $formatBool(data_get($simulationPayload, 'condition_depenses', data_get($utilisateur, 'condition_depenses'))) }}</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">📝 Prochaines étapes recommandées</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:0 8px;">
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">1. Faire <u>réaliser un audit énergétique complet</u> par un professionnel RGE. <span style="color:#a4c520;">↗</span></a></td></tr>
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://france-renov.gouv.fr/preparer-projet/trouver-conseiller" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">2. <u>Consulter un conseiller France Rénov'</u> afin de valider votre éligibilité aux aides. <span style="color:#a4c520;">↗</span></a></td></tr>
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.service-public.gouv.fr/particuliers/vosdroits/R39412" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">3. <u>Comparer plusieurs devis</u> d'artisans RGE. <span style="color:#a4c520;">↗</span></a></td></tr>
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.anah.gouv.fr/document/maprimerenov-mode-emploi" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">4. <u>Constituer votre dossier MaPrimeRénov'</u> avant le début des travaux. <span style="color:#a4c520;">↗</span></a></td></tr>
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/faire-des-economies-denergie/renovation-energetique-les-aides-auxquelles-vous-pouvez-pretendre#" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">5. <u>Vérifier les conditions d'éligibilité</u> auprès des organismes concernés. <span style="color:#a4c520;">↗</span></a></td></tr>
            <tr><td style="border:1px solid #E3E3E1;background:#f9f9f9;border-radius:10px;padding:10px 12px;"><a href="https://www.economie.gouv.fr/particuliers/investir-dans-limmobilier/tout-savoir-sur-laudit-energetique" target="_blank" rel="noopener" style="color:#222;text-decoration:none;display:block;">6. <u>Planifier vos travaux</u> selon les recommandations de l'audit énergétique. <span style="color:#a4c520;">↗</span></a></td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">📧 Vos coordonnées</div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">👤 NOM & Prénom</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($utilisateur, 'nom', $user->last_name ?? '') }} {{ data_get($utilisateur, 'prenom', $user->first_name ?? '') }}</td></tr>
            <tr><td style="padding:8px 8px 8px 0;color:#666;font-size:14px;border-bottom:1px solid #f1f1f1;">📬 Adresse mail</td><td align="right" style="padding:8px 0;color:#222;font-size:14px;font-weight:600;border-bottom:1px solid #f1f1f1;">{{ data_get($utilisateur, 'email', $user->email ?? 'N/A') }}</td></tr>
            <tr><td style="padding:8px 8px 0 0;color:#666;font-size:14px;">📞 Téléphone</td><td align="right" style="padding:8px 0 0 0;color:#222;font-size:14px;font-weight:600;">{{ data_get($utilisateur, 'telephone', $user->telephone ?? $user->phone ?? 'N/A') }}</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 0 16px;">
        <div style="color:#0b1c13;font-size:22px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">ℹ️ Informations importantes</div>
        <div style="font-size:13px;font-weight:500;color:#0b1c13;line-height:1.45;border:1px solid #E3E3E1;padding:14px;border-radius:12px;">
            <p style="margin:0 0 8px 0;"><u>Avertissement</u> : Ces estimations sont basées sur votre profil et les caractéristiques du bien renseignées. Les montants indiqués peuvent varier selon les conditions d'éligibilité officielles.</p>
            <p style="margin:0 0 8px 0;">Afin d'obtenir des montants précis et démarrer vos démarches officielles, nous vous recommandons de :</p>
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
