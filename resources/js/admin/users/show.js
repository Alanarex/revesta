import $ from 'jquery';
import Swal from 'sweetalert2';

$(function(){
    const token = $('meta[name="csrf-token"]').attr('content');
    const updateRoute = $('meta[name="update-route"]').attr('content');
    const toggleRoute = $('meta[name="toggle-route"]').attr('content');
    const resetRoute = $('meta[name="reset-route"]').attr('content');
    const destroyRoute = $('meta[name="destroy-route"]').attr('content');

    // Save information form
    $('#updateInfoForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        Swal.fire({
            title: 'Enregistrer les modifications',
            text: 'Voulez-vous sauvegarder ces informations ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                // Use POST with method spoofing to avoid browser restrictions
                const updateData = formData + '&_method=PUT';
                $.ajax({
                    url: updateRoute,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    data: updateData,
                    dataType: 'json'
                }).done(function(resp) {
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: resp.message || 'Informations mises à jour avec succès',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: resp.message || 'Erreur lors de la mise à jour'
                        });
                    }
                }).fail(function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Erreur serveur';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: errorMsg
                    });
                });
            }
        });
    });



    // Reset password for admin with SweetAlert
    $('#resetPasswordAdminBtn').on('click', function(e) {
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
                }).done(function(resp) {
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
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur serveur'
                    });
                });
            }
        });
    });

    // Change password form (own profile)
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        Swal.fire({
            title: 'Modifier le mot de passe',
            text: 'Voulez-vous modifier votre mot de passe ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, modifier',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: resetRoute,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    data: formData,
                    dataType: 'json'
                }).done(function(resp) {
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: resp.message || 'Mot de passe modifié avec succès',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#changePasswordForm')[0].reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: resp.message || 'Action échouée'
                        });
                    }
                }).fail(function(xhr) {
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
    $('#deleteAccountBtn').on('click', function(e) {
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
                }).done(function(resp) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Compte supprimé',
                        text: 'Le compte a été supprimé avec succès',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/admin/users';
                    });
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur lors de la suppression'
                    });
                });
            }
        });
    });

    $('#resetPwdBtn').on('click', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Réinitialiser le mot de passe',
            html: `
                <input id="swal-input1" type="password" class="swal2-input" placeholder="Nouveau mot de passe">
                <input id="swal-input2" type="password" class="swal2-input" placeholder="Confirmer le mot de passe">
            `,
            showCancelButton: true,
            confirmButtonText: 'Envoyer',
            preConfirm: () => {
                const p1 = $('#swal-input1').val() || '';
                const p2 = $('#swal-input2').val() || '';
                if (p1.length < 8) {
                    Swal.showValidationMessage('Le mot de passe doit contenir au moins 8 caractères');
                    return false;
                }
                if (p1 !== p2) {
                    Swal.showValidationMessage('Les mots de passe ne correspondent pas');
                    return false;
                }
                return { password: p1 };
            }
        }).then(result => {
            if(result.isConfirmed){
                const token = $('meta[name="csrf-token"]').attr('content');
                const url = `${window.location.pathname}/reset-password`;
                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    data: { password: result.value.password },
                    dataType: 'json'
                }).done(function(resp){
                    if(resp.success){
                        Swal.fire({icon: 'success', title: resp.message || 'Mot de passe mis à jour', timer: 1500, showConfirmButton: false});
                    } else {
                        Swal.fire({icon: 'error', title: 'Erreur', text: resp.message || 'Action échouée'});
                    }
                }).fail(function(xhr){
                    Swal.fire({icon: 'error', title: 'Erreur', text: xhr.responseJSON?.message || 'Erreur serveur'});
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

    $('.simulations-next').on('click', function() {
        if (currentSimulationIndex < totalSimulations - simulationsPerView) {
            currentSimulationIndex++;
            updateSimulationsCarousel();
        }
    });

    $('.simulations-prev').on('click', function() {
        if (currentSimulationIndex > 0) {
            currentSimulationIndex--;
            updateSimulationsCarousel();
        }
    });

    // Blog filtering using Bootstrap nav-pills
    const BLOGS_PER_PAGE = 4;
    let currentFilter = 'all';

    function filterBlogs(filter) {
        currentFilter = filter;
        const $allBlogs = $('.user-blog-wrapper');
        let matchedCount = 0;

        $allBlogs.each(function() {
            const $blog = $(this);
            const status = $blog.attr('data-blog-status');
            const bookmarked = $blog.attr('data-is-bookmarked') === 'true';
            
            let show = false;
            if (filter === 'all') {
                show = true;
            } else if (filter === 'bookmarked') {
                show = bookmarked;
            } else {
                show = (status === filter);
            }

            if (show) {
                $blog.removeClass('d-none');
                matchedCount++;
            } else {
                $blog.addClass('d-none');
            }
        });

        // Show only first 4, hide rest
        let visibleIndex = 0;
        $allBlogs.not('.d-none').each(function() {
            if (visibleIndex >= BLOGS_PER_PAGE) {
                $(this).addClass('collapsed-blog');
            } else {
                $(this).removeClass('collapsed-blog');
            }
            visibleIndex++;
        });

        // Update count badge
        $('#blogs-count').text(matchedCount);

        // Update expand button
        const hiddenCount = matchedCount - Math.min(BLOGS_PER_PAGE, matchedCount);
        if (hiddenCount > 0) {
            $('#showAllBlogsBtn')
                .text(`Voir tous les blogs (${hiddenCount} de plus)`)
                .removeClass('d-none');
        } else {
            $('#showAllBlogsBtn').addClass('d-none');
        }
    }

    // Bootstrap nav-pills click handler
    $('[data-bs-toggle="pill"]').on('click', function(e) {
        e.preventDefault();
        const filter = $(this).attr('data-filter');
        
        // Let Bootstrap handle active state
        $('[data-bs-toggle="pill"]').removeClass('active');
        $(this).addClass('active');
        
        filterBlogs(filter);
    });

    // Expand/collapse using Bootstrap collapse principles
    $(document).on('click', '#showAllBlogsBtn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $collapsedBlogs = $('.collapsed-blog');
        
        if ($collapsedBlogs.length > 0 && $collapsedBlogs.first().hasClass('collapsed-blog')) {
            // Expand
            $('.collapsed-blog').removeClass('collapsed-blog');
            $btn.text('Voir moins');
        } else {
            // Collapse
            let visibleIndex = 0;
            $('.user-blog-wrapper').not('.d-none').each(function() {
                if (visibleIndex >= BLOGS_PER_PAGE) {
                    $(this).addClass('collapsed-blog');
                }
                visibleIndex++;
            });
            const hiddenCount = $('.collapsed-blog').length;
            $btn.text(`Voir tous les blogs (${hiddenCount} de plus)`);
        }
    });

    // Add CSS class for collapsed blogs
    if ($('.user-blog-wrapper').length > 0) {
        // Add inline style for collapsed-blog class
        const style = document.createElement('style');
        style.textContent = '.collapsed-blog { display: none !important; }';
        document.head.appendChild(style);
        
        // Initialize
        filterBlogs('all');
    }

    // Activity expand/collapse
    $('.activity-expand-btn').on('click', function() {
        const hiddenItems = $('.activity-item:hidden');
        if (hiddenItems.length > 0) {
            $('.activity-item').show();
            $(this).text('Voir moins d\'activités');
        } else {
            $('.activity-item').each(function(index) {
                if (index >= 10) {
                    $(this).hide();
                }
            });
            const hiddenCount = $('.activity-item:hidden').length;
            $(this).text(`Voir plus d'activités (${hiddenCount} restantes)`);
        }
    });
});
