@vite('resources/scss/app.scss')

<style>
    /* Disable transitions during page load to prevent flashing */
    html.page-loading * {
        transition: none !important;
    }
</style>

@stack('styles')
