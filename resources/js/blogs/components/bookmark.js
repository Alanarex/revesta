/**
 * Bookmark Button Component
 * Handles bookmark toggle functionality for blogs
 */

import { showTooltip } from '../../helper.js';

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
        if (this.isElementDisabled(btn)) return;

        const blogId = btn.data('blog-id');
        const currentState = btn.data('bookmarked') === 'true' || btn.data('bookmarked') === true;
        const icon = btn.find('i');

        // Optimistic UI update
        btn.prop('disabled', true);

        $.ajax({
            url: '/blogs/bookmarks/toggle',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                blog_id: blogId
            },
            success: (response) => {
                if (response.success) {
                    this.updateBookmarkUI(btn, !currentState);
                    const message = !currentState ? 'Signet ajouté' : 'Signet supprimé';
                    showTooltip(btn, message);
                } else {
                    showTooltip(btn, 'Erreur: ' + (response.message || 'Action échouée'), true);
                }
                btn.prop('disabled', false);
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.message || 'Une erreur est survenue';
                showTooltip(btn, errorMsg, true);
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

    isElementDisabled(jqEl) {
        if (!jqEl || jqEl.length === 0) return false;
        const attrDisabled = typeof jqEl.attr('disabled') !== 'undefined' && jqEl.attr('disabled') !== false;
        const ariaDisabled = jqEl.attr('aria-disabled') === 'true';
        const dataDisabled = jqEl.data('disabled') === true || jqEl.data('disabled') === 'true';
        return attrDisabled || ariaDisabled || dataDisabled;
    }
}

// Initialize when DOM is ready
$(function () {
    new BookmarkButton();
});