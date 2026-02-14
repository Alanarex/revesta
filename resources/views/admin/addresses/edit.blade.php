@extends('admin.addresses.layouts')

@section('content')
    @include('admin.addresses.form', [
        'address' => $address,
        'title' => 'Modifier l\'adresse',
        'action' => route('admin.addresses.update', $address),
        'method' => 'PUT',
    ])
@endsection
