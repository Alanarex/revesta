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
        const hiddenInput = inputId ? document.getElementById(inputId) : editorDiv.nextElementSibling?.matches('input[type="hidden"]') ? editorDiv.nextElementSibling : null;

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

        // Set initial content
        const initialContent = hiddenInput.value;
        if (initialContent && initialContent.trim() !== '') {
            quill.root.innerHTML = initialContent;
        }

        // Sync to hidden input on change
        quill.on('text-change', () => {
            hiddenInput.value = quill.root.innerHTML;
        });

        // Setup form validation
        const form = hiddenInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                hiddenInput.value = quill.root.innerHTML;
                const text = quill.getText().trim();

                // Clear previous error
                editorDiv.classList.remove('is-invalid');
                const existingError = editorDiv.nextElementSibling;
                if (existingError?.classList.contains('invalid-feedback')) {
                    existingError.remove();
                }

                // Validate if required
                if (hiddenInput.hasAttribute('required') && text.length === 0) {
                    editorDiv.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Ce champ est obligatoire.';
                    editorDiv.insertAdjacentElement('afterend', errorDiv);
                    e.preventDefault();
                    return false;
                }
            });
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
