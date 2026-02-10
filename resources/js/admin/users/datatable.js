import { initDatatable } from '../../components/datatable';

document.addEventListener('DOMContentLoaded', function () {
    initDatatable({
        listMetaName: 'users-list-url',
        tableSelector: '#usersTable',
        tbodySelector: '#tableBody',
        paginationSelector: '#pagination',
        totalCountSelector: '#totalCount',
        perPage: 50,
        sortableColumns: ['id', 'full_name', 'email', 'phone', 'role', 'city'],
        columnMap: ['id', 'full_name', 'email', 'phone', 'role', 'city'],
        renderRow: function (user, { escapeHtml }) {
            const initials = (user.full_name || '').split(' ').map(s => s.charAt(0)).slice(0, 2).join('').toUpperCase();
            return `
                <tr>
                    <td class="text-center">${escapeHtml(user.id)}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="user-initials">${escapeHtml(initials)}</span>
                            <span>${escapeHtml(user.full_name)}</span>
                        </div>
                    </td>
                    <td class="text-center">${escapeHtml(user.email)}</td>
                    <td class="text-center">${escapeHtml(user.phone || '')}</td>
                    <td class="text-center"><span class="badge bg-secondary role-badge">${escapeHtml(user.role || '')}</span></td>
                    <td class="text-center small text-muted">${escapeHtml(user.city || '')}${user.city && user.postal_code ? ' - ' : ''}${escapeHtml(user.postal_code || '')}</td>
                    <td class="text-center dt-actions">
                        <a href="/admin/users/${user.id}" class="dt-action-btn" data-tooltip="Voir" title="Voir">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="/admin/users/${user.id}/edit" class="dt-action-btn" data-tooltip="Modifier" title="Modifier">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="dt-action-btn text-danger" data-dt-delete-id="${user.id}" data-dt-label="${escapeHtml(user.full_name)}" data-tooltip="Supprimer" title="Supprimer">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        },
        deleteConfig: {
            deleteUrl: (id) => `/admin/users/${id}`,
            confirmTitle: 'Supprimer cet utilisateur ?',
            confirmInfo: (dataset) => dataset.dtLabel || dataset.label || '',
        }
    });
});
