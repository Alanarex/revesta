@extends('layouts.app')

@section('content')
    <div class="container">
        <x-alerts.session />
        
        @yield('content')
    </div>

    @include('admin.newsletters.partials.styles')
@endsection
