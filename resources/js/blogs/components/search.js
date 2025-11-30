/**
 * Public Blogs Search Component
 * Handles dynamic search and pagination for public blog listing
 */

import { showTooltip } from '../../helper.js';

export class PublicBlogsSearch {
    constructor() {
        this.searchTimeout = null;
        this.currentPage = 1;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.setupSearchInput();
        this.setupPagination();
        this.setupFormSubmission();
    }

    setupSearchInput() {
        const searchInput = $('#search-input');

        searchInput.on('input', (e) => {
            const query = $(e.target).val().trim();

            // Clear previous timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // Debounce search requests (300ms delay)
            this.searchTimeout = setTimeout(() => {
                this.performSearch(query);
            }, 300);
        });

        // Clear search on Escape key
        searchInput.on('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.val('');
                this.performSearch('');
            }
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

    setupFormSubmission() {
        // Prevent default form submission and handle via AJAX
        $('form[action*="blogs.index"]').on('submit', (e) => {
            e.preventDefault();
            const query = $('#search-input').val().trim();
            this.performSearch(query);
        });
    }

    performSearch(query) {
        if (this.isLoading) return;

        this.showLoadingState();
        this.currentPage = 1; // Reset to first page on new search

        $.ajax({
            url: '/blogs',
            method: 'GET',
            data: {
                search: query,
                page: this.currentPage
            },
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(query, this.currentPage);

                if (query) {
                    showTooltip($('#search-input'), `Résultats pour "${query}" (${response.count} blogs)`);
                }
            },
            error: (xhr) => {
                this.showError('Erreur lors de la recherche');
                console.error('Search error:', xhr.responseJSON);
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

        const query = $('#search-input').val().trim();

        $.ajax({
            url: '/blogs',
            method: 'GET',
            data: {
                search: query,
                page: page
            },
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(query, page);
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

    updateResults(html) {
        $('#blogs-container').html(html);
    }

    updatePagination(paginationHtml) {
        // Pagination is included in the blogs-list partial, so no need to update separately
    }

    updateUrl(search, page) {
        const params = new URLSearchParams();

        if (search) params.set('search', search);
        if (page > 1) params.set('page', page);

        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newUrl);
    }

    showLoadingState() {
        this.isLoading = true;
        $('#blogs-container').css('opacity', '0.6');

        // Show loading indicator
        if (!$('.search-loading').length) {
            $('#blogs-container').prepend('<div class="search-loading text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Recherche en cours...</span></div><p class="text-muted mt-2">Recherche en cours...</p></div>');
        }
    }

    hideLoadingState() {
        this.isLoading = false;
        $('#blogs-container').css('opacity', '1');
        $('.search-loading').remove();
    }

    showError(message) {
        showTooltip($('#search-input'), message, true);
    }

    // Public methods
    getCurrentFilters() {
        return {
            search: $('#search-input').val().trim(),
            page: this.currentPage
        };
    }

    refresh() {
        const filters = this.getCurrentFilters();
        this.performSearch(filters.search);
    }
}