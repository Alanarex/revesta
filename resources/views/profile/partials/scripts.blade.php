@push('scripts')
    @vite('resources/js/profile/app.js')

    @guest
        @vite('resources/js/blogs/app.js')
    @endguest
@endpush
