@extends('layouts.blogs')

@section('content')
	@include('admin.blogs.form', ['blog' => $blog])
@endsection
