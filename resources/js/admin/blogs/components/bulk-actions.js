/**
 * Bulk Actions Component
 * Handles bulk actions for blog bulk actions with specific selection controls
 */

import { showTooltip } from '../../../helper.js';

export class BulkActions {
    constructor() {
        this.selectedBlogs = new Set();
        this.init();
    }

    init() {
        this.setupBulkSelection();
        this.setupBulkActions();
        this.setupCheckboxes();
    }

    setupBulkSelection() {
        // Select all pending blogs button
        $('#selectAllVisibleBtn').on('click', () => {
            this.selectPendingBlogs();
        });

        // Deselect all button (the one that replaces select all)
        $('#deselectAllVisibleBtn').on('click', () => {
            this.deselectAllBlogs();
        });
    }

    setupBulkActions() {
        // Bulk approve selected blogs
        $('#bulkApproveBtn').on('click', () => {
            this.performBulkApprove();
        });

        // Bulk reject selected blogs
        $('#bulkRejectBtn').on('click', () => {
            this.performBulkReject();
        });
    }

    setupCheckboxes() {
        // Individual checkbox toggle
        $(document).on('change', '.blog-checkbox', (e) => {
            const checkbox = $(e.target);
            const blogId = checkbox.val();

            if (checkbox.is(':checked')) {
                this.selectedBlogs.add(blogId);
            } else {
                this.selectedBlogs.delete(blogId);
            }

            this.updateBulkActionsVisibility();
        });
    }

    selectPendingBlogs() {
        // Get all pending blog IDs from the server
        $.ajax({
            url: '/admin/blogs/all-ids',
            method: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.success && response.ids && Array.isArray(response.ids)) {
                    const pendingIds = response.ids;

                    // Select all checkboxes that match the pending blog IDs
                    $('.blog-checkbox').each(function () {
                        const checkbox = $(this);
                        const blogId = checkbox.val();

                        if (pendingIds.includes(parseInt(blogId))) {
                            checkbox.prop('checked', true).trigger('change');
                        }
                    });

                    const selectedCount = this.selectedBlogs.size;
                    if (selectedCount > 0) {
                        showTooltip($('#selectAllVisibleBtn'), `${selectedCount} blogs en attente sélectionnés`);
                    } else {
                        showTooltip($('#selectAllVisibleBtn'), 'Aucun blog en attente trouvé', true);
                    }
                } else {
                    showTooltip($('#selectAllVisibleBtn'), 'Erreur lors de la récupération des blogs', true);
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.message || 'Erreur lors de la récupération des blogs en attente';
                showTooltip($('#selectAllVisibleBtn'), errorMsg, true);
            }
        });
    }

    deselectAllBlogs() {
        $('.blog-checkbox').prop('checked', false).trigger('change');
        showTooltip($('#deselectAllVisibleBtn'), 'Sélection vidée');
    }

    performBulkApprove() {
        if (this.selectedBlogs.size === 0) {
            Swal.fire('Aucun blog sélectionné', 'Veuillez sélectionner au moins un blog à approuver.', 'warning');
            return;
        }

        const blogCount = this.selectedBlogs.size;
        const confirmMessage = `Approuver ${blogCount} blog${blogCount > 1 ? 's' : ''} sélectionné${blogCount > 1 ? 's' : ''}?`;

        Swal.fire({
            title: confirmMessage,
            text: 'Cette action approuvera tous les blogs sélectionnés.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, approuver',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeBulkAction('approve', Array.from(this.selectedBlogs));
            }
        });
    }

    performBulkReject() {
        if (this.selectedBlogs.size === 0) {
            Swal.fire('Aucun blog sélectionné', 'Veuillez sélectionner au moins un blog à refuser.', 'warning');
            return;
        }

        const blogCount = this.selectedBlogs.size;
        const confirmMessage = `Refuser ${blogCount} blog${blogCount > 1 ? 's' : ''} sélectionné${blogCount > 1 ? 's' : ''}?`;

        Swal.fire({
            title: confirmMessage,
            input: 'textarea',
            inputPlaceholder: 'Raison du refus (optionnelle)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, refuser',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeBulkAction('reject', Array.from(this.selectedBlogs), result.value);
            }
        });
    }

    executeBulkAction(action, blogIds, reason = null) {
        const endpoint = action === 'approve'
            ? '/admin/blogs/approve/bulk'
            : '/admin/blogs/reject/bulk';

        $.ajax({
            url: endpoint,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                blog_ids: blogIds,
                reason: reason
            },
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || `Action ${action} effectuée avec succès`,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the page or reload the content
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Une erreur est survenue'
                    });
                }
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: xhr.responseJSON?.message || 'Une erreur est survenue lors de l\'action'
                });
            }
        });
    }

    updateBulkActionsVisibility() {
        const hasSelection = this.selectedBlogs.size > 0;
        $('.bulk-action-btn').prop('disabled', !hasSelection);

        // Show/hide bulk actions bar with opacity to maintain height
        if (hasSelection) {
            $('#bulkActionsBar').css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
            // Hide select all button and show deselect all button
            $('#selectAllVisibleBtn').hide();
            $('#deselectAllVisibleBtn').show();
        } else {
            $('#bulkActionsBar').css({
                'opacity': '0',
                'pointer-events': 'none'
            });
            // Show select all button and hide deselect all button
            $('#selectAllVisibleBtn').show();
            $('#deselectAllVisibleBtn').hide();
        }

        // Hide the clear selection button in bulk actions bar
        $('#clearSelectionBtn').hide();

        // Update selection count display if exists
        const countDisplay = $('#selectedCount');
        if (countDisplay.length) {
            countDisplay.text(`${this.selectedBlogs.size}`);
        }
    }

    // Public methods
    getSelectedBlogs() {
        return Array.from(this.selectedBlogs);
    }

    clearSelection() {
        this.selectedBlogs.clear();
        $('.blog-checkbox').prop('checked', false);
        this.updateBulkActionsVisibility();
    }
}

// Initialize when DOM is ready
$(function () {
    new BulkActions();
});