@extends('layouts.blogs')

@section('content')
    @include('admin.blogs.form', [
        'blog' => $blog,
        'title' => 'Modifier le blog',
        'action' => route('admin.blogs.update', $blog),
        'method' => 'PUT',
    ])
@endsection
