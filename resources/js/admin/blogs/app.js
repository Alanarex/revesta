/**
 * Admin Blogs Application
 * Handles filtering and blog management with sidebar filters
 */

$(function () {
    // Initialize filter functionality
    new AdminBlogsFilters();
});

class AdminBlogsFilters {
    constructor() {
        this.isLoading = false;
        this.currentPage = 1;
        this.previousFilterData = this.getFilterData();
        this.init();
    }

    init() {
        this.setupFilterFields();
        this.setupResetButton();
        this.setupPagination();
        this.updateSelectedFiltersDisplay();
    }

    setupFilterFields() {
        // Setup debounce for search input
        let searchTimeout = null;
        $('#filterSearch').on('blur', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.hasFilterChanged()) {
                    this.applyFilters();
                }
            }, 300);
        });

        // Setup change events for select fields
        $('#filterStatus, #filterAuthor, #filterDateFrom, #filterDateTo').on('change', (e) => {
            if (this.hasFilterChanged()) {
                this.applyFilters();
            }
        });
    }

    setupResetButton() {
        $('#resetFiltersBtn').on('click', (e) => {
            e.preventDefault();
            this.resetFilters();
        });
    }

    setupPagination() {
        $(document).on('click', '#blogs-container .pagination a', (e) => {
            e.preventDefault();

            if (this.isLoading) return;

            const url = $(e.target).attr('href');
            if (url) {
                const urlParams = new URLSearchParams(url.split('?')[1]);
                const page = urlParams.get('page');

                if (page) {
                    this.loadPage(page);
                }
            }
        });
    }

    applyFilters() {
        if (this.isLoading) return;

        this.showLoadingState();
        this.currentPage = 1;

        const filterData = this.getFilterData();
        this.previousFilterData = JSON.parse(JSON.stringify(filterData));

        $.ajax({
            url: '/admin/blogs',
            method: 'GET',
            data: filterData,
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(filterData);
                this.updateSelectedFiltersDisplay();
            },
            error: (xhr) => {
                this.showError('Erreur lors de l\'application des filtres');
                console.error('Filter error:', xhr.responseJSON);
            },
            complete: () => {
                this.hideLoadingState();
            }
        });
    }

    loadPage(page) {
        if (this.isLoading) return;

        this.showLoadingState();
        this.currentPage = page;

        const filterData = this.getFilterData();
        filterData.page = page;

        $.ajax({
            url: '/admin/blogs',
            method: 'GET',
            data: filterData,
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(filterData);
            },
            error: (xhr) => {
                this.showError('Erreur lors du chargement de la page');
                console.error('Pagination error:', xhr.responseJSON);
            },
            complete: () => {
                this.hideLoadingState();
            }
        });
    }

    resetFilters() {
        $('#filterSearch').val('');
        $('#filterStatus').val('');
        $('#filterAuthor').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        this.applyFilters();
    }

    getFilterData() {
        return {
            search: ($('#filterSearch').val() || '').trim(),
            status: $('#filterStatus').val() || '',
            author: $('#filterAuthor').val() || '',
            date_from: $('#filterDateFrom').val() || '',
            date_to: $('#filterDateTo').val() || '',
        };
    }

    hasFilterChanged() {
        const currentData = this.getFilterData();
        return JSON.stringify(currentData) !== JSON.stringify(this.previousFilterData);
    }

    updateSelectedFiltersDisplay() {
        const filterData = this.getFilterData();
        const activeFilters = [];

        // Check which filters are active
        if (filterData.search) {
            activeFilters.push({
                label: 'Recherche',
                value: filterData.search,
                field: 'search'
            });
        }

        if (filterData.status) {
            const statusLabels = {
                'draft': 'Brouillon',
                'pending': 'En attente',
                'published': 'Publié',
                'rejected': 'Rejeté'
            };
            activeFilters.push({
                label: 'Statut',
                value: statusLabels[filterData.status],
                field: 'status'
            });
        }

        if (filterData.author) {
            const authorName = $(`#filterAuthor option[value="${filterData.author}"]`).text();
            activeFilters.push({
                label: 'Auteur',
                value: authorName,
                field: 'author'
            });
        }

        if (filterData.date_from) {
            activeFilters.push({
                label: 'Du',
                value: filterData.date_from,
                field: 'date_from'
            });
        }

        if (filterData.date_to) {
            activeFilters.push({
                label: 'Au',
                value: filterData.date_to,
                field: 'date_to'
            });
        }

        // Update display
        if (activeFilters.length > 0) {
            $('#selectedFiltersContainer').show();
            let tagsHtml = '';
            activeFilters.forEach(filter => {
                tagsHtml += `
                    <span class="badge bg-light text-dark border">
                        ${filter.label}: <strong>${filter.value}</strong>
                        <button type="button" class="btn-close ms-2" 
                            data-field="${filter.field}" 
                            style="display: inline-block; width: 0.8em; height: 0.8em; opacity: 0.7;">
                        </button>
                    </span>
                `;
            });
            $('#selectedFiltersTags').html(tagsHtml);

            // Setup individual filter removal
            $('.btn-close').on('click', (e) => {
                e.preventDefault();
                const field = $(e.target).data('field');
                $(`#filter${field.charAt(0).toUpperCase() + field.slice(1)}`).val('');
                this.applyFilters();
            });
        } else {
            $('#selectedFiltersContainer').hide();
        }
    }

    updateResults(html) {
        $('#blogs-container').html(html);
    }

    updatePagination(paginationHtml) {
        // Pagination is included in the blogs-list partial
    }

    updateUrl(filterData) {
        const params = new URLSearchParams();

        if (filterData.search) params.append('search', filterData.search);
        if (filterData.status) params.append('status', filterData.status);
        if (filterData.author) params.append('author', filterData.author);
        if (filterData.date_from) params.append('date_from', filterData.date_from);
        if (filterData.date_to) params.append('date_to', filterData.date_to);

        const url = new URL(window.location);
        url.search = params.toString();
        window.history.pushState({}, '', url);
    }

    showLoadingState() {
        this.isLoading = true;
        $('#blogs-container').css('opacity', '0.6').css('pointer-events', 'none');
    }

    hideLoadingState() {
        this.isLoading = false;
        $('#blogs-container').css('opacity', '1').css('pointer-events', 'auto');
    }

    showError(message) {
        console.error(message);
        // You can add a toast notification here if available
    }
}
