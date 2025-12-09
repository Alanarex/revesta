@php
    $sidebarItems = [
        [
            'title' => 'Accueil',
            'route' => route('dashboard'),
            'icon' => 'bi-house',
            'active' => request()->routeIs('dashboard'),
            'enabled' => true,
        ],
        [
            'title' => 'Blogs',
            'route' => route('blogs.index'),
            'icon' => 'bi-newspaper',
            'active' => request()->routeIs('blogs.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Gérer les blogs',
            'route' => route('admin.blogs.index'),
            'icon' => 'bi-clipboard-check',
            'active' => request()->routeIs('admin.blogs.index'),
            'enabled' => Gate::allows('manage', App\Models\Blog::class),
        ],
    ];
@endphp

<!-- Main Sidebar -->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
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
                                <i class="nav-icon bi {{ $item['icon'] }}"></i>
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
