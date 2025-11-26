@extends('layouts.blog')

@section('content')
	@include('blogs.form', ['blog' => $blog])
@endsection

