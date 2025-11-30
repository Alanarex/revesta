/**
 * Admin Blogs Search Component
 * Handles dynamic search and filtering for admin blog management
 */

import { showTooltip } from '../../../helper.js';

export class AdminBlogsSearch {
    constructor() {
        this.searchTimeout = null;
        this.currentPage = 1;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.setupSearchInput();
        this.setupAuthorFilter();
        this.setupPagination();
        this.setupResetButton();
    }

    setupSearchInput() {
        const searchInput = $('#search');

        searchInput.on('input', (e) => {
            const query = $(e.target).val().trim();

            // Clear previous timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            // Debounce search requests (300ms delay)
            this.searchTimeout = setTimeout(() => {
                this.performSearch(query, $('#author').val());
            }, 300);
        });

        // Clear search on Escape key
        searchInput.on('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.val('');
                this.performSearch('', $('#author').val());
            }
        });
    }

    setupAuthorFilter() {
        $('#author').on('change', (e) => {
            const authorId = $(e.target).val();
            const searchQuery = $('#search').val().trim();

            this.performSearch(searchQuery, authorId);
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

    setupResetButton() {
        // The reset link already works with form submission, but we can enhance it
        $('a[href*="admin.blogs.index"]').on('click', (e) => {
            if ($(e.target).text().includes('Réinitialiser')) {
                e.preventDefault();
                this.resetSearch();
            }
        });
    }

    performSearch(query, authorId) {
        if (this.isLoading) return;

        this.showLoadingState();
        this.currentPage = 1; // Reset to first page on new search

        $.ajax({
            url: '/admin/blogs',
            method: 'GET',
            data: {
                search: query,
                author: authorId,
                page: this.currentPage
            },
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(query, authorId, this.currentPage);

                if (query || authorId) {
                    showTooltip($('#search'), `Résultats filtrés (${response.count} blogs)`);
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

        const query = $('#search').val().trim();
        const authorId = $('#author').val();

        $.ajax({
            url: '/admin/blogs',
            method: 'GET',
            data: {
                search: query,
                author: authorId,
                page: page
            },
            dataType: 'json',
            success: (response) => {
                this.updateResults(response.html);
                this.updatePagination(response.pagination);
                this.updateUrl(query, authorId, page);
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

    resetSearch() {
        $('#search').val('');
        $('#author').val('');
        this.currentPage = 1;

        this.performSearch('', '');
        this.updateUrl('', '', 1);
    }

    updateResults(html) {
        $('#blogs-container').html(html);
    }

    updatePagination(paginationHtml) {
        $('#blogs-container .pagination').parent().html(paginationHtml);
    }

    updateUrl(search, author, page) {
        const params = new URLSearchParams();

        if (search) params.set('search', search);
        if (author) params.set('author', author);
        if (page > 1) params.set('page', page);

        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newUrl);
    }

    showLoadingState() {
        this.isLoading = true;
        $('#blogs-container .blog-item').css('opacity', '0.6');
        $('#blogs-container .pagination').css('pointer-events', 'none');

        // Show loading indicator
        if (!$('.search-loading').length) {
            $('#blogs-container').append('<div class="search-loading text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Recherche en cours...</span></div><p class="text-muted mt-2">Recherche en cours...</p></div>');
        }
    }

    hideLoadingState() {
        this.isLoading = false;
        $('#blogs-container .blog-item').css('opacity', '1');
        $('#blogs-container .pagination').css('pointer-events', 'auto');
        $('.search-loading').remove();
    }

    showError(message) {
        showTooltip($('#search'), message, true);
    }

    // Public methods
    getCurrentFilters() {
        return {
            search: $('#search').val().trim(),
            author: $('#author').val(),
            page: this.currentPage
        };
    }

    refresh() {
        const filters = this.getCurrentFilters();
        this.performSearch(filters.search, filters.author);
    }
}