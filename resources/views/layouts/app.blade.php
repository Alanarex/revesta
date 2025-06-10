<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/logo.svg') }}">

    <title>@yield('title', config('app.name'))</title>

    @include('partials.styles')

</head>

<body class="d-flex flex-column min-vh-100">
    <div class="d-flex" id="layout-wrapper">
        @include('partials.sidebar')

        <div id="main-content-wrapper" class="flex-grow-1">
            @include('partials.navbar')

            <main class="container-fluid pt-3" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.footer')
</body>

@include('partials.scripts')

</html>
