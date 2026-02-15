/**
 * Bookmark Button Component
 * Handles bookmark toggle functionality for blogs
 */

import { showTooltip, isElementDisabled } from '../../helper.js';

export class BookmarkButton {
    constructor() {
        this.init();
    }

    init() {
        $(document).on('click', '.bookmark-btn:not([data-auth-required])', (e) => {
            e.preventDefault();
            this.handleBookmarkToggle(e);
        });
    }

    handleBookmarkToggle(e) {
        const btn = $(e.target).closest('.bookmark-btn');
        if (isElementDisabled(btn)) return;

        const blogId = btn.data('blog-id');

        // Optimistic UI update
        btn.prop('disabled', true);

        const url = `/admin/blogs/${blogId}/bookmarks/toggle`;

        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.success) {
                    this.updateBookmarkUI(btn, response.is_filled);
                    const message = response.is_filled ? 'Signet ajouté' : 'Signet supprimé';
                    showTooltip(btn, message);
                } else {
                    showTooltip(btn, 'Une erreur est survenue. Veuillez réessayer.', true);
                }
                btn.prop('disabled', false);
            },
            error: (xhr) => {
                showTooltip(btn, 'Une erreur est survenue. Veuillez réessayer.', true);
                btn.prop('disabled', false);
            }
        });
    }

    updateBookmarkUI(btn, newState) {
        const icon = btn.find('i');
        btn.data('bookmarked', newState);

        if (newState) {
            icon.removeClass('far').addClass('fas');
        } else {
            icon.removeClass('fas').addClass('far');
        }
    }


}

// Initialize when DOM is ready
$(function () {
    new BookmarkButton();
});