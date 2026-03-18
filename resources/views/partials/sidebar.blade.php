@php
    $sidebarItems = [
        [
            'title' => 'Accueil',
            'route' => route('dashboard.index'),
            'icon' => 'fa-house',
            'active' => request()->routeIs('dashboard.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Blogs',
            'route' => route('admin.blogs.index'),
            'icon' => 'fa-newspaper',
            'active' => request()->routeIs('admin.blogs.*'),
            'enabled' => Gate::allows('manage', App\Models\Blog::class),
        ],
        [
            'title' => 'Adresses',
            'route' => route('admin.addresses.index'),
            'icon' => 'fa-location-dot',
            'active' => request()->routeIs('admin.addresses.*'),
            'enabled' => Gate::allows('manage', App\Models\Address::class),
        ],
        [
            'title' => 'Utilisateurs',
            'route' => route('admin.users.index'),
            'icon' => 'fa-users',
            'active' => request()->routeIs('admin.users.*'),
            'enabled' => Gate::allows('manage', App\Models\User::class),
        ],
        [
            'title' => 'Simulations',
            'route' => route('simulations.index'),
            'icon' => 'fa-chart-column',
            'active' => request()->routeIs('simulations.*'),
            'enabled' => auth()->check(),
        ],
        [
            'title' => 'API Docs',
            'route' => route(name: 'scribe'),
            'icon' => 'fa-file-code',
            'active' => request()->is('scribe.*'),
            'enabled' => auth()->check() && auth()->user()?->isAdmin(),
        ],
        [
            'title' => 'Newsletters',
            'route' => route('admin.newsletters.index'),
            'icon' => 'fa-newspaper',
            'active' => request()->routeIs('admin.newsletters.*'),
            'enabled' => Gate::allows('manage', App\Models\NewsletterCampaign::class),
        ],
        [
            'title' => 'Abonnés Newsletter',
            'route' => route('admin.newsletter-subscribers.index'),
            'icon' => 'fa-user-check',
            'active' => request()->routeIs('admin.newsletter-subscribers.*'),
            'enabled' => Gate::allows('manage', App\Models\Newsletter::class),
        ],
    ];
@endphp

<!-- Main Sidebar -->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="{{ route('dashboard.index') }}" class="brand-link">
            <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Logo" class="brand-image opacity-75">
            <span class="brand-text fw-light">
                <span class="text-primary">re</span><span class="text-secondary">vesta</span>
            </span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-lte-toggle="treeview" role="menu"
                data-accordion="false">
                @foreach ($sidebarItems as $item)
                    @if ($item['enabled'])
                        <li class="nav-item my-1">
                            <a href="{{ $item['route'] }}"
                                class="nav-link {{ !empty($item['active']) && $item['active'] ? 'active' : '' }}">
                                <i class="nav-icon fa {{ $item['icon'] }}"></i>
                                <p>{{ $item['title'] }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
    <!-- /.sidebar-wrapper -->
</aside>
