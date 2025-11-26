@push('scripts')
    @vite('resources/js/blogs/app.js')

    @if (request()->routeIs('blogs.show'))
        @vite('resources/js/blogs/scroll.js')
    @endif

    @auth
        @if (auth()->user()->isAdmin())
            @vite('resources/js/blogs/admin.js')
        @endif
    @endauth
@endpush
