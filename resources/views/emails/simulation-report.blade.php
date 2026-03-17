@php
    $user = $simulation->user;
    $ad = $simulation->ad;
    $works = $simulation->works;
    $aids = $simulation->aids;
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

        <div class="info-box" style="background-color: #f9f9f9; border-left: 4px solid #a4c520; padding: 14px 16px; margin: 20px 0; border-radius: 4px; color: #222222; border-top: 1px solid #E3E3E1; border-right: 1px solid #E3E3E1; border-bottom: 1px solid #E3E3E1;">
            <strong style="color: #0b1c13;">📌 Bien analysé</strong><br>
            {{ $ad->titre ?? 'Annonce immobilière' }}<br>
            {{ $ad->code_postal }} {{ $ad->ville }}
        </div>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">👤 Votre profil</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            <li style="margin: 0 0 6px 0;">Ménage : {{ $user->nombre_personnes ?? 'N/A' }} personne(s)</li>
            <li style="margin: 0 0 6px 0;">Revenus : {{ $user->revenus ?? 'N/A' }} €</li>
            <li style="margin: 0 0 6px 0;">Résidence principale : {{ $user->residence_principale ? 'Oui' : 'Non' }}</li>
        </ul>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">💰 Synthèse financière</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            <li style="margin: 0 0 6px 0;">Prix du bien : {{ $ad->prix ?? 0 }} €</li>
            <li style="margin: 0 0 6px 0;">Montant total des aides : {{ $simulation->montant_total_aides ?? 0 }} €</li>
            <li style="margin: 0 0 6px 0;">Couverture estimée : {{ $simulation->pourcentage_bien ?? 0 }}%</li>
        </ul>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">🧱 Travaux envisagés</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            @forelse ($works as $work)
                <li style="margin: 0 0 6px 0;">{{ $work->label }}</li>
            @empty
                <li style="margin: 0 0 6px 0;">Aucun travail précisé.</li>
            @endforelse
        </ul>

        <h3 style="font-size: 18px; margin: 22px 0 10px 0; color: #0b1c13;">🏦 Aides détectées</h3>
        <ul style="margin: 0 0 16px 18px; padding: 0; color: #222222;">
            @forelse ($aids as $aid)
                <li style="margin: 0 0 6px 0;">
                    {{ $aid->name }} — {{ $aid->pivot?->amount ?? 0 }} €
                </li>
            @empty
                <li style="margin: 0 0 6px 0;">Aucune aide détaillée fournie.</li>
            @endforelse
        </ul>

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
