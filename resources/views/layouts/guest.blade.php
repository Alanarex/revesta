<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">

    <title>@yield('title', config('app.name'))</title>

    @include('partials.styles')
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <main class="container py-5" id="main-content">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @yield('content')
            </div>
        </div>
    </main>

    @include('partials.scripts')
</body>

</html>
