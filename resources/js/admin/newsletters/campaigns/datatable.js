import { initDatatable } from '../../../components/datatable';
import { formatDate, getCsrfToken } from '../../../utils';

document.addEventListener('DOMContentLoaded', function () {
    initDatatable({
        listMetaName: 'newsletters-list-url',
        tableSelector: '#newslettersTable',
        tbodySelector: '#tableBody',
        paginationSelector: '#pagination',
        totalCountSelector: '#totalCount',
        perPage: 20,
        sortableColumns: ['title', 'status', 'created_at', 'sent_count'],
        columnMap: ['title', 'status', 'created_at', 'sent_count'],
        renderRow: function (campaign, { escapeHtml, renderActions }) {
            // Build status badge
            let statusBadge = '';
            if (campaign.status === 'draft') {
                statusBadge = '<span class="badge bg-secondary">Brouillon</span>';
            } else if (campaign.status === 'sent') {
                statusBadge = '<span class="badge bg-success">Envoyée</span>';
            } else if (campaign.status === 'scheduled' && campaign.scheduled_at) {
                const scheduledDate = formatDate(campaign.scheduled_at);
                statusBadge = `<span class="badge bg-info" title="Programmée">Programmé le ${scheduledDate}</span>`;
            } else {
                statusBadge = '<span class="badge bg-info">Programmée</span>';
            }

            const createdDate = formatDate(campaign.created_at);

            return `
                <tr>
                    <td>
                        <div class="text-truncate fw-500" title="${escapeHtml(campaign.title)}">
                            ${escapeHtml(campaign.title)}
                        </div>
                    </td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center small text-muted">${createdDate}</td>
                    <td class="text-center">
                        <span class="badge bg-primary" title="Destinataires">${campaign.sent_count || 0}</span>
                    </td>
                    <td class="text-center dt-actions">
                        ${renderActions(campaign.actions, escapeHtml, campaign)}
                    </td>
                </tr>
            `;
        }
    });
});
