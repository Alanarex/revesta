/**
 * Delete Button Component
 * Handles blog deletion with confirmation
 */
export class DeleteButton {
    constructor() {
        this.init();
    }

    init() {
        $(document).on('click', '.delete-blog-btn', (e) => {
            e.preventDefault();
            this.handleDelete(e);
        });
    }

    handleDelete(e) {
        const btn = $(e.target).closest('.delete-blog-btn');
        const blogId = btn.data('blog-id');
        const isAdmin = $('body').data('is-admin') === true;

        Swal.fire({
            title: 'Supprimer ce blog?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            input: isAdmin ? 'textarea' : null,
            inputPlaceholder: isAdmin ? 'Raison (optionnel)' : null,
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.performDelete(blogId, result.value);
            }
        });
    }

    performDelete(blogId, reason = null) {
        $.ajax({
            url: `/blogs/${blogId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason
            },
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/blogs';
                    });
                }
            },
            error: (xhr) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: xhr.responseJSON?.message || 'Une erreur est survenue',
                    timer: 3000
                });
            }
        });
    }
}

// Initialize when DOM is ready
$(function () {
    new DeleteButton();
});