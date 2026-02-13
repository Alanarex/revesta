import $ from 'jquery';
import Swal from 'sweetalert2';

$(function () {
    const token = $('meta[name="csrf-token"]').attr('content');
    const resetRoute = $('meta[name="reset-route"]').attr('content');
    const destroyRoute = $('meta[name="destroy-route"]').attr('content');

    // Reset password for admin with SweetAlert
    $('#resetPwdBtn').on('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Réinitialiser le mot de passe',
            text: 'Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, réinitialiser',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: resetRoute,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    dataType: 'json'
                }).done(function (resp) {
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: resp.message || 'Mot de passe réinitialisé',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: resp.message || 'Action échouée'
                        });
                    }
                }).fail(function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur serveur'
                    });
                });
            }
        });
    });

    // Delete account with SweetAlert
    $('#deleteAccountBtn').on('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Supprimer le compte',
            text: 'Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (result.isConfirmed) {
                // Use POST with method spoofing for delete
                $.ajax({
                    url: destroyRoute,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    data: { _method: 'DELETE' },
                    dataType: 'json'
                }).done(function (resp) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Compte supprimé',
                        text: 'Le compte a été supprimé avec succès',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/admin/users';
                    });
                }).fail(function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur lors de la suppression'
                    });
                });
            }
        });
    });

    // Simulations carousel
    let currentSimulationIndex = 0;
    const simulationCards = $('.simulation-card');
    const simulationsPerView = 3;
    const totalSimulations = simulationCards.length;

    function updateSimulationsCarousel() {
        if (totalSimulations <= simulationsPerView) return;

        const cardWidth = simulationCards.first().outerWidth(true);
        const offset = -currentSimulationIndex * cardWidth;
        $('.simulations-carousel').css('transform', `translateX(${offset}px)`);
    }

    $('.simulations-next').on('click', function () {
        if (currentSimulationIndex < totalSimulations - simulationsPerView) {
            currentSimulationIndex++;
            updateSimulationsCarousel();
        }
    });

    $('.simulations-prev').on('click', function () {
        if (currentSimulationIndex > 0) {
            currentSimulationIndex--;
            updateSimulationsCarousel();
        }
    });

    // Simple blog filters
    const $filterButtons = $('#blog-filters .blog-filter-btn');
    const $blogItems = $('#blogs-container .user-blog-wrapper[data-tags]');
    const $blogsCount = $('#blogs-count');

    function applyBlogFilter(tag) {
        if (!$blogItems.length) return;

        if (tag === 'all') {
            $blogItems.removeClass('d-none');
        } else {
            $blogItems.each(function () {
                const rawTags = String($(this).data('tags') || '');
                const tags = rawTags.split(/\s+/).filter(Boolean);
                $(this).toggleClass('d-none', !tags.includes(tag));
            });
        }

        if ($blogsCount.length) {
            $blogsCount.text($blogItems.filter(':not(.d-none)').length);
        }
    }

    if ($filterButtons.length) {
        $filterButtons.on('click', function () {
            const $button = $(this);
            $filterButtons.removeClass('is-active');
            $button.addClass('is-active');
            applyBlogFilter($button.data('tag'));
        });

        applyBlogFilter('all');
    }
});
