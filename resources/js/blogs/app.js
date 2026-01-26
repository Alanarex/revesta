import { initAuthRequiredHandler } from '../utils/auth-handler';

// Initialize authentication required handler
initAuthRequiredHandler();

// Scroll to comments section
$(document).on('click', '.scroll-to-comments', function(e) {
    e.preventDefault();
    const commentsSection = $('#comments-section');
    if (commentsSection.length) {
        $('html, body').animate({
            scrollTop: commentsSection.offset().top - 20
        }, 0, function() {
            // Focus on the main comment input after scroll completes
            const commentInput = commentsSection.find('.comment-form[data-parent-id=""] .comment-input').first();
            if (commentInput.length) {
                commentInput.focus();
            }
        });
    }
});

// Approve blog button handler
$(document).on('click', '.approve-single-btn', function(e) {
    e.preventDefault();
    const blogId = $(this).data('blog-id');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    Swal.fire({
        title: 'Approuver le blog?',
        text: 'Êtes-vous sûr de vouloir approuver ce blog?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, approuver',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/blogs/${blogId}/approve`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        Swal.fire({
                            title: 'Succès!',
                            text: 'Blog approuvé avec succès',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: response.message || 'Erreur lors de l\'approbation',
                            icon: 'error'
                        });
                    }
                },
                error: (xhr) => {
                    const message = xhr.responseJSON?.message || 'Erreur lors de l\'approbation';
                    Swal.fire({
                        title: 'Erreur',
                        text: message,
                        icon: 'error'
                    });
                }
            });
        }
    });
});

// Reject blog button handler
$(document).on('click', '.reject-single-btn', function(e) {
    e.preventDefault();
    const blogId = $(this).data('blog-id');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    Swal.fire({
        title: 'Rejeter le blog',
        input: 'textarea',
        inputLabel: 'Raison du rejet',
        inputPlaceholder: 'Entrez la raison du rejet (facultatif)',
        showCancelButton: true,
        confirmButtonText: 'Rejeter',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/blogs/${blogId}/reject`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    rejection_reason: result.value || ''
                },
                success: (response) => {
                    if (response.success) {
                        Swal.fire({
                            title: 'Succès!',
                            text: 'Blog rejeté avec succès',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: response.message || 'Erreur lors du rejet',
                            icon: 'error'
                        });
                    }
                },
                error: (xhr) => {
                    const message = xhr.responseJSON?.message || 'Erreur lors du rejet';
                    Swal.fire({
                        title: 'Erreur',
                        text: message,
                        icon: 'error'
                    });
                }
            });
        }
    });
});