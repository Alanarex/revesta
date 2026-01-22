@extends('admin.addresses.layouts')

@push('meta')
    <meta name="addresses-list-url" content="{{ route('admin.addresses.list') }}">
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h3>Gestion des addresses</h3>
        </div>
    </div>

    <div class="container-fluid py-4">
        <!-- Header with Create Button and Search -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                            <small class="text-muted">Total: <span id="totalCount">{{ $totalCount }}</span> adresse(s)</small>
                            <div>
                                <a href="{{ route('admin.addresses.create') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Ajouter une adresse
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par libellé, rue, ville...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="addressesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th style="width: 20%">Libellé</th>
                                        <th style="width: 25%">Adresse</th>
                                        <th style="width: 10%">Code Postal</th>
                                        <th style="width: 15%">Ville</th>
                                        <th style="width: 10%">Département</th>
                                        <th style="width: 15%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="addressesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Affichage de <span id="showingFrom">0</span> à <span id="showingTo">0</span> sur <span id="showingTotal">0</span> résultats
                                </small>
                            </div>
                            <nav aria-label="Pagination des adresses">
                                <ul class="pagination mb-0" id="pagination">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin/addresses/datatable.js')
@endpush


