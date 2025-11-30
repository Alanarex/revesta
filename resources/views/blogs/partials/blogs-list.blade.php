@forelse($blogs as $blog)
    @include('blogs.partials.card', ['blog' => $blog])
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun blog trouvé pour cette recherche.</p>
            <a href="{{ route('blogs.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa fa-times"></i> Effacer la recherche
            </a>
        </div>
    </div>
@endforelse

@if($blogs->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endif