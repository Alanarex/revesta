/**
 * Quill Editor Component
 * Initializes Quill editors with automatic content syncing and validation
 */

import Quill from 'quill';

const DEFAULT_TOOLBAR = [
    [{ 'header': [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    [{ 'script': 'sub' }, { 'script': 'super' }],
    [{ 'indent': '-1' }, { 'indent': '+1' }],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'align': [] }],
    ['blockquote', 'code-block'],
    ['link', 'image'],
    ['clean']
];

const quillInstances = new Map();

/**
 * Validates Quill editor content
 * Adds is-invalid class and error message if required field is empty
 */
function validateQuillEditor({ quill, hiddenInput, editorDiv }) {
    if (!hiddenInput || !hiddenInput.hasAttribute('required')) {
        return true;
    }

    const text = quill.getText().replace(/\n/g, '').trim();
    const isEmpty = text.length === 0;
    const errorSelector = `.invalid-feedback[data-input-id="${hiddenInput.id}"]`;
    const existingError = editorDiv.parentElement.querySelector(errorSelector);

    if (isEmpty) {
        editorDiv.classList.add('is-invalid');

        const errorDiv = existingError || document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.setAttribute('data-input-id', hiddenInput.id);
        errorDiv.textContent = hiddenInput.getAttribute('data-error-value-missing') || 'Ce champ est obligatoire.';
        
        if (!existingError) {
            editorDiv.insertAdjacentElement('afterend', errorDiv);
        }

        return false;
    }

    editorDiv.classList.remove('is-invalid');
    if (existingError) {
        existingError.remove();
    }

    return true;
}

/**
 * Validates entire form (both Quill and standard inputs)
 * Returns true only if all validations pass
 */
function validateForm(form, quill, hiddenInput, editorDiv) {
    const quillIsValid = validateQuillEditor({ quill, hiddenInput, editorDiv });
    const formIsValid = form.checkValidity();
    
    if (!formIsValid || !quillIsValid) {
        form.reportValidity();
        return false;
    }
    
    return true;
}

/**
 * Sets up form submission listeners
 */
function setupFormValidation(form, quill, hiddenInput, editorDiv) {
    const handleSubmit = (e) => {
        hiddenInput.value = quill.root.innerHTML;
        
        if (!validateForm(form, quill, hiddenInput, editorDiv)) {
            e.preventDefault();
            e.stopPropagation();
        }
    };

    // Listen to submit event
    form.addEventListener('submit', handleSubmit);

    // Backup: listen to button clicks (in case submit event doesn't fire)
    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        button.addEventListener('click', handleSubmit);
    });
}

/**
 * Initializes Quill editor and links it to hidden input
 */
function initializeQuill(selector = '.quill-wrapper') {
    const editors = document.querySelectorAll(selector);
    
    if (editors.length === 0) {
        const legacyEditor = document.getElementById('quill-editor');
        if (legacyEditor && !legacyEditor.classList.contains('quill-wrapper')) {
            console.warn('Quill editor found but not properly wrapped');
        }
        return null;
    }

    editors.forEach((editorDiv) => {
        if (quillInstances.has(editorDiv)) {
            return; // Already initialized
        }

        const inputId = editorDiv.getAttribute('data-input-id');
        const hiddenInput = inputId 
            ? document.getElementById(inputId) 
            : editorDiv.nextElementSibling?.matches('input[type="hidden"]') 
                ? editorDiv.nextElementSibling 
                : null;

        if (!hiddenInput) {
            console.warn('No hidden input found for Quill editor');
            return;
        }

        // Initialize Quill
        const quill = new Quill(editorDiv, {
            theme: 'snow',
            placeholder: 'Écrivez votre contenu ici...',
            modules: { toolbar: DEFAULT_TOOLBAR }
        });

        // Load initial content
        if (hiddenInput.value?.trim()) {
            quill.root.innerHTML = hiddenInput.value;
        }

        // Sync HTML to hidden input on text changes
        quill.on('text-change', () => {
            hiddenInput.value = quill.root.innerHTML;
            validateQuillEditor({ quill, hiddenInput, editorDiv });
        });

        // Setup form validation
        const form = hiddenInput.closest('form');
        if (form) {
            setupFormValidation(form, quill, hiddenInput, editorDiv);
        }

        quillInstances.set(editorDiv, quill);
    });

    return quillInstances.size > 0 ? quillInstances.values().next().value : null;
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initializeQuill('.quill-wrapper');
});

// Export for manual use
export { initializeQuill, quillInstances };

