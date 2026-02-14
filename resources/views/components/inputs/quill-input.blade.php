@props([
    'label' => '',
    'name' => 'content',
    'value' => '',
    'required' => false,
    'muted' => '',
])

<div class="mb-4">
    @if ($label)
        <x-form.label :for="$name" :label="$label" :required="$required" />
    @endif

    <div id="quill-editor" class="quill-wrapper" data-input-id="{{ $name }}"></div>

    <input type="hidden" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        data-quill-input="true" {{ $required ? 'required' : '' }}>

    @if ($muted)
        <small class="text-muted">{{ $muted }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@pushOnce('styles')
    @vite('resources/scss/components/quill.scss')
@endPushOnce

@pushOnce('scripts')
    @vite('resources/js/components/quill.js')
@endPushOnce
