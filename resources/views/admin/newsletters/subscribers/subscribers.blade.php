@extends('admin.newsletters.layouts')

@push('meta')
    <meta name="newsletter-subscribers-list-url" content="{{ route('admin.newsletter-subscribers.list') }}">
@endpush

@section('content')
    <!-- Breadcrumbs -->
    <div class="mb-4">
        <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
    </div>

    @php
        $cols = [
            ['label' => 'Email', 'width' => 'width:35%', 'dataSort' => 'email'],
            ['label' => 'Statut', 'width' => 'width:15%', 'dataSort' => 'verified_at'],
            ['label' => 'Inscrit le', 'width' => 'width:15%', 'dataSort' => 'subscribed_at'],
            ['label' => 'Vérifié le', 'width' => 'width:15%', 'dataSort' => 'verified_at'],
            ['label' => 'Actions', 'width' => 'width:20%'],
        ];
    @endphp

    <x-layout.datatable :title="'Abonnés à la newsletter'" table-id="subscribersTable" body-id="tableBody" :columns="$cols"
        :total-count="$totalCount" />
@endsection

@push('scripts')
    @vite('resources/js/admin/newsletters/subscribers/datatable.js')
@endpush
