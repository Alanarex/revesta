@props([
    'title' => '',
    'createRoute' => null,
    'createIcon' => 'bi-plus-circle-fill',
    'createLabel' => 'Ajouter',
    'tableId' => 'datatable',
    'bodyId' => 'datatableBody',
    'columns' => [],
    'totalCount' => null,
])

@push('styles')
    @vite('resources/scss/components/datatable.scss')
@endpush

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center admin-datatable-header p-2">
        <div class="col-md-6 h-100">
            <h4 class="admin-dt-title">{{ $title }}</h4>
        </div>
        <div class="col-md-6 h-100">
            <div class="admin-dt-search-row">
                <x-inputs.text-input name="searchInput" icon="bi bi-search" clearBtn clearBtnId="clearSearch"
                    placeholder="Rechercher..." class="m-0" />
                @if ($createRoute)
                    <a href="{{ $createRoute }}" class="btn btn-primary btn-md mb-4"><i
                            class="bi {!! $createIcon !!}"></i> {{ $createLabel }}</a>
                @endif
            </div>
        </div>
    </div>

    <!-- DataTable (minimal, borderless, modern) -->
    <div class="row">
        <div class="col-12 p-0">
            <div class="table-container table-responsive">
                <table class="table table-hover table-borderless table-sm table-admin align-middle shadow"
                    id="{{ $tableId }}">
                    <thead>
                        <tr>
                            @foreach ($columns as $col)
                                <th class="text-muted small" style="{{ $col['width'] ?? '' }}"
                                    @if (!empty($col['dataSort'])) data-sort="{{ $col['dataSort'] }}" @endif>
                                    {{ $col['label'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="{{ $bodyId }}">
                        <tr>
                            <td colspan="{{ count($columns) }}" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer: pagination info left, pagination right -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="dt-pagination-info" id="paginationInfo">Pages: <span id="totalPages">0</span></div>
                <nav aria-label="Pagination">
                    <ul class="pagination dt-pagination-modern mb-0" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>


@pushOnce('styles')
    @vite('resources/scss/components/datatable.scss')
@endPushOnce

@pushOnce('scripts')
    @vite('resources/js/components/datatable.js')
@endPushOnce
