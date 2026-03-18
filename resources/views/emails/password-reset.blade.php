@extends('layouts.email', ['title' => 'Réinitialisation de mot de passe - ' . $appName])

@section('content')
    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td align="center" style="font-size:15px;font-weight:600;padding-bottom:4px;">Bonjour {{ $userName }} 👋</td></tr>
            <tr><td align="center" style="font-size:32px;font-weight:800;line-height:1.2;color:#0b1c13;padding-bottom:4px;">🔐 Réinitialisation de mot de passe</td></tr>
            <tr><td align="center" style="font-size:14px;font-weight:500;line-height:1.4;">Sécurisez votre compte en définissant un nouveau mot de passe.</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.5;color:#222;">
            Vous avez récemment demandé une réinitialisation de votre mot de passe.
            Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
        </p>

        <div style="border:1px solid #E3E3E1;border-radius:12px;background:#f9f9f9;padding:12px;margin-bottom:14px;font-size:14px;color:#0b1c13;">
            <strong>⏱️ Validité du lien :</strong> Ce lien expire dans <strong>{{ $expirationMinutes }} minutes</strong>.
        </div>

        <div style="text-align:center;margin:0 0 14px 0;">
            <a href="{{ $resetUrl }}" style="display:inline-block;background:#a4c520;color:#0b1c13;text-decoration:none;font-weight:700;padding:11px 18px;border-radius:10px;">
                Réinitialiser mon mot de passe
            </a>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:14px;font-size:13px;word-break:break-all;">
            <strong>Ou utilisez ce lien :</strong><br>
            <a href="{{ $resetUrl }}" style="color:#a4c520;text-decoration:underline;">{{ $resetUrl }}</a>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:14px;background:#fff;">
            <strong style="color:#0b1c13;">⚠️ Attention à la sécurité :</strong>
            <ul style="margin:10px 0 0 18px;padding:0;color:#666;font-size:14px;line-height:1.5;">
                <li>Jamais nous ne vous demanderons votre mot de passe par email</li>
                <li>Ne partagez jamais ce lien avec quiconque</li>
                <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
            </ul>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;background:#f9f9f9;font-size:14px;color:#222;line-height:1.5;">
            <strong>❓ Vous n'avez pas demandé cela ?</strong>
            <p style="margin:8px 0 0 0;">
                Contactez notre équipe support à
                <a href="mailto:{{ $contactEmail }}" style="color:#a4c520;text-decoration:none;font-weight:700;">{{ $contactEmail }}</a>
                pour sécuriser votre compte.
            </p>
        </div>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 0 16px;">
        <div style="color:#0b1c13;font-size:20px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">📋 Protection de vos données personnelles</div>
        <p style="margin:0 0 10px 0;font-size:14px;line-height:1.5;color:#222;"><strong>Responsable du traitement :</strong> {{ $appName }}<br><strong>Base légale :</strong> Consentement et exécution du contrat</p>
        <ul style="margin:0;padding-left:18px;color:#666;font-size:14px;line-height:1.5;">
            <li>Jeton de réinitialisation unique associé à votre adresse email</li>
            <li>Jeton valable <strong>{{ $expirationMinutes }} minutes</strong> puis supprimé automatiquement</li>
            <li>Lien sécurisé en HTTPS</li>
            <li>Données non partagées avec des tiers</li>
        </ul>
        <p style="margin:12px 0 0 0;font-size:14px;line-height:1.5;color:#222;">Pour toute question, contactez-nous à <a href="mailto:{{ $contactEmail }}" style="color:#a4c520;text-decoration:none;">{{ $contactEmail }}</a>.</p>
    </section>

    <hr style="border:none;border-top:1px solid #E3E3E1;margin:16px 16px 0 16px;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
