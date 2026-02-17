@extends('layouts.email', ['title' => 'Confirmation de changement de mot de passe - ' . $appName])

@section('content')
    <!-- Header -->
    <div class="header">
        <h1>✅ Changement de mot de passe confirmé</h1>
        <p>{{ $appName }}</p>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Greeting -->
        <div class="greeting">
            Bonjour <strong>{{ $userName }}</strong>,
        </div>
        
        <p>
            Cet email confirme que le mot de passe de votre compte a été modifié avec succès.
            Votre compte est maintenant protégé par votre nouveau mot de passe.
        </p>
        
        <!-- Information Box -->
        <div class="info-box">
            <strong>📝 Informations sur le changement :</strong><br>
            <strong>Adresse email :</strong> {{ $userEmail }}<br>
            <strong>Date et heure :</strong> {{ now()->format('d/m/Y à H:i') }}
        </div>
        
        <!-- Security Warning -->
        <div class="security-warning">
            <strong>⚠️ Attention à la sécurité :</strong>
            <ul>
                <li>Jamais nous ne vous demanderons votre mot de passe par email</li>
                <li>Assurez-vous que votre nouveau mot de passe est fort et unique</li>
                <li>Ne partagez jamais votre mot de passe avec quiconque</li>
            </ul>
        </div>
        
        <!-- Not Your Request? -->
        <div class="alert-box">
            <strong>❓ Vous n'avez pas effectué ce changement ?</strong>
            <p style="margin-top: 8px;">
                Si ce n'est pas vous qui avez changé le mot de passe, votre compte pourrait être compromis.
                Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe immédiatement.
            </p>
            
            <!-- Action Button for Suspicious Activity -->
            <div class="button-container" style="margin-top: 15px;">
                <a href="{{ route('password.request') }}" class="reset-button" style="background-color: #dc3545;">
                    Réinitialiser mon mot de passe maintenant
                </a>
            </div>
            
            <p style="margin-top: 15px;">
                Vous pouvez également contacter notre équipe support immédiatement à l'adresse 
                <strong><a href="mailto:{{ $contactEmail }}" style="color: #dc3545;">{{ $contactEmail }}</a></strong>
                pour sécuriser votre compte.
            </p>
        </div>
        
        <hr class="divider">
        
        <!-- What to do next -->
        <div class="info-box">
            <strong>✨ Pour votre sécurité :</strong>
            <ul>
                <li>Assurez-vous de mémoriser votre nouveau mot de passe</li>
                <li>Déconnectez-vous d'autres navigateurs ou appareils si nécessaire</li>
                <li>Continuez à utiliser un mot de passe fort et unique</li>
                <li>Activez l'authentification à deux facteurs si disponible</li>
            </ul>
        </div>
        
        <hr class="divider">
        
        <!-- GDPR & Privacy Section -->
        <div class="gdpr-section">
            <h3>📋 Protection de vos données personnelles</h3>
            
            <p>
                <strong>Responsable du traitement :</strong> {{ $appName }}<br>
                <strong>Base légale :</strong> Sécurité du compte et consentement
            </p>
            
            <p>
                <strong>Traitement des données :</strong>
            </p>
            <ul>
                <li>Nous avons enregistré le changement de votre mot de passe pour votre sécurité</li>
                <li>Cet événement est journalisé et crypté dans nos bases de données</li>
                <li>Nous ne transmettez jamais votre mot de passe par email</li>
                <li>Vos données ne sont pas partagées avec des tiers</li>
            </ul>
            
            <p>
                <strong>Vos droits :</strong>
            </p>
            <ul>
                <li>Droit d'accès à vos données personnelles</li>
                <li>Droit de rectification ou suppression de vos données</li>
                <li>Droit à la portabilité de vos données</li>
                <li>Droit d'opposition au traitement</li>
            </ul>
            
            <p style="margin-top: 15px;">
                Pour exercer ces droits ou pour toute question concernant la protection de vos données, 
                veuillez nous contacter à <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
            </p>
        </div>
    </div>
    
    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
