import 'pwstrength-bootstrap/dist/pwstrength-bootstrap';

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

    // Password strength meter
    $('#new_password').pwstrength({
        ui: {
            container: '.password-strength',
            showVerdictsInsideProgressBar: false,
            viewports: { progress: '.strength-bar' },
            verdicts: ['Weak', 'Normal', 'Medium', 'Strong', 'Very Strong']
        },
        common: {
            minChar: 8,
            usernameField: '#email'
        }
    });

    // Form validation
    $('#profileForm').validate({
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            email: { required: true, email: true }
        }
    });

    $('#passwordForm').validate({
        rules: {
            current_password: { required: true },
            new_password: { required: true, minlength: 8 },
            new_password_confirmation: { required: true, equalTo: '#new_password' }
        }
    });
});