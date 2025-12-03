@extends('layouts.blogs')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="flex-grow-1" style="max-width: 500px;">
                    <form action="{{ route('blogs.index') }}" method="GET" class="d-flex">
                        <input type="text" id="search-input" name="search" class="form-control"
                            placeholder="Rechercher un blog..." value="{{ $search ?? '' }}">
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
                <div>
                    @auth
                        <a href="{{ route('blogs.create') }}" class="btn btn-success">
                            <i class="fa fa-pen"></i> Écrire un blog
                        </a>
                    @else
                        <button class="btn btn-success" data-auth-required>
                            <i class="fa fa-pen"></i> Écrire un blog
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('blogs.partials.blogs-list', ['blogs' => $blogs])
        </div>
    </div>
@endsection
