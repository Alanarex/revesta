/**
 * Newsletter Utilities
 * Common helper functions for newsletter JavaScript modules
 */

/**
 * Format a date string to French locale
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date string
 */
export const formatDate = (dateString) => {
    if (!dateString) return '-';
    try {
        return new Date(dateString).toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return '-';
    }
};

/**
 * Get CSRF token from meta tag
 * @returns {string} CSRF token
 */
export const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
};

/**
 * Handle authentication required elements
 * Any element with data-auth-required attribute will show a login prompt
 */
export function initAuthRequiredHandler() {
    $(document).on('click', '[data-auth-required]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $element = $(this);
        const message = $element.data('auth-message') || 'Vous devez être connecté pour effectuer cette action.';
        const loginUrl = $element.data('login-url') || '/auth/login';

        Swal.fire({
            title: 'Connexion requise',
            text: message,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Se connecter',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to login page with return URL
                const currentUrl = encodeURIComponent(window.location.href);
                window.location.href = `${loginUrl}?redirect=${currentUrl}`;
            }
        });

        return false;
    });
}
