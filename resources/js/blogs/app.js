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