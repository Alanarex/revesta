/**
 * Share Button Component
 * Handles copying blog links to clipboard
 */

import { showTooltip, isElementDisabled } from '../../helper.js';

export class ShareButton {
    constructor() {
        this.init();
    }

    init() {
        $(document).on('click', '.copy-link', (e) => {
            e.preventDefault();
            this.handleCopyLink(e);
        });
    }

    handleCopyLink(e) {
        const link = $(e.target).closest('.copy-link');
        if (isElementDisabled(link)) return;

        const url = link.data('url');
        if (!url) {
            showTooltip(link, 'URL introuvable', true);
            return;
        }

        // Use modern Clipboard API with fallback
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url)
                .then(() => {
                    const shareBtn = link.closest('.dropdown').find('[data-bs-toggle="dropdown"]').first();
                    const targetBtn = shareBtn.length ? shareBtn : link;
                    showTooltip(targetBtn, 'Lien copié!');
                })
                .catch(() => {
                    this.copyLinkFallback(url, link);
                });
        } else {
            this.copyLinkFallback(url, link);
        }
    }

    copyLinkFallback(url, link) {
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
            showTooltip(targetBtn, 'Lien copié!');
        } catch (err) {
            showTooltip(link, 'Erreur lors de la copie', true);
        } finally {
            document.body.removeChild(textarea);
        }
    }

    showTooltip(btn, message, isError = false) {
        return showTooltip(btn, message, isError);
    }


}

// Initialize when DOM is ready
$(function () {
    new ShareButton();
});