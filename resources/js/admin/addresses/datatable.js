import { initDatatable } from '../datatable/initDatatable.js';

document.addEventListener('DOMContentLoaded', function () {
    initDatatable({
        listMetaName: 'addresses-list-url',
        tableSelector: '#addressesTable',
        tbodySelector: '#addressesTableBody',
        paginationSelector: '#pagination',
        totalCountSelector: '#totalCount',
        showingFromSelector: '#showingFrom',
        showingToSelector: '#showingTo',
        showingTotalSelector: '#showingTotal',
        perPage: 50,
        sortableColumns: ['id', 'label', 'street', 'postal_code', 'city', 'departement'],
        columnMap: ['id', 'label', 'street', 'postal_code', 'city', 'departement'],
        renderRow: function (address, { escapeHtml }) {
            return `
                <tr>
                    <td class="text-center">${escapeHtml(address.id)}</td>
                    <td class="text-center">${escapeHtml(address.label || '-')}</td>
                    <td class="text-center">${escapeHtml(address.street)}</td>
                    <td class="text-center">${escapeHtml(address.postal_code)}</td>
                    <td class="text-center">${escapeHtml(address.city)}</td>
                    <td class="text-center">${escapeHtml(address.departement)}</td>
                    <td class="text-center dt-actions">
                        <a href="/admin/addresses/${address.id}/edit" class="dt-action-btn" data-tooltip="Modifier" title="Modifier">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="dt-action-btn text-danger" data-dt-delete-id="${address.id}" data-dt-label="${escapeHtml(address.label || address.city)}" data-tooltip="Supprimer" title="Supprimer">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        },
        deleteConfig: {
            deleteUrl: (id) => `/admin/addresses/${id}`,
            confirmTitle: 'Supprimer cette adresse ?',
            confirmInfo: (dataset) => dataset.dtLabel || dataset.label || '',
        }
    });
});
