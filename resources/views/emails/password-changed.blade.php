@extends('layouts.email', ['title' => 'Confirmation de changement de mot de passe - ' . $appName])

@section('content')
    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td align="center" style="font-size:15px;font-weight:600;padding-bottom:4px;">Bonjour {{ $userName }} 👋</td></tr>
            <tr><td align="center" style="font-size:32px;font-weight:800;line-height:1.2;color:#0b1c13;padding-bottom:4px;">✅ Changement de mot de passe confirmé</td></tr>
            <tr><td align="center" style="font-size:14px;font-weight:500;line-height:1.4;">Votre compte est désormais protégé avec votre nouveau mot de passe.</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <div style="border:1px solid #E3E3E1;border-radius:12px;background:#f9f9f9;padding:12px;margin-bottom:14px;font-size:14px;color:#0b1c13;line-height:1.5;">
            <strong>📝 Informations sur le changement :</strong><br>
            <strong>Adresse email :</strong> {{ $userEmail }}<br>
            <strong>Date et heure :</strong> {{ now()->format('d/m/Y à H:i') }}
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:14px;background:#fff;">
            <strong style="color:#0b1c13;">⚠️ Attention à la sécurité :</strong>
            <ul style="margin:10px 0 0 18px;padding:0;color:#666;font-size:14px;line-height:1.5;">
                <li>Jamais nous ne vous demanderons votre mot de passe par email</li>
                <li>Assurez-vous que votre mot de passe est fort et unique</li>
                <li>Ne partagez jamais votre mot de passe</li>
            </ul>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;background:#f9f9f9;font-size:14px;color:#222;line-height:1.5;">
            <strong>❓ Vous n'avez pas effectué ce changement ?</strong>
            <p style="margin:8px 0 12px 0;">Réinitialisez immédiatement votre mot de passe si ce changement ne vient pas de vous.</p>
            <a href="{{ route('password.request') }}" style="display:inline-block;background:#a4c520;color:#0b1c13;text-decoration:none;font-weight:700;padding:11px 18px;border-radius:10px;">Réinitialiser mon mot de passe</a>
            <p style="margin:12px 0 0 0;">Support : <a href="mailto:{{ $contactEmail }}" style="color:#a4c520;text-decoration:none;font-weight:700;">{{ $contactEmail }}</a></p>
        </div>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 0 16px;">
        <div style="color:#0b1c13;font-size:20px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:6px;margin-bottom:10px;">✨ Pour votre sécurité</div>
        <ul style="margin:0;padding-left:18px;color:#666;font-size:14px;line-height:1.5;">
            <li>Déconnectez-vous des appareils non utilisés</li>
            <li>Utilisez un mot de passe fort et unique</li>
            <li>Activez l'authentification à deux facteurs si disponible</li>
        </ul>
    </section>

    <hr style="border:none;border-top:1px solid #E3E3E1;margin:16px 16px 0 16px;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
