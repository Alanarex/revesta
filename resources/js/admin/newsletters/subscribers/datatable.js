import { initDatatable } from '../../../components/datatable';
import { formatDate, getCsrfToken } from '../../../utils';

document.addEventListener('DOMContentLoaded', function () {
    initDatatable({
        listMetaName: 'newsletter-subscribers-list-url',
        tableSelector: '#subscribersTable',
        tbodySelector: '#tableBody',
        paginationSelector: '#pagination',
        totalCountSelector: '#totalCount',
        perPage: 20,
        sortableColumns: ['email', 'subscribed_at', 'verified_at'],
        columnMap: ['email', 'verified_at', 'subscribed_at', 'verified_at'],
        renderRow: function (subscriber, { escapeHtml, renderActions }) {
            // Build status badge
            let statusBadge = '';
            if (subscriber.verified_at) {
                statusBadge = '<span class="badge bg-success">Vérifié</span>';
            } else {
                statusBadge = '<span class="badge bg-warning">Non vérifié</span>';
            }

            const subscribedDate = formatDate(subscriber.subscribed_at);
            const verifiedDate = formatDate(subscriber.verified_at);

            return `
                <tr>
                    <td>
                        <div class="text-truncate" title="${escapeHtml(subscriber.email)}">
                            ${escapeHtml(subscriber.email)}
                        </div>
                    </td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center small text-muted">${subscribedDate}</td>
                    <td class="text-center small text-muted">${verifiedDate}</td>
                    <td class="text-center dt-actions">
                        ${renderActions(subscriber.actions, escapeHtml, subscriber)}
                    </td>
                </tr>
            `;
        }
    });
});
