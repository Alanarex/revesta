@extends('layouts.app')

@section('content')
    <div class="container">

        @yield('content')
    </div>

    @include('admin.newsletters.partials.styles')
@endsection
