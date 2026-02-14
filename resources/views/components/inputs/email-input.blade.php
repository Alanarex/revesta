@props([
    'label' => '',
    'name' => 'email',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => 'email',
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

        <input type="email" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}" 
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}>

        @if ($muted)
            <small class="text-muted">{{ $muted }}</small>
        @endif

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

@pushOnce('styles')
    @vite('resources/scss/components/input.scss')
@endPushOnce

