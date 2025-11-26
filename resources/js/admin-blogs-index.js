import $ from 'jquery';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

$(document).ready(function() {
    const bulkActionsBar = $('#bulkActionsBar');
    const selectedCountSpan = $('#selectedCount');
    const totalBlogsCountSpan = $('#totalBlogsCount');
    const checkboxes = $('.blog-checkbox');
    const bulkApproveBtn = $('#bulkApproveBtn');
    const bulkRejectBtn = $('#bulkRejectBtn');
    const clearSelectionBtn = $('#clearSelectionBtn');
    const selectAllVisibleBtn = $('#selectAllVisibleBtn');
    const selectAllBlogsLink = $('#selectAllBlogsLink');
    
    // Modals
    const bulkRejectModalElement = document.getElementById('bulkRejectModal');
    const bulkRejectModal = bulkRejectModalElement ? new Modal(bulkRejectModalElement) : null;
    const confirmBulkRejectBtn = $('#confirmBulkRejectBtn');
    const bulkRejectReason = $('#bulkRejectReason');
    
    const singleRejectModalElement = document.getElementById('singleRejectModal');
    const singleRejectModal = singleRejectModalElement ? new Modal(singleRejectModalElement) : null;
    const confirmSingleRejectBtn = $('#confirmSingleRejectBtn');
    const singleRejectReason = $('#singleRejectReason');
    
    // Track current blog for single actions
    let currentBlogId = null;

    // Update the bulk actions bar visibility based on selection
    function updateBulkActionsBar() {
        const selectedCheckboxes = checkboxes.filter(':checked');
        const count = selectedCheckboxes.length;
        
        if (count > 0) {
            bulkActionsBar.show();
            selectedCountSpan.text(count);
            
            // Show "select all" link if not all visible are selected
            const visibleCount = checkboxes.length;
            if (count < visibleCount || count === visibleCount) {
                totalBlogsCountSpan.show();
            }
        } else {
            bulkActionsBar.hide();
            totalBlogsCountSpan.hide();
        }
    }

    // Handle checkbox change
    checkboxes.on('change', function() {
        updateBulkActionsBar();
    });

    // Select all visible blogs on current page
    selectAllVisibleBtn.on('click', function() {
        checkboxes.prop('checked', true);
        updateBulkActionsBar();
    });

    // Select ALL blogs (including those on other pages)
    selectAllBlogsLink.on('click', function(e) {
        e.preventDefault();
        
        const urlParams = new URLSearchParams(window.location.search);
        const search = urlParams.get('search') || '';
        const author = urlParams.get('author') || '';
        
        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération de tous les blogs...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '/admin/blogs/all-ids',
            method: 'GET',
            data: {
                search: search,
                author: author
            },
            success: function(response) {
                Swal.close();
                
                // First, select all visible ones
                checkboxes.prop('checked', true);
                
                // Then add hidden IDs to a data attribute for later use
                const allIds = response.ids;
                bulkActionsBar.data('all-ids', allIds);
                
                selectedCountSpan.text(allIds.length);
                totalBlogsCountSpan.hide(); // Hide the link once all are selected
                
                Swal.fire({
                    title: 'Sélection complète',
                    text: `${allIds.length} blog(s) sélectionné(s) au total`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Erreur', 'Impossible de récupérer tous les blogs', 'error');
            }
        });
    });

    // Clear all selections
    clearSelectionBtn.on('click', function() {
        checkboxes.prop('checked', false);
        bulkActionsBar.removeData('all-ids'); // Clear stored IDs
        updateBulkActionsBar();
    });

    // Get selected blog IDs (including hidden ones if "select all" was used)
    function getSelectedBlogIds() {
        const allIds = bulkActionsBar.data('all-ids');
        
        if (allIds && allIds.length > 0) {
            // Return all IDs if "select all" was clicked
            return allIds;
        }
        
        // Otherwise return only visible checked IDs
        return checkboxes.filter(':checked').map(function() {
            return parseInt($(this).val());
        }).get();
    }

    // Handle bulk approve
    bulkApproveBtn.on('click', function() {
        const selectedIds = getSelectedBlogIds();
        
        if (selectedIds.length === 0) {
            return;
        }

        Swal.fire({
            title: 'Approuver les blogs sélectionnés?',
            text: `Vous êtes sur le point d'approuver ${selectedIds.length} blog(s).`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, approuver',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction('approve', selectedIds, null);
            }
        });
    });

    // Handle bulk reject button click
    bulkRejectBtn.on('click', function() {
        const selectedIds = getSelectedBlogIds();
        
        if (selectedIds.length === 0) {
            return;
        }

        if (bulkRejectModal) {
            bulkRejectModal.show();
        }
    });

    // Handle confirm bulk reject
    confirmBulkRejectBtn.on('click', function() {
        const selectedIds = getSelectedBlogIds();
        const reason = bulkRejectReason.val().trim();
        
        if (bulkRejectModal) {
            bulkRejectModal.hide();
        }
        performBulkAction('reject', selectedIds, reason);
        bulkRejectReason.val(''); // Clear the reason field
    });

    // Handle single blog approve
    $(document).on('click', '.approve-single-btn', function() {
        const blogId = $(this).data('blog-id');
        
        Swal.fire({
            title: 'Approuver ce blog?',
            text: 'Le blog sera publié immédiatement.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, approuver',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                performSingleAction('approve', blogId, null);
            }
        });
    });

    // Handle single blog reject
    $(document).on('click', '.reject-single-btn', function() {
        currentBlogId = $(this).data('blog-id');
        
        if (singleRejectModal) {
            singleRejectModal.show();
        }
    });

    // Handle confirm single reject
    confirmSingleRejectBtn.on('click', function() {
        const reason = singleRejectReason.val().trim();
        
        if (singleRejectModal) {
            singleRejectModal.hide();
        }
        
        if (currentBlogId) {
            performSingleAction('reject', currentBlogId, reason);
            singleRejectReason.val(''); // Clear the reason field
            currentBlogId = null;
        }
    });

    // Perform bulk action via AJAX
    function performBulkAction(action, blogIds, reason) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            Swal.fire('Erreur', 'Token CSRF manquant', 'error');
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Traitement en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const data = {
            action: action,
            blog_ids: blogIds
        };

        if (reason) {
            data.reason = reason;
        }

        $.ajax({
            url: '/admin/blogs/bulk-action',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Succès!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload the page to refresh the list
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Une erreur est survenue';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Perform single blog action via AJAX
    function performSingleAction(action, blogId, reason) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            Swal.fire('Erreur', 'Token CSRF manquant', 'error');
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Traitement en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const url = `/admin/blogs/${blogId}/${action}`;
        const data = {};

        if (reason) {
            data.reason = reason;
        }

        $.ajax({
            url: url,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Succès!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload the page to refresh the list
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Une erreur est survenue';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Initialize the bulk actions bar state
    updateBulkActionsBar();
});
