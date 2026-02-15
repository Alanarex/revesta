/**
 * Comments Manager - Simplified
 * Uses existing routes, renders HTML server-side
 */
import { isElementDisabled } from '../../helper.js';

export class CommentsManager {
    constructor() {
        this.init();
    }

    init() {
        // Comment form submission
        $(document).on('submit', '.comment-form', (e) => {
            e.preventDefault();
            this.handleCommentSubmit(e);
        });

        // Reply button
        $(document).on('click', '.reply-btn', (e) => {
            this.handleReplyButton(e);
        });

        // Show/hide replies
        $(document).on('click', '.show-replies-btn', (e) => {
            this.handleShowReplies(e);
        });

        // Load more replies
        $(document).on('click', '.load-more-replies', (e) => {
            this.handleLoadMoreReplies(e);
        });

        // Delete comment
        $(document).on('click', '.delete-comment-btn', (e) => {
            this.handleDeleteComment(e);
        });
    }

    handleCommentSubmit(e) {
        const form = $(e.target);
        const submitBtn = form.find('button[type="submit"]').first();

        if (isElementDisabled(form) || isElementDisabled(submitBtn)) return;

        const blogId = form.data('blog-id');
        const parentId = form.data('parent-id') || null;
        const input = form.find('.comment-input');
        const content = input.val().trim();

        if (!content) return;

        $.ajax({
            url: `/admin/blogs/${blogId}/comments`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: content,
                parent_id: parentId
            },
            success: (response) => {
                if (response.success && response.html) {
                    input.val('');
                    this.insertComment(response.html, response.insertType, response.targetId);

                    // If parent count was updated, update the button
                    if (response.parentRepliesCount !== undefined && response.parentCommentId) {
                        const $parentItem = $(`.comment-item[data-comment-id="${response.parentCommentId}"]`);
                        const $showBtn = $parentItem.find('.show-replies-btn').first();
                        
                        if ($showBtn.length) {
                            $showBtn.data('replies-count', response.parentRepliesCount);
                            
                            // Update button text if not currently showing replies
                            if ($showBtn.data('shown') !== 'true') {
                                $showBtn.html('<i class="fa fa-chevron-down"></i> ' + response.parentRepliesCount + ' réponse(s)');
                            }
                        }
                    }
                }
            },
            error: (xhr) => {
                const feedback = $('<div class="small text-danger mt-2 comment-feedback">').text('Une erreur est survenue. Veuillez réessayer.');
                form.append(feedback);
                setTimeout(() => feedback.fadeOut(300, () => feedback.remove()), 3000);
            }
        });
    }

    handleReplyButton(e) {
        const btn = $(e.target).closest('.reply-btn');
        if (isElementDisabled(btn)) return;

        const commentId = btn.data('comment-id');
        const container = btn.closest('.comment-item').find('.reply-form-container').first();

        if (container.is(':visible')) {
            container.hide();
        } else {
            // If form already cached, just show it
            if (container.html().trim()) {
                container.show();
                container.find('.comment-input').focus();
            } else {
                // First time - fetch from server and cache
                const blogId = $('meta[name="blog-id"]').attr('content') || 'null';
                const url = `/admin/blogs/${blogId}/comments/${commentId}/reply-form`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: (response) => {
                        if (response.success && response.html) {
                            container.html(response.html).show();
                            setTimeout(() => {
                                container.find('.comment-input').focus();
                            }, 100);
                        }
                    }
                });
            }
        }
    }

    handleShowReplies(e) {
        const btn = $(e.target).closest('.show-replies-btn');
        const commentId = btn.data('comment-id');
        const container = btn.closest('.comment-item').find('.replies-container').first();
        const isLoaded = container.data('loaded') === true || container.data('loaded') === 'true';
        const isShown = btn.data('shown') === 'true';
        const repliesCount = parseInt(btn.data('replies-count'), 10) || 0;

        // First time showing - fetch replies
        if (!isLoaded) {
            this.loadReplies(commentId, 0, btn, container);
        } else {
            // Already loaded - just toggle visibility
            if (isShown) {
                container.hide();
                btn.html('<i class="fa fa-chevron-down"></i> ' + repliesCount + ' réponse(s)');
                btn.data('shown', 'false');
            } else {
                container.show();
                btn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                btn.data('shown', 'true');
            }
        }
    }

    loadReplies(commentId, offset, btn, container, isLoadMore = false) {
        const level = parseInt(btn.data('level') || 1, 10);
        const blogId = $('meta[name="blog-id"]').attr('content');

        if (!blogId) return;

        const url = `/admin/blogs/${blogId}/comments/${commentId}/replies/load-more`;

        $.ajax({
            url: url,
            method: 'GET',
            data: { offset: offset, level: level, limit: 2 },
            success: (response) => {
                if (response.success) {
                    if (!isLoadMore) {
                        // Initial load - keep connector line
                        container.html(container.find('div').first());
                        container.data('loaded', true);
                    } else {
                        // Load more - remove old button
                        container.find('.load-more-replies').remove();
                    }
                    
                    container.append(response.html);
                    container.show();

                    if (!isLoadMore) {
                        btn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                        btn.data('shown', 'true');
                    }
                }
            },
            error: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue. Veuillez réessayer.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    handleLoadMoreReplies(e) {
        e.preventDefault();
        const btn = $(e.target).closest('.load-more-replies');
        const commentId = btn.data('comment-id');
        const offset = parseInt(btn.data('offset') || 0, 10);
        const level = parseInt(btn.data('level') || 1, 10);
        const container = btn.closest('.comment-item').find('.replies-container').first();

        if (!container.length) return;

        // Create a temporary button-like object for loadReplies
        const tempBtn = $('<div/>').data('level', level);
        this.loadReplies(commentId, offset, tempBtn, container, true);
    }

    handleDeleteComment(e) {
        const commentId = $(e.target).closest('.delete-comment-btn').data('comment-id');

        Swal.fire({
            title: 'Supprimer ce commentaire?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.performCommentDelete(commentId);
            }
        });
    }

    performCommentDelete(commentId) {
        $.ajax({
            url: `/admin/blogs/comments/${commentId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.success) {
                    const $commentItem = $(`.comment-item[data-comment-id="${commentId}"]`);
                    if ($commentItem.length) {
                        $commentItem.fadeOut(200, function () { $(this).remove(); });
                    }

                    // If server returned updated parent HTML, replace the parent
                    if (response.updatedParentHtml && response.parentCommentId) {
                        const $parentItem = $(`.comment-item[data-comment-id="${response.parentCommentId}"]`);
                        if ($parentItem.length) {
                            $parentItem.replaceWith(response.updatedParentHtml);
                        }
                    }
                }
            }
        });
    }

    insertComment(html, insertType, targetId) {
        if (insertType === 'top-level') {
            // Insert at top of comments list
            const commentsList = $('#comments-list');
            if (commentsList.length) {
                commentsList.prepend(html);
            }
        } else if (insertType === 'reply' && targetId) {
            // Insert in parent's replies container
            const parentItem = $(`.comment-item[data-comment-id="${targetId}"]`);
            const repliesContainer = parentItem.find('.replies-container').first();
            
            if (repliesContainer.length) {
                repliesContainer.append(html);
                repliesContainer.show();
            }
        }
    }

}

// Initialize when DOM is ready
$(function () {
    new CommentsManager();
});