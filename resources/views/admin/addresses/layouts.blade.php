@extends('layouts.app')

@section('content')
    @yield('content')

    @include('admin.addresses.partials.styles')
    @include('admin.addresses.partials.scripts')
@endsection
