@extends('layouts.email', ['title' => 'Réinitialisation de mot de passe - ' . $appName])

@section('content')
        <!-- Header -->
        <div class="header">
            <h1>🔐 Réinitialisation de mot de passe</h1>
            <p>{{ $appName }}</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                Bonjour <strong>{{ $userName }}</strong>,
            </div>
            
            <p>
                Vous avez récemment demandé une réinitialisation de votre mot de passe. 
                Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
            </p>
            
            <!-- Information Box -->
            <div class="info-box">
                <strong>⏱️ Validité du lien :</strong> Ce lien de réinitialisation expire dans 
                <strong>{{ $expirationMinutes }} minutes</strong>. Après ce délai, vous devrez demander un nouveau lien.
            </div>
            
            <!-- Action Button -->
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Réinitialiser mon mot de passe
                </a>
            </div>
            
            <!-- Alternative Link -->
            <div class="link-section">
                <strong>Ou utilisez ce lien :</strong>
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </div>
            
            <!-- Security Warning -->
            <div class="security-warning">
                <strong>⚠️ Attention à la sécurité :</strong>
                <ul>
                    <li>Jamais nous ne vous demanderons votre mot de passe par email</li>
                    <li>Ne partagez jamais ce lien avec quiconque</li>
                    <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                </ul>
            </div>
            
            <!-- Not Your Request? -->
            <div class="alert-box">
                <strong>❓ Vous n'avez pas demandé cela ?</strong>
                <p style="margin-top: 8px;">
                    Si vous n'êtes pas à l'origine de cette demande, veuillez contacter immédiatement notre équipe support 
                    à l'adresse <strong><a href="mailto:{{ $contactEmail }}" style="color: #856404;">{{ $contactEmail }}</a></strong>
                    pour sécuriser votre compte.
                </p>
            </div>
            
            <hr class="divider">
            
            <!-- GDPR & Privacy Section -->
            <div class="gdpr-section">
                <h3>📋 Protection de vos données personnelles</h3>
                
                <p>
                    <strong>Responsable du traitement :</strong> {{ $appName }}<br>
                    <strong>Base légale :</strong> Consentement et exécution du contrat
                </p>
                
                <p>
                    <strong>Traitement des données :</strong>
                </p>
                <ul>
                    <li>Nous avons généré un jeton de réinitialisation unique associé à votre adresse email</li>
                    <li>Ce jeton n'est valable que <strong>{{ $expirationMinutes }} minutes</strong> et est supprimé automatiquement après expiration</li>
                    <li>Le lien est chiffré et sécurisé (HTTPS)</li>
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
