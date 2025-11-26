@push('scripts')
    @vite('resources/js/blogs/app.js')

    @if (auth()->check() && Auth::user()->isAdmin())
        @vite('resources/js/blogs/admin.js')
    @endif
@endpush
