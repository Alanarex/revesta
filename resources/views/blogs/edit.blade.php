@extends('layouts.blogs')

@section('content')
	@include('blogs.form', ['blog' => $blog])
@endsection
