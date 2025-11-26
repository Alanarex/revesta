@extends('blogs.layout')

@section('blogs-content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="flex-grow-1" style="max-width: 500px;">
                    <form action="{{ route('blogs.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un blog..."
                            value="{{ $search ?? '' }}">
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
            @forelse($blogs as $blog)
                @include('blogs.partials.card', ['blog' => $blog])
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun blog trouvé.</p>
                    </div>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $blogs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
