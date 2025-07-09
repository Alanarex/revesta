@push('scripts')
    <script>
        const modelAttributes = @json($models);
        const operatorSelect = @json($operators).map(op => `<option value="${op}">${op}</option>`).join('');
    </script>

    @vite('resources/js/conditions/app.js')
@endpush
