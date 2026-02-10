@props([
    'label' => '',
    'name' => 'name',
    'value' => '',
    'placeholder' => 'Sélectionner',
    'required' => false,
    'options' => [],
    'icon' => null,
    'muted' => '',
])

<div class="mb-4">
    @if ($label)
        <x-form.label :for="$name" :label="$label" :required="$required" />
    @endif

    <div class="input-group">
        @if ($icon)
            <span class="input-group-text"><i class="{{ $icon }}"></i></span>
        @endif

        <select id="{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-select']) }}>
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>

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
