<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        {!! file_get_contents(base_path('resources/css/emails.css')) !!}
    </style>
</head>
<body>
    <div class="email-container">
        @yield('content')
    </div>
</body>
</html>
