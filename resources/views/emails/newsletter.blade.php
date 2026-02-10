<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite('resources/scss/emails/newsletter.scss')
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Newsletter</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 class="newsletter-title">{{ $title }}</h2>
            <div class="newsletter-body">
                {!! $content !!}
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer">
            <p>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
            <p>
                <a href="{{ $unsubscribeUrl ?? '#' }}">Unsubscribe from this newsletter</a>
            </p>
        </div>
    </div>
</body>
</html>
