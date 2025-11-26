import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Swal from 'sweetalert2';
import $ from 'jquery';

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.getElementById('editor');
    const form = document.getElementById('blogForm');
    if (!editorEl || !form) return;

    // initialize Quill
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    // If initial content provided via data-content, set it
    const initial = editorEl.dataset.content || '';
    if (initial) {
        try {
            quill.root.innerHTML = initial;
        } catch (e) {
            // ignore
        }
    }

    // Track which button was clicked
    let clickedButton = null;
    const submitButtons = form.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            clickedButton = this;
        });
    });

    // Handle form submission via AJAX
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent normal form submission

        const contentInput = document.getElementById('content');
        if (contentInput) {
            contentInput.value = quill.root.innerHTML;
        }

        // Get form data
        const formData = new FormData(form);
        
        // Add the clicked button's value (status)
        if (clickedButton && clickedButton.name) {
            formData.append(clickedButton.name, clickedButton.value);
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            Swal.fire('Erreur', 'Token CSRF manquant', 'error');
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Enregistrement en cours...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit via AJAX
        $.ajax({
            url: form.action,
            method: form.method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function (response) {
                Swal.fire({
                    title: 'Succès!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to the specified URL
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
            },
            error: function (xhr) {
                let errorMessage = 'Une erreur est survenue';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
