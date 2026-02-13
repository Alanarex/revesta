import { initDatatable } from '../../components/datatable';

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
        renderRow: function (address, { escapeHtml, renderActions }) {
            return `
                <tr>
                    <td class="text-center">${escapeHtml(address.id)}</td>
                    <td class="text-center">${escapeHtml(address.label || '-')}</td>
                    <td class="text-center">${escapeHtml(address.street)}</td>
                    <td class="text-center">${escapeHtml(address.postal_code)}</td>
                    <td class="text-center">${escapeHtml(address.city)}</td>
                    <td class="text-center">${escapeHtml(address.departement)}</td>
                    <td class="text-center dt-actions">
                        ${renderActions(address.actions, escapeHtml, address)}
                    </td>
                </tr>
            `;
        }
    });
});
