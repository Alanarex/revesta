@props([
    'label' => '',
    'name' => 'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'maxlength' => 255,
    'autocomplete' => false,
    'icon' => null,
    'muted' => '',
    'readonly' => false,
])

<div class="mb-4">
    @if ($label)
        <x-form.label :for="$name" :label="$label" :required="$required" />
    @endif

    <div class="input-group">
        @if ($icon)
            <span class="input-group-text"><i class="fa {{ $icon }}"></i></span>
        @endif

        <input type="text" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}" maxlength="{{ $maxlength }}"
            autocomplete="{{ $autocomplete ? 'on' : 'off' }}" {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }} {{ $attributes->merge(['class' => 'form-control']) }}>

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($muted)
        <small class="text-muted">{{ $muted }}</small>
    @endif

</div>

@pushOnce('styles')
    @vite('resources/scss/components/input.scss')
@endPushOnce
