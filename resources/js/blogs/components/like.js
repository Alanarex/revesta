/**
 * Like Button Component
 * Handles likes for both blogs and comments using polymorphic approach
 */

import { showTooltip, isElementDisabled } from '../../helper.js';

export class LikeButton {
    constructor() {
        this.init();
    }

    init() {
        $(document).on('click', '.like-btn', (e) => {
            e.preventDefault();
            this.handleLikeToggle(e);
        });
    }

    handleLikeToggle(e) {
        const btn = $(e.target).closest('.like-btn');
        if (isElementDisabled(btn)) return;

        const likeableId = btn.data('likeable-id');
        const likeableType = btn.data('likeable-type');
        const blogId = btn.data('blog-id') || $('meta[name="blog-id"]').attr('content');

        // Determine entity type for better error messages
        const isBlog = likeableType.includes('Blog') && !likeableType.includes('Comment');
        const isComment = likeableType.includes('BlogComment');
        const entityName = isBlog ? 'blog' : (isComment ? 'commentaire' : 'élément');

        const url = `/blogs/${blogId}/likes/toggle`;

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                likeable_id: likeableId,
                likeable_type: likeableType
            },
            success: (response) => {
                if (response.success) {
                    this.updateLikeUI(btn, response.liked, response.likes_count);
                    this.syncLikeButtons(likeableId, likeableType, response.liked, response.likes_count, btn);
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.message || `Erreur lors du like du ${entityName}`;
                showTooltip(btn, errorMsg, true);
            }
        });
    }

    updateLikeUI(btn, isLiked, likesCount) {
        const icon = btn.find('i');
        const count = btn.find('.likes-count');

        // Update icon
        if (isLiked) {
            icon.removeClass('far').addClass('fas');
            btn.data('liked', 'true');
        } else {
            icon.removeClass('fas').addClass('far');
            btn.data('liked', 'false');
        }

        // Update count
        if (count.length) {
            count.text(likesCount);
        }
    }

    syncLikeButtons(likeableId, likeableType, isLiked, likesCount, clickedBtn) {
        try {
            // Normalize the likeable type to handle both single and double backslashes
            const normalizedType = likeableType.replace(/\\\\/g, '\\');

            // Find other buttons for the same item (check both formats)
            $(`.like-btn[data-likeable-id="${likeableId}"]`).not(clickedBtn).each(function () {
                const $other = $(this);
                const otherType = ($other.data('likeable-type') || '').replace(/\\\\/g, '\\');
                
                // Only sync if types match (after normalization)
                if (otherType === normalizedType) {
                    const $otherIcon = $other.find('i').first();
                    const $otherCount = $other.find('.likes-count').first();

                    // Update icon - preserve the original font-size style
                    if ($otherIcon.length) {
                        const originalStyle = $otherIcon.attr('style') || '';
                        const iconHtml = `<i class="${isLiked ? 'fas' : 'far'} fa-heart" style="${originalStyle}"></i>`;
                        $otherIcon.replaceWith(iconHtml);
                    }

                    // Update count
                    if ($otherCount.length) {
                        $otherCount.text(likesCount);
                    }

                    // Update data attribute
                    $other.data('liked', isLiked ? 'true' : 'false');
                }
            });
        } catch (e) {
            // Non-fatal error, continue silently
        }
    }


}

// Initialize when DOM is ready
$(function () {
    new LikeButton();
});