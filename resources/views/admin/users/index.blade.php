@extends('admin.addresses.layouts')

@push('meta')
    <meta name="users-list-url" content="{{ route('admin.users.list') }}">
@endpush

@section('content')
    @php
        $cols = [
            ['label' => 'ID', 'width' => 'width:5%', 'dataSort' => 'id'],
            ['label' => 'Nom', 'width' => 'width:25%', 'dataSort' => 'full_name'],
            ['label' => 'Email', 'width' => 'width:25%', 'dataSort' => 'email'],
            ['label' => 'Téléphone', 'width' => 'width:12%', 'dataSort' => 'phone'],
            ['label' => 'Rôle', 'width' => 'width:10%', 'dataSort' => 'role'],
            ['label' => 'Localisation', 'width' => 'width:15%', 'dataSort' => 'city'],
            ['label' => 'Actions', 'width' => 'width:8%'],
        ];
    @endphp

    <x-layout.datatable :title="'Gestion des utilisateurs'" :createRoute="route('admin.users.create')" create-icon="bi-person-add" table-id="usersTable" body-id="tableBody" :columns="$cols" :total-count="$totalCount" />
@endsection

@push('scripts')
    @vite('resources/js/admin/users/datatable.js')
@endpush
