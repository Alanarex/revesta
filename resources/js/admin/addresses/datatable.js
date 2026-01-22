import { confirmDelete } from '../../helper.js';

document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let searchQuery = '';
    let sortColumn = null;
    let sortDirection = 'asc';

    // Get the list URL from meta tag
    const addressesListUrl = document.querySelector('meta[name="addresses-list-url"]').getAttribute('content');

    // Load addresses
    function loadAddresses(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('addressesTableBody');
        
        // Show loading
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </td>
            </tr>
        `;

        const url = new URL(addressesListUrl);
        url.searchParams.append('page', page);
        if (searchQuery) {
            url.searchParams.append('search', searchQuery);
        }
        if (sortColumn) {
            url.searchParams.append('sort', sortColumn);
            url.searchParams.append('direction', sortDirection);
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderTable(data);
                renderPagination(data);
                updateStats(data);
            })
            .catch(error => {
                console.error('Error loading addresses:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            <i class="fa fa-exclamation-triangle"></i> Erreur lors du chargement des données
                        </td>
                    </tr>
                `;
            });
    }

    // Render table
    function renderTable(data) {
        const tbody = document.getElementById('addressesTableBody');
        const thead = document.querySelector('#addressesTable thead tr');
        
        if (data.data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="fa fa-info-circle"></i> Aucune adresse trouvée
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = data.data.map(address => `
            <tr>
                <td>${address.id}</td>
                <td>${escapeHtml(address.label || '-')}</td>
                <td>${escapeHtml(address.street)}</td>
                <td>${escapeHtml(address.postal_code)}</td>
                <td>${escapeHtml(address.city)}</td>
                <td>${escapeHtml(address.departement)}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="/admin/addresses/${address.id}/edit" class="btn btn-outline-primary" title="Modifier">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger delete-btn" 
                            data-id="${address.id}" 
                            data-label="${escapeHtml(address.label || address.city)}" 
                            data-street="${escapeHtml(address.street)}" 
                            data-city="${escapeHtml(address.city)}" 
                            title="Supprimer">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Attach delete event listeners
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const addressId = this.dataset.id;
                const addressLabel = this.dataset.label;
                const addressStreet = this.dataset.street;
                const addressCity = this.dataset.city;
                
                handleDelete(addressId, addressLabel, addressStreet, addressCity);
            });
        });

        // Add sorting to headers
        updateSortHeaders(thead);
    }

    // Update sort indicators on headers
    function updateSortHeaders(thead) {
        const sortableColumns = ['id', 'label', 'street', 'postal_code', 'city', 'departement'];
        
        thead.querySelectorAll('th').forEach((th, index) => {
            th.style.cursor = 'pointer';
            th.classList.remove('table-sort-asc', 'table-sort-desc');
            
            let columnName = null;
            if (index === 0) columnName = 'id';
            if (index === 1) columnName = 'label';
            if (index === 2) columnName = 'street';
            if (index === 3) columnName = 'postal_code';
            if (index === 4) columnName = 'city';
            if (index === 5) columnName = 'departement';
            
            // Remove old listener and add new one
            const newTh = th.cloneNode(true);
            th.parentNode.replaceChild(newTh, th);
            
            if (columnName && sortableColumns.includes(columnName)) {
                if (sortColumn === columnName) {
                    newTh.classList.add(sortDirection === 'asc' ? 'table-sort-asc' : 'table-sort-desc');
                }
                
                newTh.addEventListener('click', function() {
                    if (sortColumn === columnName) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortColumn = columnName;
                        sortDirection = 'asc';
                    }
                    loadAddresses(1);
                });
            }
        });
    }

    // Render pagination
    function renderPagination(data) {
        const pagination = document.getElementById('pagination');
        const totalPages = Math.ceil(data.total / data.per_page);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let html = '';
        
        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        const maxPages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </li>
        `;

        pagination.innerHTML = html;

        // Attach pagination event listeners
        pagination.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage) {
                    loadAddresses(page);
                }
            });
        });
    }

    // Update stats
    function updateStats(data) {
        const from = data.data.length > 0 ? ((currentPage - 1) * data.per_page) + 1 : 0;
        const to = Math.min(currentPage * data.per_page, data.total);
        
        document.getElementById('showingFrom').textContent = from;
        document.getElementById('showingTo').textContent = to;
        document.getElementById('showingTotal').textContent = data.total;
        document.getElementById('totalCount').textContent = data.total;
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = this.value.trim();
            loadAddresses(1);
        }, 500);
    });

    document.getElementById('clearSearch').addEventListener('click', function() {
        searchInput.value = '';
        searchQuery = '';
        loadAddresses(1);
    });

    // Handle delete with SweetAlert confirmation
    function handleDelete(addressId, addressLabel) {
        const addressInfo = `${addressLabel}`;
        
        confirmDelete(
            'Supprimer cette adresse ?',
            addressInfo
        ).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Suppression en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/admin/addresses/${addressId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimée !',
                            text: data.message || 'Adresse supprimée avec succès',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadAddresses(currentPage);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Erreur lors de la suppression'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors de la suppression'
                    });
                });
            }
        });
    }

    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add styles for sortable headers
    const style = document.createElement('style');
    style.textContent = `
        #addressesTable thead th {
            user-select: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        #addressesTable thead th:hover {
            background-color: #e9ecef;
        }
        .table-sort-asc::after {
            content: ' ↑';
            color: #007bff;
            font-weight: bold;
        }
        .table-sort-desc::after {
            content: ' ↓';
            color: #007bff;
            font-weight: bold;
        }
    `;
    document.head.appendChild(style);

    // Load initial data
    loadAddresses();
});
