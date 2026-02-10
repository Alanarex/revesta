@extends('admin.newsletters.layouts')

@push('meta')
    <meta name="newsletters-list-url" content="{{ route('admin.newsletters.list') }}">
@endpush

@section('content')
    @php
        $cols = [
            ['label' => 'Titre', 'width' => 'width:30%', 'dataSort' => 'title'],
            ['label' => 'Statut', 'width' => 'width:15%', 'dataSort' => 'status'],
            ['label' => 'Créée', 'width' => 'width:15%', 'dataSort' => 'created_at'],
            ['label' => 'Destinataires', 'width' => 'width:12%', 'dataSort' => 'sent_count'],
            ['label' => 'Actions', 'width' => 'width:28%'],
        ];
    @endphp

    <x-layout.datatable :title="'Gestion des newsletters'" :createRoute="route('admin.newsletters.create')" create-icon="fa-envelope-paper" table-id="newslettersTable"
        body-id="tableBody" :columns="$cols" :total-count="$totalCount" />
@endsection

@push('scripts')
    @vite('resources/js/admin/newsletters/campaigns/datatable.js')
    @vite('resources/js/admin/newsletters/campaigns/show-handler.js')
@endpush
