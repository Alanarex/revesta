@extends('admin.addresses.layouts')

@push('meta')
    <meta name="addresses-list-url" content="{{ route('admin.addresses.list') }}">
@endpush

@section('content')
    @php
        $cols = [
            ['label' => 'ID', 'width' => 'width:5%', 'dataSort' => 'id'],
            ['label' => 'Libellé', 'width' => 'width:20%', 'dataSort' => 'label'],
            ['label' => 'Adresse', 'width' => 'width:25%', 'dataSort' => 'street'],
            ['label' => 'Code Postal', 'width' => 'width:10%', 'dataSort' => 'postal_code'],
            ['label' => 'Ville', 'width' => 'width:15%', 'dataSort' => 'city'],
            ['label' => 'Département', 'width' => 'width:10%', 'dataSort' => 'departement'],
            ['label' => 'Actions', 'width' => 'width:15%'],
        ];
    @endphp

    <x-admin.datatable :title="'Gestion des adresses'" :createRoute="route('admin.addresses.create')" table-id="addressesTable" body-id="addressesTableBody" :columns="$cols" :total-count="$totalCount" />
@endsection

@push('scripts')
    @vite('resources/js/admin/addresses/datatable.js')
@endpush


