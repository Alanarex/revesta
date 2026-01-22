<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')

    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">

    <title>{{ $title ?? false ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @include('partials.styles')

</head>

<body class="@auth sidebar-expand-lg sidebar-mini bg-body-tertiary @else guest-layout bg-body-tertiary @endauth">
    <div class="app-wrapper">
        @auth
            @include('partials.navbar')
            @include('partials.sidebar')
        @else
            @include('partials.navbar-guest')
        @endauth

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>
        @include('partials.footer')
    </div>

    @include('partials.scripts')
</body>

</html>
