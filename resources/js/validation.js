/**
 * Global Form Validation
 * Minimal, production-ready validation system
 * - Native HTML5 validation
 * - French error messages
 * - Password confirmation support
 * - Bootstrap styling
 */

const MESSAGES = {
    valueMissing: 'Veuillez remplir ce champ.',
    typeMismatch: 'Veuillez entrer un format valide.',
    email: 'Veuillez entrer une adresse e-mail valide.',
    url: 'Veuillez entrer une URL valide.',
    tooShort: 'La saisie est trop courte.',
    tooLong: 'La saisie est trop longue.',
    patternMismatch: 'Le format est invalide.',
    stepMismatch: 'La valeur n\'est pas valide.',
    badInput: 'Entrée non valide.',
    passwordMismatch: 'Les valeurs ne correspondent pas.'
};

/**
 * Get custom error message from data attribute or use default
 */
function getErrorMessage(input, key) {
    const customMsg = input.getAttribute(`data-error-${key}`);
    return customMsg || MESSAGES[key] || MESSAGES.badInput;
}

/**
 * Resolve validation error message for input
 */
function resolveMessage(input) {
    const validity = input.validity;
  
    if (validity.valueMissing) {
        return getErrorMessage(input, 'valueMissing');
    }
    if (validity.typeMismatch) {
        const type = input.type.toLowerCase();
        return getErrorMessage(input, type) || getErrorMessage(input, 'typeMismatch');
    }
    if (validity.tooShort) return getErrorMessage(input, 'tooShort');
    if (validity.tooLong) return getErrorMessage(input, 'tooLong');
    if (validity.patternMismatch) return getErrorMessage(input, 'patternMismatch');
    if (validity.stepMismatch) return getErrorMessage(input, 'stepMismatch');
    if (validity.badInput) return getErrorMessage(input, 'badInput');
    
    return MESSAGES.badInput;
}

/**
 * Validate password confirmation
 */
function validatePasswordConfirm(input) {
    const confirmTarget = input.getAttribute('data-confirm-target');
    if (!confirmTarget) return true;
    
    const targetInput = document.querySelector(confirmTarget);
    if (!targetInput) return true;
    
    const match = input.value === targetInput.value;
    if (!match) {
        input.setCustomValidity(getErrorMessage(input, 'passwordMismatch'));
    } else {
        input.setCustomValidity('');
    }
    
    return match;
}

/**
 * Handle invalid event
 */
function onInvalid(e) {
    const input = e.target;
    if (!isValidatableInput(input)) return;
    
    if (input.hasAttribute('data-confirm-target')) {
        validatePasswordConfirm(input);
    } else {
        input.setCustomValidity(resolveMessage(input));
    }
}

/**
 * Handle input event - clear validity on change
 */
function onInput(e) {
    const input = e.target;
    if (!isValidatableInput(input)) return;
    
    if (input.validationMessage) {
        input.setCustomValidity('');
    }
    
    // Revalidate password confirmation on change
    if (input.hasAttribute('data-confirm-target')) {
        validatePasswordConfirm(input);
    }
}

/**
 * Check if element is a validatable input
 */
function isValidatableInput(el) {
    return el instanceof HTMLInputElement || 
           el instanceof HTMLTextAreaElement || 
           el instanceof HTMLSelectElement;
}

/**
 * Initialize validation
 */
function init() {
    // Use capture phase to catch validation events
    document.addEventListener('invalid', onInvalid, true);
    document.addEventListener('input', onInput, true);
    
    // Clear validity on focus for better UX
    document.addEventListener('focusin', (e) => {
        const input = e.target;
        if (isValidatableInput(input)) {
            input.setCustomValidity('');
        }
    }, true);
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

export default {};
