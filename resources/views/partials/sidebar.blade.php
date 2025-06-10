@php
    $sidebarItems = [
        [
            'title' => 'Accueil',
            'route' => route('dashboard'),
            'icon' => 'fa-home',
            'active' => request()->routeIs('dashboard'),
        ],
        // [
        //     'title' => 'Statistiques',
        //     'route' => route('statistics'),
        //     'icon' => 'fa-chart-bar',
        //     'active' => request()->routeIs('statistics'),
        // ],
    ];
@endphp

<div class="bg-light text-white p-3 vh-100 position-fixed px-3" style="width: 250px; top: 0; left: 0;">
    <a href="{{ route('dashboard') }}">
        <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo"
            class="img-fluid sidebar-logo mb-4 ">
    </a>
    <ul class="nav nav-pills flex-column mt-5">
        @foreach ($sidebarItems as $item)
            <li class="nav-item">
                <a href="{{ $item['route'] }}"
                    class="nav-link text-dark {{ !empty($item['active']) && $item['active'] ? 'active' : '' }}">
                    <i class="fa {{ $item['icon'] }} me-2"></i>{{ $item['title'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
