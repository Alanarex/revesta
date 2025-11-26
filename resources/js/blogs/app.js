import Swal from 'sweetalert2';

// Auth Alert - Guest users trying to perform actions
$(document).on('click', '[data-auth-required]', function (e) {
    e.preventDefault();
    e.stopPropagation();

    Swal.fire({
        title: 'Connexion requise',
        text: 'Vous devez être connecté pour effectuer cette action.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Se connecter',
        cancelButtonText: 'Créer un compte'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/login';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = '/register';
        }
    });
});

// Bookmark Toggle - Only for authenticated users
$(document).on('click', '.bookmark-btn:not([data-auth-required])', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const btn = $(this);
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
        success: function (response) {
            if (response.success) {
                // Toggle the state
                const newState = !currentState;
                btn.data('bookmarked', newState);

                // Update icon: solid (fas) when bookmarked, outline (far) when not
                if (newState) {
                    icon.removeClass('far').addClass('fas');
                } else {
                    icon.removeClass('fas').addClass('far');
                }

                // Show tooltip message
                const message = newState ? 'Signet ajouté' : 'Signet supprimé';
                showTooltipMessage(btn, message);
            } else {
                showTooltipMessage(btn, 'Erreur: ' + (response.message || 'Action échouée'), true);
            }
            btn.prop('disabled', false);
        },
        error: function (xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Une erreur est survenue';
            showTooltipMessage(btn, errorMsg, true);
            btn.prop('disabled', false);
        }
    });
});

// Helper function to show tooltip message near button
function showTooltipMessage(btn, message, isError = false) {
    // Remove any existing tooltips first
    $('.bookmark-tooltip').remove();
    
    const tooltip = $('<div class="bookmark-tooltip">').text(message).css({
        position: 'absolute',
        padding: '8px 12px',
        'border-radius': '4px',
        'font-size': '0.9rem',
        'z-index': 9999,
        'pointer-events': 'none',
        color: '#fff',
        background: isError ? '#dc3545' : '#28a745',
        opacity: 0,
        transition: 'opacity 0.3s ease',
        'white-space': 'nowrap'
    });

    $('body').append(tooltip);

    // Position tooltip above the button using viewport coordinates
    const rect = btn[0].getBoundingClientRect();
    const tooltipWidth = tooltip.outerWidth();
    const tooltipHeight = tooltip.outerHeight();
    
    // Calculate position relative to viewport, then add scroll offset for absolute positioning
    const scrollTop = $(window).scrollTop();
    const scrollLeft = $(window).scrollLeft();
    
    tooltip.css({
        left: (rect.left + scrollLeft + (rect.width / 2) - (tooltipWidth / 2)) + 'px',
        top: (rect.top + scrollTop - tooltipHeight - 8) + 'px'
    });

    // Fade in
    setTimeout(() => {
        tooltip.css('opacity', '1');
    }, 10);

    // Fade out and remove
    setTimeout(() => {
        tooltip.css('opacity', '0');
        setTimeout(() => tooltip.remove(), 300);
    }, 1500);
}

// Like Toggle
$(document).on('click', '.like-btn', function () {
    const btn = $(this);
    const likeableId = btn.data('likeable-id');
    const likeableType = btn.data('likeable-type');

    $.ajax({
        url: '/blogs/likes/toggle',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            likeable_id: likeableId,
            likeable_type: likeableType
        },
        success: function (response) {
            if (response.success) {
                const icon = btn.find('i');
                const count = btn.find('.likes-count');

                if (response.liked) {
                    // switch to solid heart (FontAwesome 5)
                    icon.removeClass('far').addClass('fas');
                    btn.data('liked', 'true');
                } else {
                    // switch to regular (outline) heart
                    icon.removeClass('fas').addClass('far');
                    btn.data('liked', 'false');
                }

                count.text(response.likes_count);
                // Also update any other like buttons for the same likeable (minimal ops)
                try {
                    // Copy clicked icon HTML and count text, then apply to other matching buttons.
                    const iconHtml = icon.prop('outerHTML');
                    const likesText = response.likes_count;

                    // Match by likeable id, optionally by type if present on elements.
                    let selector = `.like-btn[data-likeable-id="${likeableId}"]`;
                    // select other buttons that may also have data-likeable-type attribute
                    selector += `[data-likeable-type]`;

                    // First try strict match with type; if none found, fall back to id-only
                    let $candidates = $(`${selector}`).not(btn);
                    if ($candidates.length === 0) {
                        $candidates = $(`.like-btn[data-likeable-id="${likeableId}"]`).not(btn);
                    }

                    $candidates.each(function () {
                        const $other = $(this);
                        const $otherIcon = $other.find('i').first();
                        const $otherCount = $other.find('.likes-count').first();

                        if ($otherIcon.length) {
                            $otherIcon.replaceWith(iconHtml);
                        } else {
                            $other.prepend(iconHtml);
                        }

                        if ($otherCount.length) {
                            $otherCount.text(likesText);
                        }
                    });
                } catch (e) {
                    // Failed to sync like buttons - non-fatal, swallow in production
                }
            }
        }
    });
});

// Copy Link
$(document).on('click', '.copy-link', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const link = $(this);
    const url = link.data('url');

    if (!url) {
        showTooltipMessage(link, 'URL introuvable', true);
        return;
    }

    // Use modern Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url)
            .then(() => {
                // Find the share button (dropdown toggle) to position tooltip there
                const shareBtn = link.closest('.dropdown').find('[data-bs-toggle="dropdown"]').first();
                const targetBtn = shareBtn.length ? shareBtn : link;
                showTooltipMessage(targetBtn, 'Lien copié!');
            })
            .catch(() => {
                // Fallback to old method
                copyLinkFallback(url, link);
            });
    } else {
        // Browser doesn't support Clipboard API
        copyLinkFallback(url, link);
    }
});

// Fallback for older browsers
function copyLinkFallback(url, link) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';
    textarea.style.top = '-9999px';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        const shareBtn = link.closest('.dropdown').find('[data-bs-toggle="dropdown"]').first();
        const targetBtn = shareBtn.length ? shareBtn : link;
        showTooltipMessage(targetBtn, 'Lien copié!');
    } catch (err) {
        showTooltipMessage(link, 'Erreur lors de la copie', true);
    } finally {
        document.body.removeChild(textarea);
    }
}

// NOTE: tooltip on hover removed. Tooltip now appears only after actions.

// Delete Blog
$(document).on('click', '.delete-blog-btn', function () {
    const blogId = $(this).data('blog-id');
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
            $.ajax({
                url: `/blogs/${blogId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: result.value || null
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000
                        }).then(() => {
                            window.location.href = '/blogs';
                        });
                    }
                }
            });
        }
    });
});

// Comment Form Submit
$(document).on('submit', '.comment-form', function (e) {
    e.preventDefault();
    const form = $(this);
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
        success: function (response) {
            if (response.success) {
                input.val('');

                // Insert the returned HTML without reloading. If parent_id is null,
                // prepend to the comments list; otherwise append into the parent's
                // replies container. We keep the same markup by rendering the
                // partial on the server and returning it as `response.html`.
                const html = response.html || '';

                if (!parentId) {
                    // Top-level comment -> insert at top of comments list
                    const commentsList = $('#comments-list');
                    if (commentsList.length) {
                        commentsList.prepend(html);
                    }
                } else {
                    // Reply -> find the parent replies container and append
                    const parentItem = $(`.comment-item[data-comment-id="${parentId}"]`);
                    const repliesContainer = parentItem.find('.replies-container').first();

                    if (repliesContainer.length) {
                        repliesContainer.append(html);
                        repliesContainer.show();
                    }

                    // Update the show-replies button count and label if present
                    const showBtn = parentItem.find('.show-replies-btn').first();
                    if (showBtn.length) {
                        const prev = parseInt(showBtn.data('replies-count') || 0, 10);
                        const now = prev + 1;
                        showBtn.data('replies-count', now);

                        if (showBtn.data('shown') === 'true') {
                            showBtn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                        } else {
                            showBtn.html('<i class="fa fa-chevron-down"></i> ' + now + ' réponse(s)');
                        }
                    }

                    // If a reply form was used, remove/hide it
                    const replyFormContainer = parentItem.find('.reply-form-container').first();
                    if (replyFormContainer.length) {
                        replyFormContainer.hide().empty();
                    }
                }

                // No inline success message: comment HTML was inserted into the DOM.
            }
        },
        error: function (xhr) {
            // Show inline error message near the form instead of modal
            const errorText = xhr.responseJSON?.message || 'Impossible d\'ajouter le commentaire.';
            const feedback = $('<div class="small text-danger mt-2 comment-feedback">').text(errorText);
            form.append(feedback);
            setTimeout(() => feedback.fadeOut(300, () => feedback.remove()), 3000);
        }
    });
});

// Reply Button
$(document).on('click', '.reply-btn', function () {
    const commentId = $(this).data('comment-id');
    const container = $(this).closest('.comment-item').find('.reply-form-container').first();

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
});

// Show Replies
$(document).on('click', '.show-replies-btn', function () {
    const btn = $(this);
    const commentId = btn.data('comment-id');
    const container = $(this).closest('.comment-item').find('.replies-container').first();
    const shown = btn.data('shown') === 'true';

    // Ensure we keep a numeric replies count on the button so we can restore the
    // original label when hiding replies. If the label currently contains a
    // number (e.g. "Voir 3 réponses"), store it; otherwise fall back to any
    // previously stored value or 0.
    const currentTextMatch = btn.text().match(/\d+/);
    if (currentTextMatch && !btn.data('replies-count')) {
        btn.data('replies-count', parseInt(currentTextMatch[0], 10));
    }

    if (shown) {
        container.hide();

        // Use stored replies-count when possible; fall back to 0 if missing.
        const repliesCount = (typeof btn.data('replies-count') !== 'undefined')
            ? btn.data('replies-count')
            : (currentTextMatch ? parseInt(currentTextMatch[0], 10) : 0);

        btn.html('<i class="fa fa-chevron-down"></i> ' + repliesCount + ' réponse(s)');
        btn.data('shown', 'false');
    } else {
        // When showing, ensure the stored count is up-to-date (if none yet, try to parse)
        if (!btn.data('replies-count')) {
            btn.data('replies-count', currentTextMatch ? parseInt(currentTextMatch[0], 10) : 0);
        }

        container.show();
        btn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
        btn.data('shown', 'true');
    }
});

// Load more replies (works for guests and authenticated users)
$(document).on('click', '.load-more-replies', function (e) {
    e.preventDefault();
    const btn = $(this);
    const commentId = btn.data('comment-id');
    const offset = parseInt(btn.data('offset') || 0, 10);
    const level = parseInt(btn.data('level') || 1, 10);
    const blogId = $('meta[name="blog-id"]').attr('content');

    if (!blogId) {
        // Blog ID missing in meta tag; abort loading replies silently.
        return;
    }

    const url = `/blogs/${blogId}/comments/${commentId}/replies/load-more`;

    $.ajax({
        url: url,
        method: 'GET',
        data: {
            offset: offset,
            level: level,
            limit: 2
        },
        success: function (response) {
            if (response.success) {
                const container = btn.closest('.comment-item').find('.replies-container').first();
                // Append the returned HTML
                container.append(response.html);
                container.show();

                // Update offset for next batch
                const newOffset = offset + 2;
                btn.data('offset', newOffset);

                if (!response.hasMore) {
                    btn.remove();
                }

                // Ensure the show-replies button reflects visible state
                const showBtn = btn.closest('.comment-item').find('.show-replies-btn').first();

                // Recompute total replies inside the container and store it on the
                // show button so the hide action can restore the exact number.
                const totalReplies = container.find('.comment-item').length;
                if (showBtn.length) {
                    showBtn.data('replies-count', totalReplies);

                    if (showBtn.data('shown') !== 'true') {
                        showBtn.html('<i class="fa fa-chevron-up"></i> Masquer les réponses');
                        showBtn.data('shown', 'true');
                    }
                }
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger les réponses.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});

// Delete Comment
$(document).on('click', '.delete-comment-btn', function () {
    const commentId = $(this).data('comment-id');
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
            $.ajax({
                url: `/blogs/comments/${commentId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: result.value || null
                },
                success: function (response) {
                    if (response.success) {
                        // Remove the comment node from the DOM (no full reload).
                        const $commentItem = $(`.comment-item[data-comment-id="${commentId}"]`);

                        if ($commentItem.length) {
                            // capture parent id if present so we can decrement parent's replies count
                            const parentId = $commentItem.data('parent-id') || $commentItem.data('parent') || $commentItem.attr('data-parent-id') || null;

                            $commentItem.fadeOut(200, function () { $(this).remove(); });

                            // If this was a reply, decrement the parent's show-replies counter (if present)
                            if (parentId) {
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
                        }

                        // Optionally show a small transient tooltip near the delete button's container
                        try {
                            const $anchor = $(this).closest('.dropdown').find('[data-bs-toggle="dropdown"]').first();
                            showCopyTooltip($anchor.length ? $anchor : $(this), response.message || 'Commentaire supprimé');
                        } catch (e) {
                            // ignore tooltip errors
                        }
                    }
                }
            });
        }
    });
});