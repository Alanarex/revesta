<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="{{ route('blogs.index') }}" style="text-decoration: none;">
                <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Logo" style="height: 30px; margin-right: 8px;">
                <span class="brand-text" style="font-family: 'Neulis Cursive', cursive; font-weight: 400; font-size: 1.8rem;">
                    <span class="text-primary">re</span><span class="text-secondary">vesta</span>
                </span>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
