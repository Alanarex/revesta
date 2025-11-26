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
            'title' => 'Blogs',
            'route' => route('blogs.index'),
            'icon' => 'fa-newspaper',
            'active' => request()->routeIs('blogs.*') && !request()->routeIs('admin.blogs.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Gérer les blogs',
            'route' => route('admin.blogs.index'),
            'icon' => 'fa-tasks',
            'active' => request()->routeIs('admin.blogs.*'),
            'enabled' => Gate::allows('manage', App\Models\Blog::class),
        ],
        // [
        //     'title' => 'Statistiques',
        //     'route' => route('statistics'),
        //     'icon' => 'fa-chart-bar',
        //     'active' => request()->routeIs('statistics'),
        // ],
    ];
@endphp

<nav id="sidebar" class="bg-light border-end vh-100 position-fixed top-0 start-0" style="z-index: 1030;">
    <div class="d-flex flex-column h-100 p-3 pt-0">
        <div class="sidebar-logo-container mb-4 py-3">
            <a href="{{ route('dashboard') }}" class="d-block">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="sidebar-logo-large">
                <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Logo" class="sidebar-logo-small">
            </a>
        </div>

        <ul class="nav nav-pills flex-column mt-3">
            @foreach ($sidebarItems as $item)
                @if ($item['enabled'])
                    <li class="nav-item">
                        <a href="{{ $item['route'] }}"
                            class="nav-link text-dark d-flex align-items-center {{ !empty($item['active']) && $item['active'] ? 'active' : '' }}">
                            <i class="fa {{ $item['icon'] }} sidebar-icon"></i>
                            <span class="sidebar-text ms-2">{{ $item['title'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>
