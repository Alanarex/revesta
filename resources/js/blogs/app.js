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

    if (confirm('Êtes-vous sûr de vouloir approuver ce blog?')) {
        $.ajax({
            url: `/admin/blogs/${blogId}/approve`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: (response) => {
                if (response.success) {
                    alert('Blog approuvé avec succès');
                    location.reload();
                } else {
                    alert(response.message || 'Erreur lors de l\'approbation');
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erreur lors de l\'approbation';
                alert(message);
            }
        });
    }
});

// Reject blog button handler
$(document).on('click', '.reject-single-btn', function(e) {
    e.preventDefault();
    const blogId = $(this).data('blog-id');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    const reason = prompt('Entrez la raison du rejet (facultatif):');
    if (reason !== null) { // null means cancel was clicked
        $.ajax({
            url: `/admin/blogs/${blogId}/reject`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                rejection_reason: reason || ''
            },
            success: (response) => {
                if (response.success) {
                    alert('Blog rejeté avec succès');
                    location.reload();
                } else {
                    alert(response.message || 'Erreur lors du rejet');
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erreur lors du rejet';
                alert(message);
            }
        });
    }
});