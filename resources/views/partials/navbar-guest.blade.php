<!-- Main Header -->
<nav class="app-header navbar navbar-expand">
    <div class="container-fluid">
        <!-- Start Navbar Links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="{{ route('blogs.index') }}">
                    <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Logo" style="height: 35px; margin-right: 10px;">
                    <span class="fw-light" style="font-family: 'Neulis Cursive', cursive; font-size: 2.5rem;">
                        <span class="text-primary">re</span><span class="text-secondary">vesta</span>
                    </span>
                </a>
            </li>
        </ul>

        <!-- End Navbar Links -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="{{ route('login') }}" class="nav-link btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            </li>
        </ul>
    </div>
</nav>
