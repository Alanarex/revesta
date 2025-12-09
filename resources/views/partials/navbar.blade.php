<!-- Navbar -->
<nav class="main-header navbar navbar-expand">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ms-auto">
        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                aria-expanded="false">
                <i class="far fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-header py-0">{{ auth()->user()->fullname }}</span></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user me-2"></i> Profil
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
