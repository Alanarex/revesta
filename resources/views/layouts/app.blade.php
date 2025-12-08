<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">

    <title>{{ $title ?? false ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @include('partials.styles')

</head>

<body
    class="hold-transition @auth sidebar-mini @endauth sidebar-collapse layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        @auth
            @include('partials.navbar')
            @include('partials.sidebar')
        @else
            @include('partials.navbar-guest')
        @endauth

        <div class="content-wrapper">
            <main class="container-fluid p-3" id="main-content">
                @yield('content')
            </main>
        </div>

        @include('partials.footer')
    </div>

    @include('partials.scripts')
</body>

</html>
