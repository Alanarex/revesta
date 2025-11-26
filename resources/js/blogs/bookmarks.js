import $ from 'jquery';

$(document).on('click', '.bookmark-toggle-btn', function(e) {
    e.preventDefault();
    
    const btn = $(this);
    const blogId = btn.data('blogId');
    
    if (!confirm('Supprimer ce signet ?')) return;
    
    // Get the route from the Blade template by checking for a data attribute
    // Fallback to hardcoded route if not found
    const url = btn.data('toggleUrl') || '/blogs/bookmarks/toggle';

    $.ajax({
        url: url,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        data: JSON.stringify({ blog_id: blogId }),
        dataType: 'json',
        success: function(data) {
            if (data && data.success) {
                // Remove the card visually
                btn.closest('.col-12').fadeOut(function() {
                    $(this).remove();
                });
            } else {
                alert(data.message || 'Impossible de retirer le signet');
            }
        },
        error: function() {
            alert('Erreur réseau');
        }
    });
});
