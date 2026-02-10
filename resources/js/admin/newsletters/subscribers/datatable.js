import { initDatatable } from '../../../components/datatable';
import { formatDate, getCsrfToken } from '../../../utils';

document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = getCsrfToken();

    initDatatable({
        listMetaName: 'newsletter-subscribers-list-url',
        tableSelector: '#subscribersTable',
        tbodySelector: '#tableBody',
        paginationSelector: '#pagination',
        totalCountSelector: '#totalCount',
        perPage: 20,
        sortableColumns: ['email', 'subscribed_at', 'verified_at'],
        columnMap: ['email', 'verified_at', 'subscribed_at', 'verified_at'],
        renderRow: function (subscriber, { escapeHtml }) {
            // Build status badge
            let statusBadge = '';
            if (subscriber.verified_at) {
                statusBadge = '<span class="badge bg-success">Vérifié</span>';
            } else {
                statusBadge = '<span class="badge bg-warning">Non vérifié</span>';
            }

            const subscribedDate = formatDate(subscriber.subscribed_at);
            const verifiedDate = formatDate(subscriber.verified_at);

            // Build action buttons
            let actionsHtml = '';

            // Verify button for unverified subscribers
            if (!subscriber.verified_at) {
                actionsHtml += `
                    <form method="POST" action="/admin/newsletter-subscribers/${subscriber.id}/verify" style="display: inline;" 
                          onsubmit="return confirm('Vérifier cet abonné ?');">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="dt-action-btn text-success" data-tooltip="Vérifier" title="Vérifier">
                            <i class="fa fa-check-circle"></i>
                        </button>
                    </form>
                `;
            }

            // Delete button
            actionsHtml += `
                <button class="dt-action-btn text-danger" data-dt-delete-id="${subscriber.id}" 
                        data-dt-label="${escapeHtml(subscriber.email)}" data-tooltip="Supprimer" title="Supprimer">
                    <i class="fa fa-trash"></i>
                </button>
            `;

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
                    <td class="text-center dt-actions">${actionsHtml}</td>
                </tr>
            `;
        },
        deleteConfig: {
            deleteUrl: (id) => `/admin/newsletter-subscribers/${id}`,
            confirmTitle: 'Supprimer cet abonné ?',
            confirmInfo: (dataset) => dataset.dtLabel || '',
        }
    });
});
