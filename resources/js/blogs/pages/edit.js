$(function () {
    $('.publish-draft-link').on('click', function (e) {
        e.preventDefault();
        const $link = $(this);
        const blogId = $link.data('blog-id');
        const publishUrl = $link.data('publish-url');
        const csrfToken = $link.data('csrf-token');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Confirmer la publication',
                text: 'Êtes-vous sûr de vouloir soumettre ce blog pour approbation ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, soumettre',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Publication en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: publishUrl,
                        method: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Redirect to blog page
                                    window.location.href = response.redirect || publishUrl.replace('/publish', '');
                                });
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = 'Une erreur est survenue.';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        } else {
            // Fallback to browser confirm if Swal is not available
            if (confirm('Êtes-vous sûr de vouloir soumettre ce blog pour approbation ?')) {
                $.ajax({
                    url: publishUrl,
                    method: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect || publishUrl.replace('/publish', '');
                        }
                    },
                    error: function (xhr) {
                        alert('Une erreur est survenue lors de la publication.');
                    }
                });
            }
        }
    });
});