@php
    $layout = Auth::check() ? 'layouts.blog' : 'layouts.blog-guest';
@endphp

@extends($layout)

@section('content')
    @yield('blogs-content')
@endsection

@include('blogs.partials.scripts')
