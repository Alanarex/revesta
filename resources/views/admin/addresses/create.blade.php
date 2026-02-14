@extends('admin.addresses.layouts')

@section('content')
    @include('admin.addresses.form', [
        'address' => new App\Models\Address(),
        'title' => 'Créer une nouvelle adresse',
        'action' => route('admin.addresses.store'),
        'method' => 'POST',
    ])
@endsection
