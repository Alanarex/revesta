import Swal from 'sweetalert2';

function approveBlog(blogId) {
    Swal.fire({
        title: 'Approuver ce blog?',
        text: 'Le blog sera publié et visible par tous.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, approuver',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/blogs/${blogId}/approve`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000
                        }).then(() => {
                            // Redirect to admin blogs index. Prefer a meta tag if present so this file
                            // can remain a plain JS asset (not processed by Blade).
                            const redirect = document.querySelector('meta[name="admin-blogs-index"]')?.content || '/admin/blogs';
                            window.location.href = redirect;
                        });
                    }
                }
            });
        }
    });
}

function rejectBlog(blogId) {
    Swal.fire({
        title: 'Refuser ce blog?',
        text: 'L\'auteur sera notifié.',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Raison du refus (optionnel)',
        showCancelButton: true,
        confirmButtonText: 'Oui, refuser',
        confirmButtonColor: '#d33',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/blogs/${blogId}/reject`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: result.value || null
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000
                        }).then(() => {
                            const redirect = document.querySelector('meta[name="admin-blogs-index"]')?.content || '/admin/blogs';
                            window.location.href = redirect;
                        });
                    }
                }
            });
        }
    });
}

// Expose functions globally so inline onclick handlers in Blade continue to work
window.approveBlog = approveBlog;
window.rejectBlog = rejectBlog;