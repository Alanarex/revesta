<div class="footer" style="background-color: #f9f9f9; border-top: 1px solid #E3E3E1; padding: 20px 30px; font-size: 12px; color: #666666; text-align: center; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
    <p style="margin: 0 0 10px 0;">
        Cet email a ete envoye a l'adresse email associee a votre compte {{ $appName }}.
    </p>

    <div class="contact-info" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #E3E3E1; font-size: 13px; color: #666666;">
        <strong style="display: block; margin-bottom: 5px; color: #0b1c13;">Besoin d'aide ?</strong>
        Contactez notre equipe support : <a href="mailto:{{ $contactEmail }}" style="color: #a4c520; text-decoration: none;">{{ $contactEmail }}</a>
    </div>

    <div class="footer-links" style="margin-top: 10px;">
        <a href="{{ $appUrl }}" style="color: #a4c520; text-decoration: none; margin: 0 5px;">Site Web</a>
        @if (!empty($unsubscribeUrl))
            <a href="{{ $unsubscribeUrl }}" style="color: #a4c520; text-decoration: none; margin: 0 5px;">Se desabonner</a>
        @endif
    </div>

    <p style="margin-top: 15px; font-size: 11px; color: #666666;">
        © {{ now()->year }} {{ $appName }}. Tous droits reserves.
    </p>
</div>
