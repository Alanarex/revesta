@extends('layouts.email', ['title' => 'Vérification email - ' . $appName])

@section('content')
    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td align="center" style="font-size:15px;font-weight:600;padding-bottom:4px;">Bonjour {{ $userName }} 👋</td></tr>
            <tr><td align="center" style="font-size:32px;font-weight:800;line-height:1.2;color:#0b1c13;padding-bottom:4px;">✉️ Vérification de votre email</td></tr>
            <tr><td align="center" style="font-size:14px;font-weight:500;line-height:1.4;">Activez votre compte en validant votre adresse email.</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 16px 16px;">
        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.5;color:#222;">
            Merci pour votre inscription. Pour activer votre compte, vérifiez votre adresse email
            et définissez votre mot de passe.
        </p>

        <div style="border:1px solid #E3E3E1;border-radius:12px;background:#f9f9f9;padding:12px;margin-bottom:14px;font-size:14px;color:#0b1c13;">
            <strong>⏱️ Validité du lien :</strong> Ce lien expire dans <strong>24 heures</strong>.
        </div>

        <div style="text-align:center;margin:0 0 14px 0;">
            <a href="{{ $verificationUrl }}" style="display:inline-block;background:#a4c520;color:#0b1c13;text-decoration:none;font-weight:700;padding:11px 18px;border-radius:10px;">
                Vérifier mon email et définir mon mot de passe
            </a>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;margin-bottom:14px;font-size:13px;word-break:break-all;">
            <strong>Ou utilisez ce lien :</strong><br>
            <a href="{{ $verificationUrl }}" style="color:#a4c520;text-decoration:underline;">{{ $verificationUrl }}</a>
        </div>

        <div style="border:1px solid #E3E3E1;border-radius:12px;padding:12px;background:#f9f9f9;font-size:14px;color:#222;line-height:1.5;">
            <strong>❓ Vous n'avez pas créé ce compte ?</strong>
            <p style="margin:8px 0 0 0;">
                Contactez notre équipe support à
                <a href="mailto:{{ $contactEmail }}" style="color:#a4c520;text-decoration:none;font-weight:700;">{{ $contactEmail }}</a>
                pour sécuriser votre compte.
            </p>
        </div>
    </section>

    <hr style="border:none;border-top:1px solid #E3E3E1;margin:16px 16px 0 16px;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
