/**
 * Comments Component
 * Handles all comment-related functionality: submit, reply, show/hide replies, load more, delete
 */
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

        if (this.isElementDisabled(form) || this.isElementDisabled(submitBtn)) return;

        const blogId = form.data('blog-id');
        const parentId = form.data('parent-id') || null;
        const input = form.find('.comment-input');
        const content = input.val().trim();

        if (!content) return;

        $.ajax({
            url: `/blogs/${blogId}/comments`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: content,
                parent_id: parentId
            },
            success: (response) => {
                if (response.success) {
                    input.val('');
                    this.insertComment(response.html, parentId);
                }
            },
            error: (xhr) => {
                const errorText = xhr.responseJSON?.message || 'Impossible d\'ajouter le commentaire.';
                const feedback = $('<div class="small text-danger mt-2 comment-feedback">').text(errorText);
                form.append(feedback);
                setTimeout(() => feedback.fadeOut(300, () => feedback.remove()), 3000);
            }
        });
    }

    handleReplyButton(e) {
        const btn = $(e.target).closest('.reply-btn');
        if (this.isElementDisabled(btn)) return;

        const commentId = btn.data('comment-id');
        const container = btn.closest('.comment-item').find('.reply-form-container').first();

        if (container.is(':visible')) {
            container.hide().empty();
        } else {
            const blogId = $('meta[name="blog-id"]').attr('content') || 'null';
            const initials = $('body').data('user-initials') || '';

            container.html(`
                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                        style="width: 30px; height: 30px; font-size: 12px; font-weight: bold;">
                        ${initials}
                    </div>
                    <form class="comment-form flex-grow-1" data-blog-id="${blogId}" data-parent-id="${commentId}">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control comment-input" placeholder="Répondre..." required>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            `).show();
        }
    }

    handleShowReplies(e) {
        const btn = $(e.target).closest('.show-replies-btn');
        const commentId = btn.data('comment-id');
        const container = btn.closest('.comment-item').find('.replies-container').first();
        const shown = btn.data('shown') === 'true';

        const repliesCount = btn.data('replies-count') || 0;

        if (shown) {
            container.hide();
            btn.html('<i class="fa fa-chevron-down"></i> ' + repliesCount + ' réponse(s)');
            btn.data('shown', 'false');
        } else {
            container.show();
            btn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
            btn.data('shown', 'true');
        }
    }

    handleLoadMoreReplies(e) {
        e.preventDefault();
        const btn = $(e.target).closest('.load-more-replies');
        const commentId = btn.data('comment-id');
        const offset = parseInt(btn.data('offset') || 0, 10);
        const level = parseInt(btn.data('level') || 1, 10);
        const blogId = $('meta[name="blog-id"]').attr('content');

        if (!blogId) return;

        const url = `/blogs/${blogId}/comments/${commentId}/replies/load-more`;

        $.ajax({
            url: url,
            method: 'GET',
            data: { offset: offset, level: level, limit: 2 },
            success: (response) => {
                if (response.success) {
                    const container = btn.closest('.comment-item').find('.replies-container').first();
                    container.append(response.html);
                    container.show();

                    const newOffset = offset + 2;
                    btn.data('offset', newOffset);

                    if (!response.hasMore) {
                        btn.remove();
                    }

                    this.updateShowRepliesButton(container, btn);
                }
            },
            error: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les réponses.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    handleDeleteComment(e) {
        const commentId = $(e.target).closest('.delete-comment-btn').data('comment-id');
        const isAdmin = $('body').data('is-admin') === true;

        Swal.fire({
            title: 'Supprimer ce commentaire?',
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
                this.performCommentDelete(commentId, result.value);
            }
        });
    }

    performCommentDelete(commentId, reason = null) {
        $.ajax({
            url: `/blogs/comments/${commentId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason
            },
            success: (response) => {
                if (response.success) {
                    const $commentItem = $(`.comment-item[data-comment-id="${commentId}"]`);
                    if ($commentItem.length) {
                        const parentId = $commentItem.data('parent-id');
                        $commentItem.fadeOut(200, function () { $(this).remove(); });

                        if (parentId) {
                            this.updateParentRepliesCount(parentId);
                        }
                    }
                }
            }
        });
    }

    insertComment(html, parentId) {
        if (!parentId) {
            // Top-level comment
            const commentsList = $('#comments-list');
            if (commentsList.length) {
                commentsList.prepend(html);
            }
        } else {
            // Reply
            const parentItem = $(`.comment-item[data-comment-id="${parentId}"]`);
            const repliesContainer = parentItem.find('.replies-container').first();

            if (repliesContainer.length) {
                repliesContainer.append(html);
                repliesContainer.show();
                this.updateParentRepliesCount(parentId);
            }
        }
    }

    updateParentRepliesCount(parentId) {
        const $parentItem = $(`.comment-item[data-comment-id="${parentId}"]`);
        if ($parentItem.length) {
            const $showBtn = $parentItem.find('.show-replies-btn').first();
            if ($showBtn.length) {
                const prev = parseInt($showBtn.data('replies-count') || 0, 10);
                const now = Math.max(0, prev - 1);
                $showBtn.data('replies-count', now);

                if ($showBtn.data('shown') === 'true') {
                    $showBtn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                } else {
                    $showBtn.html('<i class="fa fa-chevron-down"></i> ' + now + ' réponse(s)');
                }
            }
        }
    }

    updateShowRepliesButton(container, btn) {
        const totalReplies = container.find('.comment-item').length;
        const showBtn = btn.closest('.comment-item').find('.show-replies-btn').first();

        if (showBtn.length) {
            showBtn.data('replies-count', totalReplies);

            if (showBtn.data('shown') !== 'true') {
                showBtn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                showBtn.data('shown', 'true');
            }
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
    new CommentsManager();
});