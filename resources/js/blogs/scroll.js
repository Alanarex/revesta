/**
 * Smooth scroll to comments section
 */
$(document).ready(function () {
    // Handle scroll to comments button
    $(document).on('click', '.scroll-to-comments', function (e) {
        e.preventDefault();
        const commentsSection = $('#comments-section');
        if (commentsSection.length) {
            $('html, body').animate({
                scrollTop: commentsSection.offset().top
            }, 500);
        }
    });
});
