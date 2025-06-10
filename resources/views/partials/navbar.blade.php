<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="margin-left: 250px;">
    <div class="container-fluid">
        {{-- <a class="navbar-brand text-dark" href="#">Panneau d'administration</a> --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#"><i class="fa fa-user"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link text-dark border-0 bg-transparent" type="submit">
                            <i class="fa fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
