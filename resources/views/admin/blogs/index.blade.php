@extends('layouts.blogs')

@section('content')
    <div class="container-xl">
        <div class="row mb-4">
            <div class="col-9 d-flex justify-content-between align-items-center">
                <h3>Gestion des blogs</h3>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Créer un blog
                </a>
            </div>
        </div>

        <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 20px;">
                <h5 class="mb-3">Filtres</h5>

                <!-- Selected Filters Display -->
                <div id="selectedFiltersContainer" style="display: none;" class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Filtres appliqués:</small>
                        <button type="button" class="btn btn-link btn-sm text-danger p-0" id="resetFiltersBtn">
                            <i class="fa fa-times"></i> Réinitialiser
                        </button>
                    </div>
                    <div id="selectedFiltersTags" class="d-flex flex-wrap gap-2"></div>
                </div>

                <form id="filterForm">
                    <!-- Search Input -->
                    <div class="mb-4 pb-4 border-bottom">
                        <label for="filterSearch" class="form-label">Rechercher</label>
                        <input type="text" class="form-control" id="filterSearch" name="search"
                            placeholder="Titre, auteur, contenu..." value="{{ $currentSearch ?? '' }}">
                        <small class="text-muted">Recherche dans le titre, la description et le contenu</small>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4 pb-4 border-bottom">
                        <label for="filterStatus" class="form-label">Statut</label>
                        <select class="form-select" id="filterStatus" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="draft" {{ $currentStatus == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="published" {{ $currentStatus == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>

                    <!-- Author Filter -->
                    <div class="mb-4 pb-4 border-bottom">
                        <label for="filterAuthor" class="form-label">Auteur</label>
                        <select class="form-select" id="filterAuthor" name="author">
                            <option value="">Tous les auteurs</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ $currentAuthor == $author->id ? 'selected' : '' }}>
                                    {{ $author->first_name }} {{ $author->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="mb-4 pb-4 border-bottom">
                        <label for="filterDateFrom" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="filterDateFrom" name="date_from"
                            value="{{ $currentDateFrom ?? '' }}">
                    </div>

                    <div>
                        <label for="filterDateTo" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="filterDateTo" name="date_to"
                            value="{{ $currentDateTo ?? '' }}">
                    </div>
                </form>
            </div>
        </div>

        <!-- Blogs List -->
        <div class="col-lg-6">
            <div id="blogs-container" class="ps-4 border-start">
                @include('admin.blogs.partials.blogs-list', ['blogs' => $blogs])
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin/blogs/app.js')
@endpush
