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
        const loginUrl = $element.data('login-url') || '/login';

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