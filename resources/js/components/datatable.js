export async function initDatatable(config) {
    const {
        listMetaName,
        listUrl,
        tableSelector,
        tbodySelector,
        searchInputSelector = '#searchInput',
        clearSearchSelector = '#clearSearch',
        paginationSelector = '#pagination',
        totalCountSelector = '#totalCount',
        showingFromSelector = '#showingFrom',
        showingToSelector = '#showingTo',
        showingTotalSelector = '#showingTotal',
        perPage = 50,
        sortableColumns = [],
        columnMap = null, // optional array mapping header index -> column name
        renderRow,
        deleteConfig = null, // { deleteUrl: id=>string, confirmTitle, confirmInfo: item=>string }
        injectStyles = true,
        debounceMs = 400,
    } = config;

    const table = document.querySelector(tableSelector);
    const tbody = document.querySelector(tbodySelector);
    const searchInput = document.querySelector(searchInputSelector);
    const clearSearch = document.querySelector(clearSearchSelector);
    const pagination = document.querySelector(paginationSelector);
    const totalCountEl = document.querySelector(totalCountSelector);
    const showingFromEl = document.querySelector(showingFromSelector);
    const showingToEl = document.querySelector(showingToSelector);
    const showingTotalEl = document.querySelector(showingTotalSelector);

    if (!table || !tbody) return;

    let currentPage = 1;
    let searchQuery = '';
    let sortColumn = null;
    let sortDirection = 'asc';

    const listUrlBase = (() => {
        if (listUrl) return listUrl;
        if (listMetaName) {
            const meta = document.querySelector(`meta[name="${listMetaName}"]`);
            if (meta) return meta.getAttribute('content');
        }
        return null;
    })();

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function buildUrl(page = 1) {
        const url = new URL(listUrlBase, window.location.origin);
        url.searchParams.set('page', page);
        if (searchQuery) url.searchParams.set('search', searchQuery);
        if (sortColumn) {
            url.searchParams.set('sort', sortColumn);
            url.searchParams.set('direction', sortDirection);
        }
        url.searchParams.set('per_page', perPage);
        return url.toString();
    }

    async function loadData(page = 1) {
        currentPage = page;

        // show loading
        tbody.innerHTML = `<tr><td colspan="${table.querySelectorAll('thead th').length}" class="text-center">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
        </td></tr>`;

        try {
            const res = await fetch(buildUrl(page));
            const data = await res.json();
            renderTable(data.data || data);
            renderPagination(data);
            updateStats(data);
        } catch (err) {
            console.error('Error loading datatable:', err);
            tbody.innerHTML = `<tr><td colspan="${table.querySelectorAll('thead th').length}" class="text-center text-danger">Erreur lors du chargement des données</td></tr>`;
        }
    }

    function renderTable(items) {
        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${table.querySelectorAll('thead th').length}" class="text-center text-muted">Aucune donnée trouvée</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(item => renderRow(item, { escapeHtml })).join('');

        // attach delete handlers if configured
        if (deleteConfig && deleteConfig.deleteUrl) {
            tbody.querySelectorAll('[data-dt-delete-id]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-dt-delete-id');
                    const info = deleteConfig.confirmInfo ? deleteConfig.confirmInfo(this.dataset) : id;
                    const title = deleteConfig.confirmTitle || 'Supprimer ?';
                            // Prefer SweetAlert2 confirmation when available, otherwise use confirmDelete helper or window.confirm
                            let doConfirmFunc;
                            if (typeof Swal !== 'undefined') {
                                doConfirmFunc = () => Swal.fire({
                                    title: title || 'Confirmer',
                                    text: String(info || ''),
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Oui, supprimer',
                                    cancelButtonText: 'Annuler',
                                    reverseButtons: true
                                }).then(r => ({ isConfirmed: !!r.isConfirmed }));
                            } else if (typeof confirmDelete !== 'undefined') {
                                doConfirmFunc = () => confirmDelete(title, info);
                            } else {
                                doConfirmFunc = () => Promise.resolve(window.confirm(`${title}\n${info}`) ? { isConfirmed: true } : { isConfirmed: false });
                            }

                            doConfirmFunc().then(result => {
                        if (result && result.isConfirmed) {
                            // show loading via simple alert or Swal if available
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ title: 'Suppression en cours...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            }
                            // Use POST with method override to support servers that don't accept DELETE verbs
                            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            const url = deleteConfig.deleteUrl(id);
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                                },
                                body: new URLSearchParams({ _method: 'DELETE' }).toString()
                            }).then(async (r) => {
                                if (r.ok) {
                                    // successful HTTP response; attempt to parse JSON for message
                                    let data = null;
                                    try { data = await r.json(); } catch (e) { data = null; }
                                    const message = (data && data.message) ? data.message : '';
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon: 'success', title: 'Supprimé !', text: message || '', timer: 1500, showConfirmButton: false });
                                    }
                                    // reload the table to reflect deletion
                                    loadData(currentPage);
                                } else {
                                    // try to extract error message from JSON
                                    let errMsg = 'Erreur lors de la suppression';
                                    try { const errData = await r.json(); if (errData && errData.message) errMsg = errData.message; } catch (e) {}
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon: 'error', title: 'Erreur', text: errMsg });
                                    } else {
                                        alert(errMsg);
                                    }
                                }
                            }).catch(err => {
                                console.error('Delete error', err);
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur lors de la suppression' });
                                } else {
                                    alert('Erreur lors de la suppression');
                                }
                            });
                        }
                    });
                });
            });
        }

        // add sortable headers
        updateSortHeaders();
    }

    function updateSortHeaders() {
        const thead = table.querySelector('thead tr');
        if (!thead) return;

        thead.querySelectorAll('th').forEach((th, index) => {
            th.style.cursor = 'pointer';
            th.classList.remove('table-sort-asc', 'table-sort-desc');

            // determine column name
            let columnName = th.getAttribute('data-sort') || (Array.isArray(columnMap) ? columnMap[index] : null);
            // remove old listener by cloning
            const newTh = th.cloneNode(true);
            th.parentNode.replaceChild(newTh, th);

            if (columnName && (sortableColumns.length === 0 || sortableColumns.includes(columnName))) {
                if (sortColumn === columnName) {
                    newTh.classList.add(sortDirection === 'asc' ? 'table-sort-asc' : 'table-sort-desc');
                }

                newTh.addEventListener('click', function () {
                    if (sortColumn === columnName) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortColumn = columnName;
                        sortDirection = 'asc';
                    }
                    loadData(1);
                });
            }
        });
    }

    function renderPagination(data) {
        if (!pagination) return;
        const totalPages = Math.max(1, Math.ceil((data.total || 0) / (data.per_page || perPage)));
        if (totalPages <= 1) { pagination.innerHTML = ''; return; }

        let html = '';
        html += `<li class="page-item ${currentPage===1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage-1}"><i class="fa fa-chevron-left"></i></a></li>`;

        const maxPages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(totalPages, startPage + maxPages -1);
        if (endPage - startPage < maxPages -1) startPage = Math.max(1, endPage - maxPages +1);

        if (startPage > 1) { html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`; if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`; }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i===currentPage ? 'active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        if (endPage < totalPages) { if (endPage < totalPages-1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`; html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`; }

        html += `<li class="page-item ${currentPage===totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage+1}"><i class="fa fa-chevron-right"></i></a></li>`;

        pagination.innerHTML = html;

        pagination.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage) loadData(page);
            });
        });
    }

    function updateStats(data) {
        const total = data.total || 0;
        const per = data.per_page || perPage;
        const from = (total === 0) ? 0 : ((currentPage-1) * per) +1;
        const to = Math.min(currentPage * per, total);
        if (showingFromEl) showingFromEl.textContent = from;
        if (showingToEl) showingToEl.textContent = to;
        if (showingTotalEl) showingTotalEl.textContent = total;
        if (totalCountEl) totalCountEl.textContent = total;
    }

    // search handling
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => { searchQuery = this.value.trim(); loadData(1); }, debounceMs);
        });
    }
    if (clearSearch) {
        clearSearch.addEventListener('click', function () { if (searchInput) searchInput.value=''; searchQuery=''; loadData(1); });
    }

    // inject sort styles
    if (injectStyles) {
        const style = document.createElement('style');
        style.textContent = `
            ${tableSelector} thead th { user-select: none; cursor: pointer; transition: background-color 0.2s; }
            ${tableSelector} thead th:hover { background-color: #e9ecef; }
            .table-sort-asc::after { content: ' ↑'; color: #007bff; font-weight: bold; }
            .table-sort-desc::after { content: ' ↓'; color: #007bff; font-weight: bold; }
        `;
        document.head.appendChild(style);
    }

    // initial load
    await loadData(1);

    // listen for Bootstrap dropdown show/hide events to disable row hover while menu is open
    function onDropdownShow() {
        if (table) table.classList.add('datatable-dropdown-open');
    }
    function onDropdownHide() {
        if (table) table.classList.remove('datatable-dropdown-open');
    }

    // support Bootstrap 5 native events
    table.addEventListener('show.bs.dropdown', onDropdownShow);
    table.addEventListener('shown.bs.dropdown', onDropdownShow);
    table.addEventListener('hide.bs.dropdown', onDropdownHide);
    table.addEventListener('hidden.bs.dropdown', onDropdownHide);

    return {
        reload: () => loadData(currentPage),
        goToPage: (p) => loadData(p),
    };
}
