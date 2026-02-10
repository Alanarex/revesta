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
        renderRow: function (campaign, { escapeHtml }) {
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

            // Build action buttons
            let actionsHtml = '';

            // View button for all campaigns
            actionsHtml += `
                <a href="/admin/newsletters/${campaign.id}" class="dt-action-btn" data-tooltip="Voir" title="Voir">
                    <i class="fa fa-eye"></i>
                </a>
            `;

            if (campaign.status === 'draft') {
                actionsHtml += `
                    <a href="/admin/newsletters/${campaign.id}/edit" class="dt-action-btn" data-tooltip="Éditer" title="Éditer">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="/admin/newsletters/${campaign.id}/schedule" class="dt-action-btn" data-tooltip="Programmer" title="Programmer">
                        <i class="fa fa-clock"></i>
                    </a>
                    <button class="dt-action-btn text-success send-now-btn" data-campaign-id="${campaign.id}" data-tooltip="Envoyer" title="Envoyer">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                    <button class="dt-action-btn text-danger delete-campaign-btn" data-campaign-id="${campaign.id}" data-campaign-title="${escapeHtml(campaign.title)}" data-tooltip="Supprimer" title="Supprimer">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
            } else if (campaign.status === 'scheduled') {
                actionsHtml += `
                    <a href="/admin/newsletters/${campaign.id}/schedule" class="dt-action-btn" data-tooltip="Éditer la programmation" title="Éditer la programmation">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <button class="dt-action-btn text-warning cancel-schedule-btn" data-campaign-id="${campaign.id}" data-tooltip="Annuler la programmation" title="Annuler la programmation">
                        <i class="fa fa-x-circle"></i>
                    </button>
                `;
            }

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
                    <td class="text-center dt-actions">${actionsHtml}</td>
                </tr>
            `;
        }
    });
});
