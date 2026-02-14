@extends('layouts.blogs')

@section('content')
    @include('admin.blogs.form', [
        'blog' => new App\Models\Blog(),
        'title' => 'Créer un nouveau blog',
        'action' => route('admin.blogs.store'),
        'method' => 'POST',
    ])
@endsection
