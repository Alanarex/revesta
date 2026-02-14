@props([
    'label' => '',
    'name' => 'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'rows' => 5,
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

        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->merge(['class' => 'form-control']) }}>{{ old($name, $value) }}</textarea>

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($muted)
        <small class="text-muted d-block mt-2">{{ $muted }}</small>
    @endif

</div>

@pushOnce('styles')
    @vite('resources/scss/components/input.scss')
@endPushOnce
