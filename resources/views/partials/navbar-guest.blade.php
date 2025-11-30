<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="{{ config('app.name') }}" height="40">
        </a>

        <!-- Sign In Button -->
        <div class="d-flex">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="fa fa-sign-in-alt me-2"></i>Se connecter
            </a>
        </div>
    </div>
</nav>
