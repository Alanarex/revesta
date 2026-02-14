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
                    <div class="pb-4 border-bottom">
                        <x-inputs.text-input
                            name="filterSearch"
                            label="Rechercher"
                            placeholder="Titre, auteur, contenu..."
                            value="{{ $currentSearch ?? '' }}"
                            muted="Recherche dans le titre, la description et le contenu"
                        />
                    </div>

                    @php
                        $statusOptions = [
                            'draft' => 'Brouillon',
                            'pending' => 'En attente',
                            'published' => 'Publié',
                            'rejected' => 'Rejeté',
                        ];
                        $authorsOptions = collect($authors)->filter()->mapWithKeys(function ($author) {
                            return [$author->id => "{$author->first_name} {$author->last_name}"];
                        })->toArray();
                    @endphp

                    <div class="pb-4 border-bottom">
                        <x-inputs.select-input
                            name="filterStatus"
                            label="Statut"
                            placeholder="Tous les statuts"
                            value="{{ $currentStatus ?? '' }}"
                            :options="$statusOptions"
                        />
                    </div>

                    <div class="pb-4 border-bottom">
                        <x-inputs.select-input
                            name="filterAuthor"
                            label="Auteur"
                            placeholder="Tous les auteurs"
                            value="{{ $currentAuthor ?? '' }}"
                            :options="$authorsOptions"
                        />
                    </div>

                    <div class="pb-4 border-bottom">
                        <x-inputs.date-input
                            name="filterDateFrom"
                            label="Date de début"
                            value="{{ $currentDateFrom ?? '' }}"
                        />
                    </div>

                    <div>
                        <x-inputs.date-input
                            name="filterDateTo"
                            label="Date de fin"
                            value="{{ $currentDateTo ?? '' }}"
                        />
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
