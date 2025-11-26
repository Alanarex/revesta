import $ from 'jquery';

$(document).on('click', '.bookmark-toggle-inline', function(e) {
    e.preventDefault();
    
    if (!confirm('Retirer ce signet ?')) return;
    
    const btn = $(this);
    const blogId = btn.data('blogId');
    const url = '/blogs/bookmarks/toggle'; // Fallback URL
    
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
                // Remove the parent anchor element (the entire dropdown item)
                btn.closest('a.dropdown-item').fadeOut(function() {
                    $(this).remove();
                });
                
                // Decrement badge
                const badge = $('#bookmarksBadge');
                if (badge.length) {
                    let n = parseInt(badge.text() || '0', 10) - 1;
                    if (n <= 0) {
                        badge.fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        badge.text(n);
                    }
                }
            } else {
                alert(data.message || 'Impossible de retirer le signet');
            }
        },
        error: function() {
            alert('Erreur réseau');
        }
    });
});
