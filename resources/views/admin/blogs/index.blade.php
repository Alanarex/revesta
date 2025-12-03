@extends('layouts.blogs')

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
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Titre du blog..." value="{{ $currentSearch ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="author" class="form-label">Filtrer par auteur</label>
                            <select class="form-select" id="author" name="author">
                                <option value="">Tous les auteurs</option>
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}"
                                        {{ $currentAuthor == $author->id ? 'selected' : '' }}>
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

    <!-- Bulk Actions and Select Buttons Row -->
    <div class="row mb-3">
        <div class="col-auto d-flex align-items-center">
            <div class="d-flex justify-content-start gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllVisibleBtn">
                    <i class="fa fa-check-square"></i> Tout sélectionner
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllVisibleBtn" style="display: none;">
                    <i class="fa fa-square"></i> Tout désélectionner
                </button>
            </div>
        </div>
        <div class="col d-flex align-items-center">
            <!-- Bulk Actions Bar -->
            <div id="bulkActionsBar" class="w-100" style="opacity: 0; pointer-events: none; transition: opacity 0.3s ease;">
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
                                <button type="button" class="btn btn-sm btn-success me-2 bulk-action-btn" id="bulkApproveBtn">
                                    <i class="fa fa-check"></i> Approuver la sélection
                                </button>
                                <button type="button" class="btn btn-sm btn-danger me-2 bulk-action-btn" id="bulkRejectBtn">
                                    <i class="fa fa-times"></i> Rejeter la sélection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div id="blogs-container">
                @include('admin.blogs.partials.blogs-list', ['blogs' => $blogs])
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
                        <textarea class="form-control" id="bulkRejectReason" rows="3"
                            placeholder="Expliquez pourquoi ces blogs sont rejetés..."></textarea>
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
                        <textarea class="form-control" id="singleRejectReason" rows="3"
                            placeholder="Expliquez pourquoi ce blog est rejeté..."></textarea>
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
    @vite('resources/js/admin/blogs/app.js')
@endpush
