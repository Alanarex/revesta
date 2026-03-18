<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 20px 10px; background-color: #fffcf1; color: #222222; font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
    <div class="email-container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #E3E3E1; border-radius: 8px; overflow: hidden;">
        @yield('content')
    </div>
</body>
</html>
