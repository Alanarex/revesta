@extends('layouts.blog')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3>Blogs en attente d'approbation</h3>
    </div>
</div>

<!-- Search and Filter Form -->
<div class="row mb-4">
    <div class="col-12">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Rechercher par titre</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="search" 
                            name="search" 
                            placeholder="Titre du blog..."
                            value="{{ $currentSearch ?? '' }}"
                        >
                    </div>
                    <div class="col-md-4">
                        <label for="author" class="form-label">Filtrer par auteur</label>
                        <select class="form-select" id="author" name="author">
                            <option value="">Tous les auteurs</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ ($currentAuthor == $author->id) ? 'selected' : '' }}>
                                    {{ $author->first_name }} {{ $author->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fa fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="row mb-3" id="bulkActionsBar" style="display: none;">
    <div class="col-12">
        <div class="card bg-light shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <strong id="selectedCount">0</strong> blog(s) sélectionné(s)
                        <span id="totalBlogsCount" style="display: none;">
                            sur <strong>{{ $blogs->total() }}</strong> au total
                            <a href="#" id="selectAllBlogsLink" class="ms-2 text-primary">
                                <i class="fa fa-check-double"></i> Tout sélectionner ({{ $blogs->total() }} blogs)
                            </a>
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-2" id="bulkApproveBtn">
                            <i class="fa fa-check"></i> Approuver la sélection
                        </button>
                        <button type="button" class="btn btn-sm btn-danger me-2" id="bulkRejectBtn">
                            <i class="fa fa-times"></i> Rejeter la sélection
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelectionBtn">
                            <i class="fa fa-undo"></i> Désélectionner tout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select All Button -->
<div class="row mb-3">
    <div class="col-12">
        <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllVisibleBtn">
            <i class="fa fa-check-square"></i> Tout sélectionner sur cette page
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @forelse($blogs as $blog)
            <div class="card mb-3 shadow-sm blog-item" data-blog-id="{{ $blog->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-start flex-grow-1">
                            <div class="form-check me-3 mt-1">
                                <input 
                                    class="form-check-input blog-checkbox" 
                                    type="checkbox" 
                                    value="{{ $blog->id }}"
                                    id="blog-{{ $blog->id }}"
                                    style="border: 1px solid black;"
                                >
                            </div>
                            <div class="flex-grow-1">
                                <h5>{{ $blog->title }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($blog->short_description, 150) }}</p>
                                <small class="text-muted">
                                    Par {{ $blog->user->full_name }} • {{ $blog->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.blogs.show', $blog) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-eye"></i> Voir
                            </a>
                            <button type="button" class="btn btn-success btn-sm approve-single-btn" data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-check"></i> Approuver
                            </button>
                            <button type="button" class="btn btn-danger btn-sm reject-single-btn" data-blog-id="{{ $blog->id }}">
                                <i class="fa fa-times"></i> Rejeter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Aucun blog en attente d'approbation.</p>
                </div>
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $blogs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter les blogs sélectionnés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="bulkRejectReason" class="form-label">Raison du rejet (optionnel)</label>
                    <textarea 
                        class="form-control" 
                        id="bulkRejectReason" 
                        rows="3" 
                        placeholder="Expliquez pourquoi ces blogs sont rejetés..."
                    ></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmBulkRejectBtn">Confirmer le rejet</button>
            </div>
        </div>
    </div>
</div>

<!-- Single Reject Modal -->
<div class="modal fade" id="singleRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter le blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="singleRejectReason" class="form-label">Raison du rejet (optionnel)</label>
                    <textarea 
                        class="form-control" 
                        id="singleRejectReason" 
                        rows="3" 
                        placeholder="Expliquez pourquoi ce blog est rejeté..."
                    ></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmSingleRejectBtn">Confirmer le rejet</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/admin-blogs-index.js')
@endpush
