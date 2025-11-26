@extends('layouts.blog')

@section('content')
	@include('blogs.form', ['blog' => $blog])
@endsection

@push('scripts')
	@vite('resources/js/blogs/editor-submit.js')
@endpush
