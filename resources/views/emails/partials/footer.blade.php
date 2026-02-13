<div class="footer">
    <p>
        Cet email a ete envoye a l'adresse email associee a votre compte {{ $appName }}.
    </p>

    <div class="contact-info">
        <strong>Besoin d'aide ?</strong>
        Contactez notre equipe support : <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
    </div>

    <div class="footer-links">
        <a href="{{ $appUrl }}">Site Web</a>
        @if (!empty($unsubscribeUrl))
            <a href="{{ $unsubscribeUrl }}">Se desabonner</a>
        @endif
    </div>

    <p style="margin-top: 15px; font-size: 11px; color: #bbb;">
        © {{ now()->year }} {{ $appName }}. Tous droits reserves.
    </p>
</div>
