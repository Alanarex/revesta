/**
 * Blog Form Validation and Rich Text Editor
 * jQuery Validation Plugin + Quill Editor for blog forms
 */

import Quill from 'quill';

$(function () {

    // Only initialize if the blog form exists on the page
    if ($('#blogForm').length === 0) {
        return;
    }

    // Initialize Quill editor for content field
    let quill = null;
    const $contentTextarea = $('#content');
    const $editorDiv = $('#editor');

    if (typeof Quill !== 'undefined' && $contentTextarea.length > 0 && $editorDiv.length > 0) {
        // Create Quill toolbar options
        const toolbarOptions = [
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

        // Initialize Quill
        quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Écrivez votre article ici...',
            modules: {
                toolbar: toolbarOptions
            }
        });

        // Set initial content if exists
        const initialContent = $contentTextarea.val();
        if (initialContent && initialContent.trim() !== '') {
            quill.root.innerHTML = initialContent;
        }

        // Update textarea on Quill content change (for validation)
        quill.on('text-change', function () {
            $contentTextarea.val(quill.root.innerHTML);
        });
    }

    // Custom Quill content validation on submit
    $('#blogForm').on('submit', function () {
        if (!quill) return;
        $contentTextarea.val(quill.root.innerHTML);
        const quillEditor = $('#editor .ql-editor');
        const html = quillEditor.html();
        const text = quillEditor.text().trim();
        let isValid = true;
        // Remove previous error
        $('#editor').removeClass('is-invalid');
        $('#editor').nextAll('.invalid-feedback, .invalid-feedback.d-block').remove();
        if (text.length === 0 || html === '<p><br></p>') {
            // Show error
            $('#editor').addClass('is-invalid');
            $('#editor').after('<div class="invalid-feedback d-block">Le contenu est obligatoire.</div>');
            isValid = false;
        }
        if (!isValid) {
            // Prevent form submission
            return false;
        }
    });

    // Character counter for short description
    const $shortDescription = $('#short_description');
    const maxLength = 500;

    function updateCharCounter() {
        const currentLength = $shortDescription.val().length;
        const remaining = maxLength - currentLength;

        let $counter = $shortDescription.siblings('.text-muted').find('.char-counter');
        if ($counter.length === 0) {
            $shortDescription.siblings('.text-muted').append('<span class="char-counter ms-2"></span>');
            $counter = $shortDescription.siblings('.text-muted').find('.char-counter');
        }

        $counter.text(`${currentLength}/${maxLength}`);

        if (remaining < 0) {
            $counter.removeClass('text-warning').addClass('text-danger');
        } else if (remaining < 50) {
            $counter.removeClass('text-danger').addClass('text-warning');
        } else {
            $counter.removeClass('text-warning text-danger');
        }
    }

    // Initialize character counter
    updateCharCounter();
    $shortDescription.on('input', updateCharCounter);
});