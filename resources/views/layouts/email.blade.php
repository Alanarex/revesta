<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:20px 10px;background:#fffcf1;color:#222;font-family:Montserrat,-apple-system,BlinkMacSystemFont,'Segoe UI','Roboto','Oxygen','Ubuntu','Cantarell','Fira Sans','Droid Sans','Helvetica Neue',sans-serif;">
    <div style="max-width:650px;margin:0 auto;background:#fffcf1;padding:14px 0;">
        @include('emails.partials.header', ['appUrl' => $appUrl ?? config('app.url')])
        @yield('content')
    </div>
</body>
</html>
