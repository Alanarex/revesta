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
            'active' => request()->routeIs('blogs.*'),
            'enabled' => true,
        ],
        [
            'title' => 'Gérer les blogs',
            'route' => route('admin.blogs.index'),
            'icon' => 'fa-tasks',
            'active' => request()->routeIs('admin.blogs.index'),
            'enabled' => Gate::allows('manage', App\Models\Blog::class),
        ],
    ];
@endphp

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-2">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
        <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="Logo" class="brand-image">
        <span class="brand-text h-100">
            <span class="text-primary">re</span><span class="text-secondary">vesta</span>
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @foreach ($sidebarItems as $item)
                    @if ($item['enabled'])
                        <li class="nav-item">
                            <a href="{{ $item['route'] }}"
                                class="nav-link {{ !empty($item['active']) && $item['active'] ? 'active' : '' }}">
                                <i class="nav-icon fas {{ $item['icon'] }}"></i>
                                <p>{{ $item['title'] }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
