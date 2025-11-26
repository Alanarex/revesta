<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">
    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    @include('partials.styles')
</head>

<body class="bg-light" data-is-admin="false" data-user-initials="">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top m-0">
        <div class="container">
            <a class="navbar-brand" href="{{ route('blogs.index') }}">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo Revesta" height="40">
            </a>
            <div class="ms-auto">
                <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5 pt-5">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.scripts')

</body>

</html>
