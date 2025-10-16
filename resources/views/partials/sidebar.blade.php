@php
    $sidebarItems = [
        [
            'title' => 'Accueil',
            'route' => route('dashboard'),
            'icon' => 'fa-home',
            'active' => request()->routeIs('dashboard'),
            'enabled' => true,
        ],
        [
            'title' => 'Simulations',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-sliders-h',
            'active' => request()->routeIs('simulations.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Support',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-life-ring',
            'active' => request()->routeIs('support.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Utilisateurs',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-users',
            'active' => request()->routeIs('users.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Adresses',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-map-marker-alt',
            'active' => request()->routeIs('addresses.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Annonces',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-bullhorn',
            'active' => request()->routeIs('ads.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Tranches fiscales',
            'route' => route('dashboard'), //à changer
            'icon' => 'fa-coins',
            'active' => request()->routeIs('fiscal.*'),
            'enabled' => true,
        ],
    ];
@endphp

<div class="bg-light text-white p-3 vh-100 position-fixed px-3" style="width: 250px; top: 0; left: 0;">
    <a href="{{ route('dashboard') }}">
        <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo"
            class="img-fluid sidebar-logo mb-4 ">
    </a>
    <ul class="nav nav-pills flex-column mt-5">
        @foreach ($sidebarItems as $item)
            @if ($item['enabled'])
                <li class="nav-item">
                    <a href="{{ $item['route'] }}"
                        class="nav-link text-dark {{ !empty($item['active']) && $item['active'] ? 'active' : '' }}"
                        @if (!empty($item['active']) && $item['active']) style="background-color: #9CC41B;" @endif>
                        <i class="fa {{ $item['icon'] }} me-2"></i>{{ $item['title'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
