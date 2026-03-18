@props([
    'label' => '',
    'name' => 'password',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => false,
    'icon' => null,
    'confirmTarget' => null,
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

        <input type="password" id="{{ $name }}" name="{{ $name }}" value=""
            placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete ? 'on' : 'off' }}"
            {{ $required ? 'required' : '' }} {{ $readonly ? 'readonly' : '' }}
            {{ $confirmTarget ? "data-confirm-target=\"$confirmTarget\"" : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}>

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($muted)
        <small class="text-muted d-block mt-1">{{ $muted }}</small>
    @endif
</div>

@pushOnce('styles')
    @vite('resources/scss/components/input.scss')
@endPushOnce
