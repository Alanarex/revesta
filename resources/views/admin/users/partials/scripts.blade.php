@push('scripts')
    @vite('resources/js/admin/users/app.js')
    @vite('resources/js/admin/users/show.js')

    @guest
        @vite('resources/js/blogs/app.js')
    @endguest
@endpush
