@php
    $user = $simulation->user;
    $ad = $simulation->ad;
    $works = $simulation->works;
    $aids = $simulation->aids;
    $adImage = $ad?->images?->first()?->url;

    $formatMoney = static function ($value): string {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return number_format((float) $value, 0, ',', ' ') . ' €';
    };

    $projectCost = ((float) ($ad->prix ?? 0)) + ((float) ($user->budget_travaux ?? 0));
    $totalAids = (float) ($simulation->montant_total_aides ?? 0);
    $netCost = $projectCost - $totalAids;
@endphp

@extends('layouts.email', ['title' => 'Compte-rendu simulation - ' . $appName])

@section('content')
    <div class="header" style="background: #0b1c13; color: #ffffff; padding: 32px 20px; text-align: center; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
        <h1 style="font-size: 28px; margin: 0 0 10px 0; font-weight: 600; line-height: 1.3;">🏡 Votre projet d'achat immobilier</h1>
        <p style="font-size: 14px; margin: 0; opacity: 0.95;">Compte-rendu REVESTA</p>
    </div>

    <div class="content" style="padding: 30px; background-color: #ffffff; color: #222222; font-size: 14px; line-height: 1.6; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
        <div class="greeting" style="font-size: 16px; margin-bottom: 20px; color: #222222;">
            Bonjour <strong style="color: #a4c520;">{{ $user->full_name }}</strong>,
        </div>

        <p style="margin: 0 0 16px 0;">Voici le compte-rendu détaillé des aides disponibles pour votre projet.</p>

        @if (!empty($adImage))
            <div style="margin: 0 0 20px 0; border: 1px solid #E3E3E1; border-radius: 8px; overflow: hidden; background-color: #f9f9f9;">
                <img src="{{ $adImage }}" alt="Image du bien" style="display: block; width: 100%; max-height: 260px; object-fit: cover;">
            </div>
        @endif

        <div class="info-box" style="background-color: #f9f9f9; border-left: 4px solid #a4c520; padding: 14px 16px; margin: 20px 0; border-radius: 4px; color: #222222; border-top: 1px solid #E3E3E1; border-right: 1px solid #E3E3E1; border-bottom: 1px solid #E3E3E1;">
            <strong style="color: #0b1c13;">📌 Bien analysé</strong><br>
            {{ $ad->titre ?? 'Annonce immobilière' }}<br>
            {{ $ad->code_postal }} {{ $ad->ville }}
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">🏠 Le bien immobilier</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Type : <span style="color: #222222; font-weight: 600;">{{ $ad->housing_type_id ?? 'N/A' }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Surface : <span style="color: #222222; font-weight: 600;">{{ $ad->surface ? number_format((float) $ad->surface, 0, ',', ' ') . ' m²' : 'N/A' }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Pièces : <span style="color: #222222; font-weight: 600;">{{ $ad->pieces ? number_format((float) $ad->pieces, 0, ',', ' ') : 'N/A' }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Classe DPE : <span style="color: #222222; font-weight: 600;">{{ $ad->dpe_class_id ?? 'N/A' }}</span></div>
            <div style="padding: 10px 12px; background-color: #f9f9f9; color: #666666;">Annonce :
                @if (!empty($ad->url))
                    <a href="{{ $ad->url }}" target="_blank" rel="noopener" style="color: #a4c520; text-decoration: underline;">Voir l'annonce</a>
                @else
                    <span style="color: #222222; font-weight: 600;">N/A</span>
                @endif
            </div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">👤 Votre profil</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Ménage : <span style="color: #222222; font-weight: 600;">{{ $user->nombre_personnes ?? 'N/A' }} personne(s)</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Revenus annuels : <span style="color: #222222; font-weight: 600;">{{ $formatMoney($user->revenus) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Résidence principale : <span style="color: #222222; font-weight: 600;">{{ $user->residence_principale ? 'Oui' : 'Non' }}</span></div>
            <div style="padding: 10px 12px; color: #666666;">Période de construction : <span style="color: #222222; font-weight: 600;">{{ $user->construction_period_id ?? 'N/A' }}</span></div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">💰 Synthèse financière</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Prix du bien : <span style="color: #222222; font-weight: 600;">{{ $formatMoney($ad->prix) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Budget travaux : <span style="color: #222222; font-weight: 600;">{{ $formatMoney($user->budget_travaux) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Coût total du projet : <span style="color: #222222; font-weight: 600;">{{ $formatMoney($projectCost) }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Montant total des aides : <span style="color: #a4c520; font-weight: 700;">- {{ $formatMoney($totalAids) }}</span></div>
            <div style="padding: 10px 12px; background-color: #f9f9f9; color: #666666;">Coût net estimé : <span style="color: #0b1c13; font-weight: 700;">{{ $formatMoney($netCost) }}</span> &nbsp;•&nbsp; Couverture : <span style="color: #222222; font-weight: 600;">{{ number_format((float) ($simulation->pourcentage_bien ?? 0), 2, ',', ' ') }}%</span></div>
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">🧱 Travaux envisagés</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            @forelse ($works as $work)
                <li style="margin: 0 0 6px 0;">{{ $work->label }}</li>
            @empty
                <li style="margin: 0 0 6px 0;">Aucun travail précisé.</li>
            @endforelse
        </ul>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">🏦 Aides détectées</h3>
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

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">📧 Vos coordonnées</h3>
        <div style="border: 1px solid #E3E3E1; border-radius: 6px; overflow: hidden; margin-bottom: 16px;">
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; background-color: #f9f9f9; color: #666666;">Nom : <span style="color: #222222; font-weight: 600;">{{ $user->full_name }}</span></div>
            <div style="padding: 10px 12px; border-bottom: 1px solid #E3E3E1; color: #666666;">Email : <span style="color: #222222; font-weight: 600;">{{ $user->email ?? 'N/A' }}</span></div>
            <div style="padding: 10px 12px; background-color: #f9f9f9; color: #666666;">Téléphone : <span style="color: #222222; font-weight: 600;">{{ $user->telephone ?? $user->phone ?? 'N/A' }}</span></div>
        </div>

        <div class="alert-box" style="background-color: #f9f9f9; border-left: 4px solid #a4c520; padding: 14px 16px; margin: 20px 0 0 0; border-radius: 4px; color: #666666; border-top: 1px solid #E3E3E1; border-right: 1px solid #E3E3E1; border-bottom: 1px solid #E3E3E1;">
            <strong style="color: #0b1c13;">ℹ️ Information :</strong>
            Ces montants sont des estimations et doivent être confirmés avec les dispositifs officiels.
        </div>
    </div>

    <hr class="divider" style="border: none; border-top: 1px solid #E3E3E1; margin: 0;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
