<!DOCTYPE html>
<html lang="fr" class="page-loading">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">

    <title>{{ $title ?? false ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @include('partials.styles')

</head>

<body class="sidebar-collapsed">
    <div id="layout-wrapper">
        @auth
            @include('partials.sidebar')
        @endauth

        <div id="main-content-wrapper">
            @auth
                @include('partials.navbar')
            @else
                @include('partials.navbar-guest')
            @endauth

            <main class="container-fluid p-0 pt-3" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.footer')
</body>

@include('partials.scripts')

</html>
