/**
 * Global Helper Functions
 * Utility functions available globally via window.helpers
 */

/**
 * Shows a tooltip near the given element with the specified message.
 * @param {jQuery} element - The jQuery element to position the tooltip near.
 * @param {string} message - The message to display in the tooltip.
 * @param {boolean} isError - Whether this is an error tooltip (red background).
 * @returns {jQuery} The tooltip element for manual control.
 */
export function showTooltip(element, message, isError = false) {
    // Remove any existing tooltips before creating a new one
    $('.global-tooltip').remove();

    const tooltip = $('<div class="global-tooltip">').text(message).css({
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

    const rect = element[0].getBoundingClientRect();
    const tooltipWidth = tooltip.outerWidth();
    const tooltipHeight = tooltip.outerHeight();
    const scrollTop = $(window).scrollTop();
    const scrollLeft = $(window).scrollLeft();

    tooltip.css({
        left: (rect.left + scrollLeft + (rect.width / 2) - (tooltipWidth / 2)) + 'px',
        top: (rect.top + scrollTop - tooltipHeight - 8) + 'px'
    });

    setTimeout(() => tooltip.css('opacity', '1'), 10);

    // Auto-hide after 1.5 seconds if not manually hidden
    setTimeout(() => {
        hideTooltip(tooltip);
    }, 1500);

    return tooltip;
}

/**
 * Hides and removes the given tooltip element.
 * @param {jQuery} tooltip - The tooltip element to hide and remove.
 */
export function hideTooltip(tooltip) {
    if (tooltip && tooltip.length) {
        tooltip.css('opacity', '0');
        setTimeout(() => tooltip.remove(), 300);
    }
}

/**
 * Shows a delete confirmation SweetAlert dialog
 * @param {string} title - The title of the confirmation dialog
 * @param {string} text - The message text
 * @returns {Promise} A promise that resolves with the user's choice
 */
export function confirmDelete(title = 'Êtes-vous sûr?', text = 'Cette action ne peut pas être annulée.') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    });
}

/**
 * Checks if a jQuery element is disabled
 * @param {jQuery} jqEl - The jQuery element to check
 * @returns {boolean} True if the element is disabled
 */
export function isElementDisabled(jqEl) {
    if (!jqEl || jqEl.length === 0) return false;
    const attrDisabled = typeof jqEl.attr('disabled') !== 'undefined' && jqEl.attr('disabled') !== false;
    const ariaDisabled = jqEl.attr('aria-disabled') === 'true';
    const dataDisabled = jqEl.data('disabled') === true || jqEl.data('disabled') === 'true';
    return attrDisabled || ariaDisabled || dataDisabled;
}

// Make functions globally available when imported
if (typeof window !== 'undefined') {
    window.helpers = {
        showTooltip,
        hideTooltip,
        confirmDelete,
        isElementDisabled
    };
}