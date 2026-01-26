import Swal from 'sweetalert2';
import { initAuthRequiredHandler } from '../utils/auth-handler';
import * as bootstrap from 'bootstrap';

// Initialize authentication required handler
initAuthRequiredHandler();

$(function () {
    // Toggle password visibility
    $('.toggle-visibility').on('click', function () {
        const $target = $($(this).data('target'));
        const $icon = $(this).find('i');
        const isPassword = $target.attr('type') === 'password';

        $target.attr('type', isPassword ? 'text' : 'password');
        $icon.toggleClass('fa-eye fa-eye-slash');
    });

    // Button loading state
    $('.btn-action').on('click', function () {
        const $btn = $(this);
        const $form = $btn.closest('form');

        setTimeout(() => {
            if ($form.valid()) {
                const loadingText = $btn.data('loading-text') || '...';
                const spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';

                $btn.attr('disabled', true)
                    .addClass('is-loading')
                    .data('original-html', $btn.html())
                    .html(spinner + loadingText);
            }
        }, 0);
    });

    // Custom Password Strength Meter (CSP-compliant)
    function calculatePasswordStrength(password) {
        if (!password) {
            return { verdict: '', percentage: 0, color: '#e9ecef' };
        }

        let strength = 0;

        // Length checks
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;

        // Character variety checks
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/\d/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;

        // Calculate verdict and color
        const verdicts = ['Weak', 'Weak', 'Fair', 'Fair', 'Good', 'Strong', 'Very Strong'];
        const colors = ['#dc3545', '#dc3545', '#ffc107', '#ffc107', '#17a2b8', '#28a745', '#155724'];
        const percentages = [20, 20, 40, 40, 60, 80, 100];

        return {
            verdict: verdicts[strength],
            percentage: percentages[strength],
            color: colors[strength]
        };
    }

    // Password strength indicator
    $('#new_password').on('input', function () {
        const password = $(this).val();
        const result = calculatePasswordStrength(password);
        const $container = $('.password-strength');
        const $progressBar = $container.find('.progress-bar');
        const $verdict = $container.find('.password-verdict');

        $progressBar.css({
            width: result.percentage + '%',
            backgroundColor: result.color
        }).attr('aria-valuenow', result.percentage);

        $verdict.text(result.verdict).css('color', result.color);
    });

    // Form validation with Bootstrap styling
    $.validator.setDefaults({
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            error.addClass('d-block');
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // Custom phone validation method
    $.validator.addMethod('phoneNumber', function (value, element) {
        return this.optional(element) || /^[\d\s+()]+$/.test(value);
    }, 'Please enter a valid phone number (only numbers, spaces, +, and parentheses allowed).');

    $('#profileForm').validate({
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            email: { required: true, email: true },
            phone: { phoneNumber: true }
        },
        messages: {
            first_name: 'Please enter your first name.',
            last_name: 'Please enter your last name.',
            email: {
                required: 'Please enter your email address.',
                email: 'Please enter a valid email address.'
            }
        }
    });

    $('#passwordForm').validate({
        rules: {
            current_password: { required: true },
            new_password: { required: true, minlength: 8 },
            new_password_confirmation: { required: true, equalTo: '#new_password' }
        },
        messages: {
            current_password: 'Please enter your current password.',
            new_password: {
                required: 'Please enter a new password.',
                minlength: 'Please enter at least 8 characters.'
            },
            new_password_confirmation: {
                required: 'Please confirm your new password.',
                equalTo: 'Passwords do not match.'
            }
        }
    });

    // Delete account with SweetAlert2
    $('#deleteAccountBtn').on('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Delete Account',
            text: 'Are you sure you want to delete your account? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete my account',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Enter Your Password',
                    text: 'Please confirm your password to delete your account',
                    input: 'password',
                    inputPlaceholder: 'Enter your password',
                    inputAttributes: {
                        autocomplete: 'current-password',
                        autocorrect: 'off',
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete Account',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: (password) => {
                        if (!password) {
                            Swal.showValidationMessage('Password is required');
                            return false;
                        }
                        return password;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((passwordResult) => {
                    if (passwordResult.isConfirmed) {
                        const $form = $('#deleteAccountForm');
                        $form.find('input[name="password"]').val(passwordResult.value);
                        $form.submit();
                    }
                });
            }
        });
    });

    // Handle Bootstrap tabs with URL parameter support
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam) {
        // Get the tab button to activate
        const tabButton = document.querySelector(`#tab-${tabParam}`);
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }

    // Handle tab switching and update URL
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            const tabId = this.id.replace('tab-', '');
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', newUrl);
        });
    });
});
