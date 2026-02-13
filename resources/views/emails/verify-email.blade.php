@extends('layouts.email', ['title' => 'Vérification email - ' . $appName])

@section('content')
    <!-- Header -->
    <div class="header">
        <h1>✉️ Vérification de votre email</h1>
        <p>{{ $appName }}</p>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Greeting -->
        <div class="greeting">
            Bonjour <strong>{{ $userName }}</strong>,
        </div>
        
        <p>
            Merci de vous etre enregistre! Pour activer votre compte, vous devez d'abord verifier votre adresse email et definir votre mot de passe.
        </p>
        
        <!-- Information Box -->
        <div class="info-box">
            <strong>⏱️ Validité du lien :</strong> Ce lien d'activation expire dans 
            <strong>24 heures</strong>. Après ce délai, vous devrez vous réenregistrer.
        </div>
        
        <!-- Action Button -->
        <div class="button-container">
            <a href="{{ $verificationUrl }}" class="reset-button">
                Verifier mon email et definir mon mot de passe
            </a>
        </div>
        
        <!-- Alternative Link -->
        <div class="link-section">
            <strong>Ou utilisez ce lien :</strong>
            <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
        </div>
        
        <!-- Security Warning -->
        <div class="security-warning">
            <strong>⚠️ Attention à la sécurité :</strong>
            <ul>
                <li>Jamais nous ne vous demanderons votre mot de passe par email</li>
                <li>Ne partagez jamais ce lien avec quiconque</li>
                <li>Si vous n'avez pas créé de compte, ignorez cet email</li>
            </ul>
        </div>
        
        <!-- Not Your Request? -->
        <div class="alert-box">
            <strong>❓ Vous n'avez pas créé ce compte ?</strong>
            <p style="margin-top: 8px;">
                Si vous ne reconnaissez pas cette inscription, veuillez contacter immédiatement notre équipe support 
                à l'adresse <strong><a href="mailto:{{ $contactEmail }}" style="color: #856404;">{{ $contactEmail }}</a></strong>
                pour sécuriser votre compte.
            </p>
        </div>
    </div>

    <hr class="divider">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
