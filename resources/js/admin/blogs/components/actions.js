/**
 * Admin Blog Actions Component
 * Handles individual approve/reject actions for blogs in admin view
 */

import { showTooltip } from '../../../helper.js';

export class AdminBlogActions {
    constructor() {
        this.init();
    }

    init() {
        this.setupSingleActions();
    }

    setupSingleActions() {
        // Single approve button
        $(document).on('click', '.approve-single-btn', (e) => {
            const button = $(e.target).closest('.approve-single-btn');
            const blogId = button.data('blog-id');
            this.approveSingleBlog(blogId, button);
        });

        // Single reject button
        $(document).on('click', '.reject-single-btn', (e) => {
            const button = $(e.target).closest('.reject-single-btn');
            const blogId = button.data('blog-id');
            this.rejectSingleBlog(blogId, button);
        });
    }

    approveSingleBlog(blogId, button) {
        Swal.fire({
            title: 'Approuver ce blog?',
            text: 'Le blog sera publié et visible par tous les utilisateurs.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, approuver',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeSingleAction('approve', blogId, button);
            }
        });
    }

    rejectSingleBlog(blogId, button) {
        Swal.fire({
            title: 'Rejeter ce blog?',
            input: 'textarea',
            inputPlaceholder: 'Raison du rejet (optionnelle)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, rejeter',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeSingleAction('reject', blogId, button, result.value);
            }
        });
    }

    executeSingleAction(action, blogId, button, reason = null) {
        const endpoint = action === 'approve'
            ? `/admin/blogs/${blogId}/approve`
            : `/admin/blogs/${blogId}/reject`;

        // Disable button and show loading
        button.prop('disabled', true);
        const originalHtml = button.html();
        button.html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: endpoint,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason
            },
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || `Blog ${action === 'approve' ? 'approuvé' : 'rejeté'} avec succès`,
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
                    // Re-enable button
                    button.prop('disabled', false).html(originalHtml);
                }
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: xhr.responseJSON?.message || 'Une erreur est survenue lors de l\'action'
                });
                // Re-enable button
                button.prop('disabled', false).html(originalHtml);
            }
        });
    }
}