@php
    $user = $simulation->user;
    $ad = $simulation->ad;
    $works = $simulation->works;
    $aids = $simulation->aids;
@endphp

@extends('layouts.email', ['title' => 'Compte-rendu simulation - ' . $appName])

@section('content')
    <div class="header">
        <h1>🏡 Votre projet d'achat immobilier</h1>
        <p>Compte-rendu REVESTA</p>
    </div>

    <div class="content">
        <div class="greeting">
            Bonjour <strong>{{ $user->full_name }}</strong>,
        </div>

        <p>Voici le compte-rendu détaillé des aides disponibles pour votre projet.</p>

        <div class="info-box">
            <strong>📌 Bien analysé</strong><br>
            {{ $ad->titre ?? 'Annonce immobilière' }}<br>
            {{ $ad->code_postal }} {{ $ad->ville }}
        </div>

        <h3>👤 Votre profil</h3>
        <ul>
            <li>Ménage : {{ $user->nombre_personnes ?? 'N/A' }} personne(s)</li>
            <li>Revenus : {{ $user->revenus ?? 'N/A' }} €</li>
            <li>Résidence principale : {{ $user->residence_principale ? 'Oui' : 'Non' }}</li>
        </ul>

        <h3>💰 Synthèse financière</h3>
        <ul>
            <li>Prix du bien : {{ $ad->prix ?? 0 }} €</li>
            <li>Montant total des aides : {{ $simulation->montant_total_aides ?? 0 }} €</li>
            <li>Couverture estimée : {{ $simulation->pourcentage_bien ?? 0 }}%</li>
        </ul>

        <h3>🧱 Travaux envisagés</h3>
        <ul>
            @forelse ($works as $work)
                <li>{{ $work->label }}</li>
            @empty
                <li>Aucun travail précisé.</li>
            @endforelse
        </ul>

        <h3>🏦 Aides détectées</h3>
        <ul>
            @forelse ($aids as $aid)
                <li>
                    {{ $aid->name }} — {{ $aid->pivot?->amount ?? 0 }} €
                </li>
            @empty
                <li>Aucune aide détaillée fournie.</li>
            @endforelse
        </ul>

        <div class="alert-box">
            <strong>ℹ️ Information :</strong>
            Ces montants sont des estimations et doivent être confirmés avec les dispositifs officiels.
        </div>
    </div>

    <hr class="divider">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
